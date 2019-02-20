# Array Sanitization Library

[![Build Status](https://travis-ci.org/multidimension-al/array-sanitization.svg)](https://travis-ci.org/multidimension-al/array-sanitization)
[![Latest Stable Version](https://poser.pugx.org/multidimensional/array-sanitization/v/stable.svg)](https://packagist.org/packages/multidimensional/array-sanitization)
[![Code Coverage](https://scrutinizer-ci.com/g/multidimension-al/array-sanitization/badges/coverage.png)](https://scrutinizer-ci.com/g/multidimension-al/array-sanitization/)
[![Minimum PHP Version](http://img.shields.io/badge/php-%3E%3D%205.5-8892BF.svg)](https://php.net/)
[![License](https://poser.pugx.org/multidimensional/array-sanitization/license.svg)](https://packagist.org/packages/multidimensional/array-sanitization)
[![Total Downloads](https://poser.pugx.org/multidimensional/array-sanitization/d/total.svg)](https://packagist.org/packages/multidimensional/array-sanitization)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/multidimension-al/array-sanitization/badges/quality-score.png)](https://scrutinizer-ci.com/g/multidimension-al/array-sanitization/)

This library sanitizes an array based on another array ruleset. The ruleset can have specific settings to force array values such as boolean, string, integer, decimal and others, as well as force regular expression pattern matching. This library is intended to be used as a compliment to our [Array Validation](https://github.com/multidimension-al/array-validation) library.

## Requirements

* PHP 5.5+

# Installation

The easiest way to install this library is to use composer. To install, simply include the following in your ```composer.json``` file:

```
"require": {
    "multidimensional/array-sanitization": "*"
}
```

Or run the following command from a terminal or shell:

```
composer require --prefer-dist multidimensional/array-sanitization
```

You can also specify version constraints, with more information available [here](https://getcomposer.org/doc/articles/versions.md).

# Usage

This library utilizes PSR-4 autoloading, so make sure you include the library near the top of your class file:

```php
use Multidimensional\ArraySanitization\Sanitization;
```

How to use in your code:

__Create Ruleset__

```php
$rules = ['keyName' => ['type' => 'integer']];
```

__Sanitize an array against that ruleset__

```php
$array = ['keyName' => 10.1];
$result = Sanitization::sanitize($array, $rules);
print_r ($result);

/*
 * $result = array('keyName' => 10);
 */
```

# Advanced Rulesets

Specific ruleset examples can be found in the ```SanitizationTest.php``` file. For more advanced rulesets, view the [README.md](https://github.com/multidimension-al/array-validation/blob/master/README.md) from our [Array Validation](https://github.com/multidimension-al/array-validation) library. 


# License

    MIT License
    
    Copyright (c) 2017 - 2019 multidimension.al
    
    Permission is hereby granted, free of charge, to any person obtaining a copy
    of this software and associated documentation files (the "Software"), to deal
    in the Software without restriction, including without limitation the rights
    to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
    copies of the Software, and to permit persons to whom the Software is
    furnished to do so, subject to the following conditions:
    
    The above copyright notice and this permission notice shall be included in all
    copies or substantial portions of the Software.
    
    THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
    IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
    FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
    AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
    LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
    OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
    SOFTWARE.