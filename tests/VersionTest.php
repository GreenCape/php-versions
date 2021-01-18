<?php

use GreenCape\PHPVersions\PhpVersions;
use PHPUnit\Framework\TestCase;

class VersionTest extends TestCase
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
}
