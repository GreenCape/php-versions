<?php

use GreenCape\PHPVersions\PhpVersions;
use PHPUnit\Framework\TestCase;

class PHPVersionTest extends TestCase
{
    public function versionMap(): array
    {
        return [
            ['php' => '5.2', 'xdebug' => '2.2.7',],
            ['php' => '5.3', 'xdebug' => '2.2.7',],
            ['php' => '5.4', 'xdebug' => '2.4.1',],
            ['php' => '5.5', 'xdebug' => '2.5.5',],
            ['php' => '5.6', 'xdebug' => '2.5.5',],
            ['php' => '7.0', 'xdebug' => '2.7.2',],
            ['php' => '7.1', 'xdebug' => '2.9.8',],
            ['php' => '7.2', 'xdebug' => '3.0.2',],
            ['php' => '7.3', 'xdebug' => '3.0.2',],
            ['php' => '7.4', 'xdebug' => '3.0.2',],
            ['php' => '8.0', 'xdebug' => '3.0.2',],
        ];
    }

    /**
     * @dataProvider versionMap
     * @param $phpVersion
     * @param $xdebugVersion
     */
    public function testXdebugVersion($phpVersion, $xdebugVersion): void
    {
        $versions = new PhpVersions();

        $xdebugInfo = $versions->getXdebugInfo($phpVersion);
        self::assertEquals($xdebugVersion, $xdebugInfo['version']);
    }

    public function testGetVersionWithoutCache(): void
    {
        $this->expectOutputString("Fetching data from php.net\n");
        $flags = PhpVersions::VERBOSITY_NORMAL | PhpVersions::CACHE_DISABLED;
        $versions = new PhpVersions(null, $flags);

        $versions->getVersions('4');
    }

    public function testStructure()
    {
        $versions = new PhpVersions();

        $expected = [
            'version' => '5.3.29',
            'aliases' => ['5.3'],
            'announcement' => 'http://php.net/releases/5_3_29.php',
            'date' => '2014-08-14',
            'source' => [
                'bz2' => [
                    'filename' => 'php-5.3.29.tar.bz2',
                    'name' => 'PHP 5.3.29 (tar.bz2)',
                    'sha256' => 'c4e1cf6972b2a9c7f2777a18497d83bf713cdbecabb65d3ff62ba441aebb0091',
                    'md5' => null,
                ],
                'gz' => [
                    'filename' => 'php-5.3.29.tar.gz',
                    'name' => 'PHP 5.3.29 (tar.gz)',
                    'sha256' => '57cf097de3d6c3152dda342f62b1b2e9c988f4cfe300ccfe3c11f3c207a0e317',
                    'md5' => null,
                ],
                'xz' => [
                    'filename' => 'php-5.3.29.tar.xz',
                    'name' => 'PHP 5.3.29 (tar.xz)',
                    'sha256' => '8438c2f14ab8f3d6cd2495aa37de7b559e33b610f9ab264f0c61b531bf0c262d',
                    'md5' => null,
                ],
            ],
            'museum' => false,
            'xdebug' => [
                'version' => '2.2.7',
                'sha256' => '4fce7fc794ccbb1dd0b961191cd0323516e216502fe7209b03711fc621642245',
            ],
            'gpg' => [
                [
                    'pub' => '0B96 609E 270F 565C 1329  2B24 C13C 70B8 7267 B52D',
                    'uid' => 'David Soria Parra <dsp@php.net>',
                ],
                [
                    'pub' => '0A95 E9A0 2654 2D53 835E  3F3A 7DEC 4E69 FC9C 83D7',
                    'uid' => 'Johannes Schlüter <johannes@php.net>',
                ],
            ],
        ];
        $actual = $versions->getInfo('5.3');

        self::assertEquals($expected, $actual);
    }
}
