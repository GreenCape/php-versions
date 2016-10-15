<?php
/**
 * GreenCape PHP Versions
 *
 * MIT License
 *
 * Copyright (c) 2016, Niels Braczek <nbraczek@bsds.de>. All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and
 * to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @package         GreenCape\PHPVersions
 * @author          Niels Braczek <nbraczek@bsds.de>
 * @copyright   (C) 2016 GreenCape, Niels Braczek <nbraczek@bsds.de>
 * @license         http://opensource.org/licenses/MIT The MIT license (MIT)
 */

namespace Greencape;

/**
 * Class PhpVersions
 *
 * A utility class to provide a list of all PHP versions and their matching xDebug version.
 *
 * @package Greencape\PhpVersions
 * @version 1.0.0
 */
class PhpVersions
{
	const VERBOSITY_SILENT = 0;
	const VERBOSITY_NORMAL = 1;
	const VERBOSITY_VERBOSE = 2;
	const VERBOSITY_DEBUG = 3;

	private $verbosity = 1;
	private $versions = [];
	private $aliases = [];
	private $cacheFilename;
	private $xDebugVersions = [
		'3.0' => ['version' => '1.3.2', 'sha1' => '333338d3c04bc41e16d63bd23b6f0f43298b9eb1'],
		'4.3' => ['version' => '2.0.5', 'sha1' => '77e6a8fd56641d8b37be68ea3c4a5c52b7511114'],
		'5.2' => ['version' => '2.2.7', 'sha1' => '587d300b8df0d1213910c59dda0c4f5807233744'],
		'5.4' => ['version' => '2.4.1', 'sha1' => '52b5cede5dcb815de469d671bfdc626aec8adee3'],
	];

	/**
	 * PhpVersions constructor.
	 *
	 * Data is read from the cache file, if available.
	 * If the cache file is older than one week, PhpVersions looks for new releases.
	 * If the cache file does not exist, all known releases are downloaded from php.net.
	 *
	 * @param string $cache     Optional cache path. Defaults to ~/.php_versions
	 * @param int    $verbosity Verbosity level. 0=silent, 3=debug
	 */
	public function __construct($cache = null, $verbosity = self::VERBOSITY_NORMAL)
	{
		$this->verbosity = $verbosity;

		if (empty($cache))
		{
			$cache = getenv('HOME') . '/.php_versions';
		}

		$this->cacheFilename = $cache;

		if (file_exists($this->cacheFilename))
		{
			$this->out("Reading from cache ({$this->cacheFilename})", self::VERBOSITY_VERBOSE);

			$data           = unserialize((file_get_contents($this->cacheFilename)));
			$this->versions = $data['versions'];
			$this->aliases  = $data['aliases'];

			if (time() - filemtime($this->cacheFilename) > 3600 * 24 * 7)
			{
				$this->out("Looking for new releases", self::VERBOSITY_NORMAL);

				$this->loadFromPhpSite(false);
			}
		}
		else
		{
			$this->out("Fetching data from php.net", self::VERBOSITY_NORMAL);

			$this->loadFromPhpSite(true);
		}
	}

	/**
	 * Get all known version numbers matching a pattern.
	 *
	 * Patterns are matched from the beginning of the string.
	 * Valid patterns contain dots, numbers, and optionally letters (x, y, z) as wildcards.
	 * Multiple wildcards without a separating dot are treated as one.
	 *
	 * If `pattern` is omitted, all version numbers are returned.
	 *
	 * @param string $pattern A version number pattern
	 *
	 * @return array All matching version numbers.
	 */
	public function getVersions($pattern = null)
	{
		$versions = array_keys($this->versions);

		if (empty($pattern))
		{
			return $versions;
		}

		$pattern = preg_replace('~\.~', '\\.', $pattern);
		$pattern = preg_replace('~[xyz]+~i', '\\d+', $pattern);

		$result = [];

		foreach ($versions as $version)
		{
			if (preg_match("~^{$pattern}~", $version))
			{
				$result[] = $version;
			}
		}

		return $result;
	}

	/**
	 * Get information about a specific version.
	 *
	 * The version may be provided partially.
	 * It will be completed internally to match the latest version starting with the given string.
	 *
	 * @param string $version The version. Defaults to 'latest'.
	 *
	 * @return array The information about the version.
	 * @throws \Exception if `version` is unknown.
	 */
	public function getInfo($version = 'latest')
	{
		if (isset($this->aliases[$version]))
		{
			$version = $this->aliases[$version];
		}

		if (!isset($this->versions[$version]))
		{
			throw new \Exception("No information for version $version.");
		}

		return $this->versions[$version];
	}

