<?php
/**
 * GreenCape PHP Versions
 *
 * MIT License
 *
 * Copyright (c) 2016-17, Niels Braczek <nbraczek@bsds.de>. All rights reserved.
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
 * @package     GreenCape\PHPVersions
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   (C) 2016-17 GreenCape, Niels Braczek <nbraczek@bsds.de>
 * @license     http://opensource.org/licenses/MIT The MIT license (MIT)
 */

namespace GreenCape\PHPVersions;

/**
 * Class PhpVersions
 *
 * A utility class to provide a list of all PHP versions and their matching xDebug version.
 *
 * @package GreenCape\PhpVersions
 * @version 1.2.0
 */
class PhpVersions
{
    public const VERBOSITY_SILENT = 0;
    public const VERBOSITY_NORMAL = 1;
    public const VERBOSITY_VERBOSE = 2;
    public const VERBOSITY_DEBUG = 3;
    public const VERBOSITY_MASK = 3;

    public const CACHE_ENABLED = 0;
    public const CACHE_DISABLED = 4;
    public const CACHE_MASK = 4;

    private int $verbosity;
    private array $versions = [];
    private array $aliases = [];
    private ?string $cacheFilename = null;

    /**
     * PHP - XDebug version map
     *
     * SHA256 values for XDebug are found at https://xdebug.org/download/historical (hover over 'Source' button)
     *
     * @var array|\string[][]
     */
    private array $xDebugVersions = [
        '3.0' => ['version' => '1.3.2', 'sha256' => 'f3f9d2e60d1e7a2621f546812195bd164174933252b5752b778450449eb3b9bd'],
        '4.3' => ['version' => '2.0.5', 'sha256' => '4638641e643f4cedd9d2ec360fb13f47221973518b07ec6a2016c967063bb8b2'],
        '5.2' => ['version' => '2.2.7', 'sha256' => '4fce7fc794ccbb1dd0b961191cd0323516e216502fe7209b03711fc621642245'],
        '5.4' => ['version' => '2.4.1', 'sha256' => '23c8786e0f5aae67b1e5035972bfff282710fb84c483887cebceb8ef5bbdf8ef'],
        '5.5' => ['version' => '2.5.5', 'sha256' => '72108bf2bc514ee7198e10466a0fedcac3df9bbc5bd26ce2ec2dafab990bf1a4'],
        '7.0' => ['version' => '2.7.2', 'sha256' => 'b0f3283aa185c23fcd0137c3aaa58554d330995ef7a3421e983e8d018b05a4a6'],
        '7.1' => ['version' => '2.9.8', 'sha256' => 'f555b6cc58d96c9965af942d22e0f1818b7a477a410c76b1ab0eebe85a762f8a'],
        '7.2' => ['version' => '3.0.2', 'sha256' => '096d46dec061341868d3e3933b977013a592e2e88992b2c0aba7fa52f87c4e17'],
    ];

