<?php

use GreenCape\PHPVersions\Commands\InfoCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class InfoTest extends TestCase
{
    public function testInfo()
    {
        $command = new InfoCommand();
        $input = new StringInput('7.4');
        $output = new BufferedOutput();

        $expected = "Array\n(\n    [version] => 7.4.14\n    [aliases] => Array\n        (\n            [0] => 7\n            [1] => 7.4\n        )\n\n";

        $command->run($input, $output);

        self::assertStringStartsWith($expected, $output->fetch());
    }

    public function testInfoDump()
    {
        $command = new InfoCommand();
        $input = new StringInput('--format=dump 7.4');
        $output = new BufferedOutput();

        $expected = "Array\n(\n    [version] => 7.4.14\n    [aliases] => Array\n        (\n            [0] => 7\n            [1] => 7.4\n        )\n\n";

        $command->run($input, $output);

        self::assertStringStartsWith($expected, $output->fetch());
    }

    public function testInfoJson()
    {
        $command = new InfoCommand();
        $input = new StringInput('--format=json 7.4');
        $output = new BufferedOutput();

        $expected = '{"version":"7.4.14","aliases":[7,"7.4"],';

        $command->run($input, $output);

        self::assertStringStartsWith($expected, $output->fetch());
    }

    public function testInfoUnknown()
    {
        $command = new InfoCommand();
        $input = new StringInput('--format=unknown 7.4');
        $output = new BufferedOutput();

        $this->expectExceptionMessage("Format 'unknown' is currently not supported.'");

        $command->run($input, $output);
    }
}
