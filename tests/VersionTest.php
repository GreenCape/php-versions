<?php

use GreenCape\PHPVersions\Commands\VersionCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class VersionTest extends TestCase
{
    public function testMajor()
    {
        $command = new VersionCommand();
        $input = new StringInput('7');
        $output = new BufferedOutput();

        $command->run($input, $output);

        self::assertEquals('7.4.14', $output->fetch());
    }

    public function testMinor()
    {
        $command = new VersionCommand();
        $input = new StringInput('7.4');
        $output = new BufferedOutput();

        $command->run($input, $output);

        self::assertEquals('7.4.14', $output->fetch());
    }
}
