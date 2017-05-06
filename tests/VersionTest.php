<?php

class VersionTest extends \PHPUnit\Framework\TestCase
{
    public function versionMap()
    {
        return [
            ['php' => '5.2', 'xdebug' => '2.2.7',],
            ['php' => '5.3', 'xdebug' => '2.2.7',],
            ['php' => '5.4', 'xdebug' => '2.4.1',],
            ['php' => '5.5', 'xdebug' => '2.5.3',],
            ['php' => '5.6', 'xdebug' => '2.5.3',],
            ['php' => '7.0', 'xdebug' => '2.5.3',],
            ['php' => '7.1', 'xdebug' => '2.5.3',],
        ];
    }

    /**
     * @dataProvider versionMap
     */
    public function testXdebugVersion($phpVersion, $xdebugVersion)
    {
        $versions = new Greencape\PhpVersions();

        $xdebugInfo = $versions->getXdebugInfo($phpVersion);
        $this->assertEquals($xdebugVersion, $xdebugInfo['version']);
    }
}