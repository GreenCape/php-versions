# Class PhpVersions

> A utility class to provide a list of all PHP versions and their matching xDebug version.

  - Namespace: Greencape
  - Package: Greencape\PhpVersions
  - Version: 1.0.0
  - Located at php-versions.php

## __construct
`__construct( string $cache = null, integer $verbosity = Greencape\PhpVersions::VERBOSITY_NORMAL )`

> PhpVersions constructor.

Data is read from the cache file, if available. If the cache file is older than one week, PhpVersions looks for new releases. If the cache file does not exist, all known releases are downloaded from php.net.

**Parameters**

  - `$cache` - Optional cache path. Defaults to ~/.php_versions
  - `$verbosity` - Verbosity level. 0=silent, 3=debug

## getVersions

`getVersions( string $pattern = null )`

> Get all known version numbers matching a pattern.

Patterns are matched from the beginning of the string. Valid patterns contain dots, numbers, and optionally letters (x, y, z) as wildcards. Multiple wildcards without a separating dot are treated as one.

If pattern is omitted, all version numbers are returned.

**Parameters**

  - `$pattern` - A version number pattern

**Returns**

`array` All matching version numbers.

## getInfo

`getInfo( string $version = 'latest' )`

> Get information about a specific version.

The version may be provided partially. It will be completed internally to match the latest version starting with the given string.

**Parameters**

  - `$version` - The version. Defaults to 'latest'.

**Returns**
`array` The information about the version.

**Throws**

`Exception` if version is unknown.

## getSourceInfo

`getSourceInfo( string $version = 'latest', string $ext = null )`

> Get the download information for a specific version.

If an extension (one of 'xz', 'bz2', or 'gz') is provided, the matching source info is returned. If there is no matching source package, or if no extension was given, the smallest possible package is returned.

**Parameters**

  - `$version` - The version. Defaults to 'latest'.
  - `$ext` - Optional preferred file extension of the download package

**Returns**

`array` The download information.

**Throws**

`Exception` if no download information is found.

## getGpgInfo

`getGpgInfo( string $version )`

> Get the GPG key(s) for a specific version.

The version may be provided partially. It will be completed internally to match the latest version starting with the given string.

**Parameters**

  - `$version` - The version.

**Returns**
`array` The GPG key(s) for the version, if available.

## getXdebugInfo

`getXdebugInfo( string $version )`

> Get information about XDebug for a specific version.

The version may be provided partially. It will be completed internally to match the latest version starting with the given string.

**Parameters**

  - `$version` - The version.

**Returns**
`array` The XDebug information for the version.

## Constants Summary

  - `VERBOSITY_SILENT` `integer 0`
  - `VERBOSITY_NORMAL` `integer 1`
  - `VERBOSITY_VERBOSE` `integer 2`
  - `VERBOSITY_DEBUG` `integer 3` 
