# Class PhpVersions

> A utility class to provide a list of all PHP versions and their matching xDebug version.

  - Namespace: Greencape
  - Package: Greencape\PhpVersions
  - Version: 1.2.0
  - Located at php-versions.php

## __construct

`__construct( string $cache = null, integer $flags = Greencape\PhpVersions::VERBOSITY_NORMAL | Greencape\PhpVersions::CACHE_ENABLED)`

> PhpVersions constructor.

Data is read from the cache file, if enabled and available.
If caching is enabled and the cache file is older than one week, PhpVersions looks for new releases.
If caching is disabled or the cache file does not exist, all known releases are downloaded from php.net.

**Parameters**

  - `$cache` - Optional cache path. Defaults to ~/.php_versions
  - `$flags` - Combination of VERBOSITY_* and CACHE_* flags, combined with '|'. See *Constants Summary* below.

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

Constant | Value | as int
-------- | ----- | ------
`VERBOSITY_SILENT` | `0x0000` | 0
`VERBOSITY_NORMAL` | `0x0001` | 1
`VERBOSITY_VERBOSE` | `0x0010` | 2
`VERBOSITY_DEBUG` | `0x0011` | 3
`VERBOSITY_MASK` | `0x0011` | 3
`CACHE_ENABLED` | `0x0000` | 0
`CACHE_DISABLED` | `0x0100` | 4
`CACHE_MASK` | `0x0100` | 4
