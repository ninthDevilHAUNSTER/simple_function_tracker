# Simple Function Tracker 
> A tool that used in V0Finder for PHP-Extension

## Usage

```shell
php simple_function_tracker.php -i vunlfile.php
```

## Extend in V0Finder

### STEP1 Change Path
change ctagpath in `OSS_Collecor` and `CVEPatch_Collector`

```python
# change ctag path as this tool
ctagPath = "php simple_function_tracker\simple_function_tracker.php"  # Ctags binary path (please specify your own ctags path)

```

### STEP2 Change Script

change the CVEPatch_Collector.py command as our tools' command
```python
finding_cfiles = subprocess.check_output(
        ctagPath + ' -i ' + vulfile,
        stderr=subprocess.STDOUT, shell=True).decode(
        errors='ignore')

alllist = str(finding_cfiles)
with open(vulfile, 'r', encoding='utf8') as fp:
    body = ''.join(fp.readlines())

    for result in alllist.split('\n'):
        if result == '' or result == ' ' or result == '\n':
            continue

        funcname = result.split('\t')[0]
        # if len(result.split('\t')) < 7:
        #     continue

        if True:
            # Question : how to get the start line and end line here ? I wants to use php-ast here
            startline = int(result.split('\t')[1].replace('line:', ''))
            endline = int(result.split('\t')[-1].replace('end:', ''))
            if sl >= startline and el <= endline:
                funcbody = ''.join(
                        ''.join('\n'.join(body.split('\n')[startline - 1: endline]).split('{')[
                                1:]).split('}')[:-1])
```

change the OSS_Collecor.py to fit our tool

```python
functionList = subprocess.check_output(
        ctagsPath + ' -i ' + filePath, stderr=subprocess.STDOUT,
        shell=True).decode()
f = open(filePath, 'r', encoding='utf8', errors='ignore')
# For parsing functions
lines = f.readlines()
allFuncs = str(functionList).split('\n')

func = re.compile(r'(function)')
number = re.compile(r'(\d+)')
funcSearch = re.compile(r'{([\S\s]*)}')
tmpString = ""
funcBody = ""
fileCnt += 1

for i in allFuncs:
    # elemList = re.sub(r'[\t\s ]{2,}', '', i)
    # elemList = elemList.split('\t')
    funcBody = ""

    # if i != '' and len(elemList) >= 8 and func.fullmatch(elemList[3]):
    if i.count("\t") == 2:
        funcStartLine = int(i.split("\t")[1])
        funcEndLine = int(i.split("\t")[2])
```