	/**
	 * Get the download information for a specific version.
	 *
	 * If an extension (one of 'xz', 'bz2', or 'gz') is provided, the matching source info is returned.
	 * If there is no matching source package, or if no extension was given, the smallest possible package is returned.
	 *
	 * @param string $version The version. Defaults to 'latest'.
	 * @param string $ext     Optional preferred file extension of the download package
	 *
	 * @return array The download information.
	 * @throws \Exception if no download information is found.
	 */
	public function getSourceInfo($version = 'latest', $ext = null)
	{
		$info = $this->getInfo($version);

		if (!empty($ext) && isset($info['source'][$ext]))
		{
			return $info['source'][$ext];
		}

		foreach (['xz', 'bz2', 'gz'] as $ext)
		{
			if (isset($info['source'][$ext]))
			{
				return $info['source'][$ext];
			}
		}

		throw new \Exception("No source information for version $version.");
	}

	private function loadFromPhpSite($getAllVersions = false)
	{
		$versions = $this->versions;

		foreach ([3, 4, 5, 7] as $major)
		{
			$url = "http://php.net/releases/index.php?serialize=1&version={$major}";
			if ($getAllVersions)
			{
				$url .= '&max=1000';
				$versions = array_merge($versions, unserialize(file_get_contents($url)));
			}
			else
			{
				$info                       = unserialize(file_get_contents($url));
				$versions[$info['version']] = $info;
			}
		}

		$versions       = $this->handleVersions($versions);
		$this->versions = $versions;

		foreach ($this->getAliases() as $alias => $version)
		{
			$this->versions[$version]['aliases'][] = $alias;
		}

		$this->out("Writing to cache ({$this->cacheFilename})", self::VERBOSITY_VERBOSE);

		file_put_contents($this->cacheFilename, serialize([
			'versions' => $this->versions,
			'aliases'  => $this->aliases
		]));
	}

	/**
	 * @param $versions
	 *
	 * @return mixed
	 */
	private function handleVersions($versions)
	{
		foreach ($versions as $version => $info)
		{
			$announcement = $this->handleAnnouncement($version, $info);
			$date         = $this->handleDate($info);

			$sources = [];

			foreach ($info['source'] as $source)
			{
				if (!isset($source['filename']))
				{
					continue;
				}

				foreach (['md5', 'sha256'] as $index)
				{
					if (!isset($source[$index]))
					{
						$source[$index] = null;
					}
				}

				if (isset($source['date']))
				{
					unset($source['date']);
				}

				$sources[preg_replace('~.*\.~', '', $source['filename'])] = $source;
			}

			$versions[$version] = [
				'version'      => $version,
				'aliases'      => [],
				'announcement' => $announcement,
				'date'         => date('Y-m-d', strtotime($date)),
				'source'       => $sources,
				'museum'       => isset($info['museum']) ? (boolean) $info['museum'] : false,
				'xdebug'       => $this->getXdebugInfo($version),
			];
		}

		return $versions;
	}

	/**
	 * @return array
	 */
	private function getAliases()
	{
		$versions = $this->versions;

		if (empty($this->aliases))
		{
			foreach (array_keys($versions) as $version)
			{
				list($major, $minor, $patch) = explode('.', $version);

				$this->updateAlias('latest', $version);
				$this->updateAlias($major, $version);
				$this->updateAlias("$major.$minor", $version);
			}
		}

		return $this->aliases;
	}

	/**
	 * @param $version
	 * @param $info
	 *
	 * @return string
	 */
	private function handleAnnouncement($version, $info)
	{
		$announcement = null;

		if (!isset($info['announcement']))
		{
			return null;
		}

		if ($info['announcement'] == 1)
		{
			return 'http://php.net/releases/' . str_replace('.', '_', $version) . '.php';
		}

		if (isset($info['announcement']['English']))
		{
			if ($info['announcement']['English'][0] == '/')
			{
				return 'http://php.net' . $info['announcement']['English'];
			}

			return $info['announcement']['English'];
		}

		return $info['announcement'];
	}

	/**
	 * @param $info
	 *
	 * @return string
	 */
	private function handleDate($info)
	{
		return isset($info['date']) ? $info['date'] : $info['source'][0]['date'];
	}

	/**
	 * @param $index
	 * @param $version
	 */
	private function updateAlias($index, $version)
	{
		if (!isset($this->aliases[$index]) || version_compare($version, $this->aliases[$index], 'gt'))
		{
			$this->aliases[$index] = $version;
		}
	}

	/**
	 * @param $version
	 */
	private function getXdebugInfo($version)
	{
		$xDebug = [];

		foreach ($this->xDebugVersions as $phpRelease => $xDebugInfo)
		{
			if (version_compare($phpRelease, $version, 'lt'))
			{
				$xDebug = $xDebugInfo;
			}
		}

		return $xDebug;
	}

	private function out($message, $verbosity = self::VERBOSITY_NORMAL)
	{
		if ($verbosity <= $this->verbosity)
		{
			echo "$message\n";
		}
	}
}

$v = new PhpVersions();

$foo = $v->getVersions('x.y.zz');
print_r($foo);