    /**
     * PHP GPG keys
     *
     * Keys are found on https://www.php.net/gpg-keys.php
     *
     * @var array|\string[][][]
     */
    private array $gpgKeys = [
        '5.3' => [
            [
                'pub' => '0B96 609E 270F 565C 1329  2B24 C13C 70B8 7267 B52D',
                'uid' => 'David Soria Parra <dsp@php.net>'
            ],
            [
                'pub' => '0A95 E9A0 2654 2D53 835E  3F3A 7DEC 4E69 FC9C 83D7',
                'uid' => 'Johannes Schlüter <johannes@php.net>'
            ],
        ],
        '5.4' => [
            [
                'pub' => 'F382 5282 6ACD 957E F380  D39F 2F79 56BC 5DA0 4B5D',
                'uid' => 'Stanislav Malyshev (PHP key) <stas@php.net>'
            ],
        ],
        '5.5' => [
            [
                'pub' => '0BD7 8B5F 9750 0D45 0838  F95D FE85 7D9A 90D9 0EC1',
                'uid' => 'Julien Pauli <jpauli@php.net>'
            ],
            [
                'pub' => '0B96 609E 270F 565C 1329  2B24 C13C 70B8 7267 B52D',
                'uid' => 'David Soria Parra <dsp@php.net>'
            ],
            [
                'pub' => 'F382 5282 6ACD 957E F380  D39F 2F79 56BC 5DA0 4B5D',
                'uid' => 'Stanislav Malyshev (PHP key) <stas@php.net>'
            ],
        ],
        '5.6' => [
            [
                'pub' => '6E4F 6AB3 21FD C07F 2C33  2E3A C2BF 0BC4 33CF C8B3',
                'uid' => 'Ferenc Kovacs <tyrael@php.net>'
            ],
            [
                'pub' => '0BD7 8B5F 9750 0D45 0838  F95D FE85 7D9A 90D9 0EC1',
                'uid' => 'Julien Pauli <jpauli@php.net>'
            ],
        ],
        '7.0' => [
            [
                'pub' => '1A4E 8B72 77C4 2E53 DBA9  C7B9 BCAA 30EA 9C0D 5763',
                'uid' => 'Anatol Belski <ab@php.net>'
            ],
            [
                'pub' => '6E4F 6AB3 21FD C07F 2C33  2E3A C2BF 0BC4 33CF C8B3',
                'uid' => 'Ferenc Kovacs <tyrael@php.net>'
            ],
        ],
        '7.1' => [
            [
                'pub' => 'A917 B1EC DA84 AEC2 B568  FED6 F50A BC80 7BD5 DCD0',
                'uid' => 'Davey Shafik <davey@php.net>'
            ],
            [
                'pub' => '5289 95BF EDFB A719 1D46  839E F9BA 0ADA 31CB D89E',
                'uid' => 'Joe Watkins <krakjoe@php.net>'
            ],
            [
                'pub' => '1729 F839 38DA 44E2 7BA0  F4D3 DBDB 3974 70D1 2172',
                'uid' => 'Sara Golemon <pollita@php.net>'
            ],
        ],
        '7.2' => [
            [
                'pub' => '1729 F839 38DA 44E2 7BA0  F4D3 DBDB 3974 70D1 2172',
                'uid' => 'Sara Golemon <pollita@php.net>'
            ],
            [
                'pub' => 'B1B4 4D8F 021E 4E2D 6021  E995 DC9F F8D3 EE5A F27F',
                'uid' => 'Remi Collet <remi@php.net>'
            ],
            [
                'pub' => 'CBAF 69F1 73A0 FEA4 B537  F470 D66C 9593 118B CCB6',
                'uid' => 'Christoph M. Becker <cmb@php.net>'
            ],
        ],
        '7.3' => [
            [
                'pub' => 'CBAF 69F1 73A0 FEA4 B537  F470 D66C 9593 118B CCB6',
                'uid' => 'Christoph M. Becker <cmb@php.net>'
            ],
            [
                'pub' => 'F382 5282 6ACD 957E F380  D39F 2F79 56BC 5DA0 4B5D',
                'uid' => 'Stanislav Malyshev (PHP key) <stas@php.net>'
            ],
        ],
        '7.4' => [
            [
                'sec' => '5A52880781F755608BF815FC910DEB46F53EA312',
                'uid' => 'Derick Rethans (PHP) <derick@php.net>'
            ],
            [
                'pub' => '4267 0A7F E4D0 441C 8E46  3234 9E4F DC07 4A4E F02D',
                'uid' => 'Peter Kokot <petk@php.net>'
            ],
        ],
        '8.0' => [
            [
                'pub' => '1729 F839 38DA 44E2 7BA0  F4D3 DBDB 3974 70D1 2172',
                'uid' => 'Sara Golemon <pollita@php.net>'
            ],
            [
                'pub' => 'BFDD D286 4282 4F81 18EF  7790 9B67 A5C1 2229 118F',
                'uid' => 'Gabriel Caruso (Release Manager) <carusogabriel@php.net>'
            ],
        ],
    ];

