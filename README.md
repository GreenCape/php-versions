# GreenCape/php-versions

[![Code Climate](https://codeclimate.com/github/GreenCape/php-versions/badges/gpa.svg)](https://codeclimate.com/github/GreenCape/php-versions)
[![Latest Stable Version](https://poser.pugx.org/GreenCape/php-versions/v/stable.png)](https://packagist.org/packages/GreenCape/php-versions)
[![standard-readme compliant](https://img.shields.io/badge/standard--readme-OK-green.svg?style=flat-square)](https://github.com/RichardLitt/standard-readme)

> A utility class to provide a list of all PHP versions and their matching xDebug version.

*GreenCape/php-versions* is designed for use in automated build environments, as it provides the download information for any (stable) PHP version since 3.0.18. 

## Table of Contents

- [Install](#install)
- [Usage on the Command Line](#usage-on-the-command-line)
- [Usage as PHP Class](#usage-as-php-class)
- [API](#api)
- [Contribute](#contribute)
- [License](#license)

## Install

*GreenCape/php-versions* requires **PHP 7.4+** with **remote file access**, and comes with **no dependencies** except the Symfony console.

Either download this repository and copy `src/php-versions.php` to your project, or use `composer` (recommended):

```bash
$ composer require GreenCape/php-versions
```

## Usage on the Command Line

### General Info

Show information about a PHP version

    $ php-versions [options] [--] [<php>]

Argument | Description
-------- | -----------
php | The PHP version to get the info for. Defaults to 'latest'

Option | Description
------ | -----------
-f, --format[=FORMAT] | The output format. Supported values are 'dump' (default), 'json'

### Download URLs

Get the filename or download URL for a PHP version or its signature file

```bash
$ php-versions download-url [options] [--] [<php>]
```

Argument | Description
-------- | -----------
php | The PHP version to get the info for. Defaults to 'latest'

Option | Description
------ | -----------
--asc | Get the URL for the signature file instead of the PHP source
-f, --format[=FORMAT] | The requested compression format, one of 'bz2', 'gz', or 'xz'
-u, --url | If set, the full URL is returned. if not, just the filename

### GPG Keys

Get the GPG keys for a PHP distribution file

```bash
$ php-versions gpg [<php>]
```

Argument | Description
-------- | -----------
php | The PHP version to get the info for. Defaults to 'latest'

### Hashes

Get the sha256 or md5 hash for a PHP distribution file

```bash
$ php-versions hash [options] [--] [<php>]
```

Argument | Description
-------- | -----------
php | The PHP version to get the info for. Defaults to 'latest'

Option | Description
------ | -----------
-f, --format[=FORMAT] | The requested compression format, one of 'bz2', 'gz', or 'xz'
-t, --type[=TYPE] | The requested hash type, one of 'sha256' (default) or 'md5'

### Versions

Show full version number of a PHP version.

```bash
$ php-versions version [<php>]
```

Argument | Description
-------- | -----------
php | The PHP version to get the info for. Defaults to 'latest'


## Usage as PHP Class

The simplest use-case is to determine the current (latest) version of PHP:

```php
$versions = new GreenCape\PhpVersions();

$latest = $versions->getInfo();
print_r($latest);
```

This will output

```
Array
(
    [version] => 7.1.0
    [aliases] => Array
        (
            [0] => latest
            [1] => 7
            [2] => 7.1
        )
    [announcement] => http://php.net/releases/7_1_0.php
    [date] => 2016-12-01
    [source] => Array
        (
            [bz2] => Array
                (
                    [filename] => php-7.1.0.tar.bz2
                    [name] => PHP 7.1.0 (tar.bz2)
                    [md5] => 54e364b60a88db77adb96aacb10f10a4
                    [sha256] => 68bcfd7deed5b3474d81dec9f74d122058327e2bed0ac25bbc9ec70995228e61
                )
            [gz] => Array
                (
                    [filename] => php-7.1.0.tar.gz
                    [name] => PHP 7.1.0 (tar.gz)
                    [md5] => ec2218f97b4edbc35a2d7919ff37a662
                    [sha256] => 9e84c5b13005c56374730edf534fe216f6a2e63792a9703d4b894e770bbccbae
                )
            [xz] => Array
                (
                    [filename] => php-7.1.0.tar.xz
                    [name] => PHP 7.1.0 (tar.xz)
                    [md5] => cf36039303c47f493100afea522a8f53
                    [sha256] => a810b3f29c21407c24caa88f50649320d20ba6892ae1923132598b8a0ca145b6
                )
        )
    [museum] => 
    [xdebug] => Array
        (
            [version] => 2.4.1
            [sha1] => 52b5cede5dcb815de469d671bfdc626aec8adee3
        )
    [gpg] => Array
        (
            [0] => Array
                (
                    [pub] => A917 B1EC DA84 AEC2 B568 FED6 F50A BC80 7BD5 DCD0
                    [uid] => Davey Shafik <davey@php.net>
                )
        )
)
```

*GreenCape/php-versions* provides a couple of access methods. See [Examples](#examples) and [API](#api) sections for more information.

### Examples

**Get the latest release**

```php
$versions = new GreenCape\PhpVersions();

$info = $versions->getInfo();
echo $info['version']; // 7.1.0
```

**Get the latest release of the 5.5 branch**

```php
$versions = new GreenCape\PhpVersions();

$info = $versions->getInfo('5.5');
echo $info['version']; // 5.5.38
```

**Get the latest release of version 4**

```php
$versions = new GreenCape\PhpVersions();

$info = $versions->getInfo('4');
echo $info['version']; // 4.4.9
```

**Get all release numbers of the 4.4 branch**

```php
$versions = new GreenCape\PhpVersions();

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
$versions = new GreenCape\PhpVersions();

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

**Get the GPG key(s) for PHP 5.4**

```php
$versions = new GreenCape\PhpVersions();

$info = $versions->getGpgInfo('5.4');
print_r($info);
```

Output:

```
Array
(
    [0] => Array
        (
            [pub] => F382 5282 6ACD 957E F380  D39F 2F79 56BC 5DA0 4B5D
            [uid] => Stanislav Malyshev (PHP key) <stas@php.net>
        )
)
```

**Get the XDebug information for PHP 5.4**

```php
$versions = new GreenCape\PhpVersions();

$info = $versions->getXdebugInfo('5.4');
print_r($info);
```

Output:

```
Array
(
    [version] => 2.4.1
    [sha1] => 52b5cede5dcb815de469d671bfdc626aec8adee3
)
```

## API

See separate [API documentation](docs/API.md).

## Contribute

PRs are welcome.
If you encounter any bug or issues, please use the [issue tracker](https://github.com/GreenCape/php-versions/issues).

## License

[The MIT license (MIT)](LICENSE.md).
