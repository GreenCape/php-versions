# greencape/php-versions

[![Code Climate](https://codeclimate.com/github/GreenCape/php-versions/badges/gpa.svg)](https://codeclimate.com/github/GreenCape/php-versions)
[![Latest Stable Version](https://poser.pugx.org/greencape/php-versions/v/stable.png)](https://packagist.org/packages/greencape/php-versions)
[![standard-readme compliant](https://img.shields.io/badge/standard--readme-OK-green.svg?style=flat-square)](https://github.com/RichardLitt/standard-readme)

> A utility class to provide a list of all PHP versions and their matching xDebug version.

*greencape/php-versions* is designed for use in automated build environments, as it provides the download information for any (stable) PHP version since 3.0.18. 

## Table of Contents

- [Install](#install)
- [Usage](#usage)
- [Examples](#examples)
- [API](#api)
- [Contribute](#contribute)
- [License](#license)

## Install

*greencape/php-versions* requires **PHP 5.4+** with **remote file access**, and comes with **no dependencies**.

Either download this repository and copy `src/php-versions.php` to your project, or use `composer` (recommended):

```bash
$ composer require greencape/php-versions
```

## Usage

The simplest use-case is to determine the current (latest) version of PHP:

```php
$versions = new Greencape\PhpVersions();

$latest = $versions->getInfo();
print_r($latest);
```

This will output

```
Array
(
    [version] => 7.0.12
    [aliases] => Array
        (
            [0] => latest
            [1] => 7
            [2] => 7.0
        )

    [announcement] => http://php.net/releases/7_0_12.php
    [date] => 2016-10-13
    [source] => Array
        (
            [bz2] => Array
                (
                    [filename] => php-7.0.12.tar.bz2
                    [name] => PHP 7.0.12 (tar.bz2)
                    [md5] => d7b11b40d84ed1f590e5f086f3711a3c
                    [sha256] => 38c47294fe8fb239b0230dc63a93c3e4044f472ab93b5dff8b65feb4103a6a27
                )

            [gz] => Array
                (
                    [filename] => php-7.0.12.tar.gz
                    [name] => PHP 7.0.12 (tar.gz)
                    [md5] => 5dd00a65a1d76a4792f6989d4576623d
                    [sha256] => c4693cc363b4bbc7224294cc94faf3598e616cbe8540dd6975f68c7d3c52682f
                )

            [xz] => Array
                (
                    [filename] => php-7.0.12.tar.xz
                    [name] => PHP 7.0.12 (tar.xz)
                    [md5] => bdcc4dbdac90c2a39422786653059f70
                    [sha256] => f3d6c49e1c242e5995dec15e503fde996c327eb86cd7ec45c690e93c971b83ff
                )

        )

    [museum] => 
    [xdebug] => Array
        (
            [version] => 2.4.1
            [sha1] => 52b5cede5dcb815de469d671bfdc626aec8adee3
        )

)
```

*greencape/php-versions* provides a couple of access methods. See [Examples](#examples) and [API](#api) sections for more information.

## Examples

**Get the latest release**

```php
$versions = new Greencape\PhpVersions();

$info = $versions->getInfo();
echo $info['version']; // 7.0.12
```

**Get the latest release of the 5.5 branch**

```php
$versions = new Greencape\PhpVersions();

$info = $versions->getInfo('5.5');
echo $info['version']; // 5.5.38
```

**Get the latest release of version 4**

```php
$versions = new Greencape\PhpVersions();

$info = $versions->getInfo('4');
echo $info['version']; // 4.4.9
```

**Get all release numbers of the 4.4 branch**

```php
$versions = new Greencape\PhpVersions();

$info = $versions->getVersions('4.4');
print_r($info);
```

Output:

```
Array
(
    [0] => 4.4.9
    [1] => 4.4.8
    [2] => 4.4.7
    [3] => 4.4.6
    [4] => 4.4.5
    [5] => 4.4.4
    [6] => 4.4.3
    [7] => 4.4.2
    [8] => 4.4.1
    [9] => 4.4.0
)
```

**Get the download info for PHP 5.2.9**

```php
$versions = new Greencape\PhpVersions();

$info = $versions->getSourceInfo('5.2.9');
print_r($info);
```

Output:

```
Array
(
    [filename] => php-5.2.9.tar.bz2
    [name] => PHP 5.2.9 (tar.bz2)
    [md5] => 280d6cda7f72a4fc6de42fda21ac2db7
    [sha256] => 
)
```

## API

See separate [API documentation](docs/API.md).

## Contribute

PRs are welcome.
If you encounter any bug or issues, please use the [issue tracker](https://github.com/GreenCape/php-versions/issues).

## License

[The MIT license (MIT)](LICENSE.md).