    /**
     * PhpVersions constructor.
     *
     * Data is read from the cache file, if available.
     * If the cache file is older than one week, PhpVersions looks for new releases.
     * If the cache file does not exist, all known releases are downloaded from php.net.
     *
     * @param string $cache Optional cache path. Defaults to ~/.php_versions
     * @param int $flags Combination of VERBOSITY_* and CACHE_* flags, combined with '|'
     *
     */
    public function __construct($cache = null, $flags = self::VERBOSITY_NORMAL | self::CACHE_ENABLED)
    {
        $this->verbosity = ($flags & self::VERBOSITY_MASK);

        if (($flags & self::CACHE_MASK) === self::CACHE_ENABLED) {
            if (empty($cache)) {
                $cache = getenv('HOME') . '/.php_versions';
            }

            $this->cacheFilename = $cache;

            if (file_exists($this->cacheFilename)) {
                $this->out("Reading from cache ({$this->cacheFilename})", self::VERBOSITY_VERBOSE);

                $data = unserialize(file_get_contents($this->cacheFilename), [false]);
                $this->versions = $data['versions'];
                $this->aliases = $data['aliases'];

                if (time() - filemtime($this->cacheFilename) > 3600 * 24 * 7) {
                    $this->out("Looking for new releases");

                    $this->loadFromPhpSite(2);
                }

                return;
            }
        }

        $this->out("Fetching data from php.net");

        $this->loadFromPhpSite(1000);
    }

    private function out($message, $verbosity = self::VERBOSITY_NORMAL): void
    {
        if ($verbosity <= $this->verbosity) {
            echo "$message\n";
        }
    }

    private function loadFromPhpSite($max = 1): void
    {
        $versions = $this->versions;

        foreach ([3, 4, 5, 7, 8] as $major) {
            $url = "http://php.net/releases/index.php?serialize=1&version={$major}";

            if ($max > 1) {
                $url .= '&max=' . $max;
                /** @noinspection SlowArrayOperationsInLoopInspection */
                $versions = array_merge($versions, unserialize(file_get_contents($url), [false]));

                continue;
            }

            $info = unserialize(file_get_contents($url), [false]);
            $versions[$info['version']] = $info;
        }

        $versions = $this->handleVersions($versions);
        $this->versions = $versions;

        foreach ($this->getAliases() as $alias => $version) {
            $this->versions[$version]['aliases'][] = $alias;
        }

        $this->fixFilenameBug();

        if (!empty($this->cacheFilename)) {
            $this->out("Writing to cache ({$this->cacheFilename})", self::VERBOSITY_VERBOSE);

            file_put_contents(
                $this->cacheFilename,
                serialize(
                    [
                        'versions' => $this->versions,
                        'aliases' => $this->aliases
                    ]
                )
            );
        }
    }

    /**
     * @param $versions
     *
     * @return mixed
     */
    private function handleVersions($versions)
    {
        foreach ($versions as $version => $info) {
            $announcement = $this->handleAnnouncement($version, $info);
            $date = $this->handleDate($info);

            $sources = [];

            foreach ($info['source'] as $source) {
                if (!isset($source['filename'])) {
                    continue;
                }

                foreach (['md5', 'sha256'] as $index) {
                    if (!isset($source[$index])) {
                        $source[$index] = null;
                    }
                }

                if (isset($source['date'])) {
                    unset($source['date']);
                }

                $sources[preg_replace('~.*\.~', '', $source['filename'])] = $source;
            }

            $versions[$version] = [
                'version' => $version,
                'aliases' => [],
                'announcement' => $announcement,
                'date' => date('Y-m-d', strtotime($date)),
                'source' => $sources,
                'museum' => isset($info['museum']) ? (boolean)$info['museum'] : false,
                'xdebug' => $this->getXdebugInfo($version),
                'gpg' => $this->getGpgInfo($version),
            ];
        }

        return $versions;
    }

