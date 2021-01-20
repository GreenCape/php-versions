<?php

use GreenCape\PHPVersions\Commands\HashCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class HashTest extends TestCase
{
    public function hashValues()
    {
        return [
            ['8.0', '208b3330af881b44a6a8c6858d569c72db78dab97810332978cc65206b0ec2dc'],
            ['--type=sha256 8.0', '208b3330af881b44a6a8c6858d569c72db78dab97810332978cc65206b0ec2dc'],
            ['--type=md5 8.0', ''],
            ['4.4', ''],
            ['--type=md5 4.4', '2e3b2a0e27f10cb84fd00e5ecd7a1880'],
            ['--format=bz2 --type=md5 4.4', '2e3b2a0e27f10cb84fd00e5ecd7a1880'],
            ['--format=gz --type=md5 4.4', '9bcc1aba50be0dfeeea551d018375548'],
        ];
    }

    /**
     * @param $inputString
     * @param $expected
     * @dataProvider hashValues
     */
    public function testHash($inputString, $expected)
    {
        $command = new HashCommand();
        $input = new StringInput($inputString);
        $output = new BufferedOutput();

        $command->run($input, $output);

        self::assertEquals($expected, $output->fetch());
    }
}
