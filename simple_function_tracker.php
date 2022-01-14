<?php
require 'vendor/autoload.php';

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

class SimpleFunctionInfo
{
    public $func_name;
    public $func_start_line;
    public $func_end_line;

    public function __construct($_1, $_2, $_3)
    {
        $this->func_name = $_1;
        $this->func_start_line = $_2;
        $this->func_end_line = $_3;
    }

    public function __toString()
    {
        return sprintf("%s\t%s\t%s\n", $this->func_name, $this->func_start_line, $this->func_end_line);
    }
}

class SimpleFunctionTracker extends NodeVisitorAbstract
{
    /*
     * storage : [funcname] = func_body  content
     */
    public $storage = array();

    public function enterNode(Node $node)
    {
        if ($node instanceof Function_ or $node instanceof ClassMethod) {
            $this->storage[$node->name->name] = new SimpleFunctionInfo(
                $node->name->name,
                $node->getLine(),
                $node->getEndLine(),
            );
        }
    }
}

function main($file)
{

    $traverser = new NodeTraverser();
    $tracker = new SimpleFunctionTracker();
    $traverser->addVisitor($tracker);
    $code = file_get_contents($file);
    $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
    try {
        $ast = $parser->parse($code);
    } catch (Error $error) {
        echo "Parse error: {$error->getMessage()}\n";
        return;
    }

    $ast = $traverser->traverse($ast);
    return $tracker->storage;
}

$option = getopt("i:");

if (isset($option['i'])) {
    foreach (main($option['i']) as $k => $v) {
        echo($v);
    }
}