    /**
     * @param $version
     * @param $info
     *
     * @return string
     */
    private function handleAnnouncement($version, $info): ?string
    {
        if (!isset($info['announcement'])) {
            return null;
        }

        if ($info['announcement'] === 1) {
            return 'http://php.net/releases/' . str_replace('.', '_', $version) . '.php';
        }

        if (isset($info['announcement']['English'])) {
            if ($info['announcement']['English'][0] === '/') {
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
    private function handleDate($info): string
    {
        return $info['date'] ?? $info['source'][0]['date'];
    }

    /**
     * @param $version
     *
     * @return array
     */
    public function getXdebugInfo($version): array
    {
        $version = $this->resolveVersion($version);
        $xDebug = [];

        foreach ($this->xDebugVersions as $phpRelease => $xDebugInfo) {
            if (version_compare($phpRelease, $version, 'lt')) {
                $xDebug = $xDebugInfo;
            }
        }

        return $xDebug;
    }

    /**
     * @param $version
     *
     * @return mixed
     */
    private function resolveVersion($version)
    {
        if (isset($this->aliases[$version])) {
            $version = $this->aliases[$version];

            return $version;
        }

        return $version;
    }

    /**
     * @param $version
     *
     * @return array
     */
    public function getGpgInfo($version): array
    {
        $phpRelease = preg_replace('~^(\d+\.\d+)\.\d+$~', '\1', $this->resolveVersion($version));

        return $this->gpgKeys[$phpRelease] ?? [];
    }

    /**
     * @return array
     */
    private function getAliases(): array
    {
        $versions = $this->versions;

        if (empty($this->aliases)) {
            foreach (array_keys($versions) as $version) {
                /** @noinspection PhpUnusedLocalVariableInspection */
                [$major, $minor, $patch] = explode('.', $version);

                $this->updateAlias('latest', $version);
                $this->updateAlias($major, $version);
                $this->updateAlias("$major.$minor", $version);
            }
        }

        return $this->aliases;
    }

    /**
     * @param $index
     * @param $version
     */
    private function updateAlias($index, $version): void
    {
        if (!isset($this->aliases[$index]) || version_compare($version, $this->aliases[$index], 'gt')) {
            $this->aliases[$index] = $version;
        }
    }

    /**
     * Fix the filename bug
     *
     * The original data from the PHP site contain a wrong filename for
     * the xz compressed source of 5.5.37.
     */
    private function fixFilenameBug(): void
    {
        if (!isset($this->versions['5.5.37'])) {
            return;
        }

        $this->versions['5.5.37']['source']['xz']['filename'] = 'php-5.5.37.tar.xz';
        $this->versions['5.5.37']['source']['xz']['name'] = 'PHP 5.5.37 (tar.xz)';
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
    public function getVersions($pattern = null): array
    {
        $versions = array_keys($this->versions);

        if (empty($pattern)) {
            return $versions;
        }

        $pattern = str_replace(".", '\\.', $pattern);
        $pattern = preg_replace('~[xyz]+~i', '\\d+', $pattern);

        $result = [];

        foreach ($versions as $version) {
            if (preg_match("~^{$pattern}~", $version)) {
                $result[] = $version;
            }
        }

        return $result;
    }

    /**
     * Get the download information for a specific version.
     *
     * If an extension (one of 'xz', 'bz2', or 'gz') is provided, the matching source info is returned.
     * If there is no matching source package, or if no extension was given, the smallest possible package is returned.
     *
     * @param string $version The version. Defaults to 'latest'.
     * @param string $ext Optional preferred file extension of the download package
     *
     * @return array The download information.
     * @throws \Exception if no download information is found.
     */
    public function getSourceInfo($version = 'latest', $ext = null): array
    {
        $info = $this->getInfo($version);

        if (!empty($ext) && isset($info['source'][$ext])) {
            return $info['source'][$ext];
        }

        /** @noinspection SuspiciousLoopInspection */
        foreach (['xz', 'bz2', 'gz'] as $ext) {
            if (isset($info['source'][$ext])) {
                return $info['source'][$ext];
            }
        }

        throw new \RuntimeException("No source information for version $version.");
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
    public function getInfo($version = 'latest'): array
    {
        $version = $this->resolveVersion($version);

        if (!isset($this->versions[$version])) {
            throw new \RuntimeException("No information for version $version.");
        }

        return $this->versions[$version];
    }
}
