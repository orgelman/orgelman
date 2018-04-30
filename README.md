# Orgelman
[![Build Status](https://travis-ci.org/orgelman/orgelman.svg)](https://travis-ci.org/orgelman/orgelman)
[![Latest Stable Version](https://poser.pugx.org/orgelman/orgelman/v/stable.svg)](https://packagist.org/packages/orgelman/orgelman) [![Total Downloads](https://poser.pugx.org/orgelman/orgelman/downloads)](https://packagist.org/packages/orgelman/orgelman) [![Latest Unstable Version](https://poser.pugx.org/orgelman/orgelman/v/unstable.svg)](https://packagist.org/packages/orgelman/orgelman) [![License](https://poser.pugx.org/orgelman/orgelman/license.svg)](https://packagist.org/packages/orgelman/orgelman)

## Code Example

```
<?php 
$debug = new orgelmanDebug("",__DIR__."/logs/");
$debug->set(true);
$debug->log("DEBUG FUNCTION DONE");

$_Orgelman = new orgelmanFunctions();

echo $_Orgelman->toAscii("Hello World");
?>

```

## Installation
Composer
```
{
    "require": {
        "orgelman/functions": "dev-master"
    }
}

```

## Version
```
<major>.<minor>.<patch>
```
