<?php

use GreenCape\PHPVersions\Commands\GpgCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class GpgTest extends TestCase
{
    public function testSigned()
    {
        $command = new GpgCommand();
        $input = new StringInput('8.0');
        $output = new BufferedOutput();

        $keys = [
            'pollita' => '1729F83938DA44E27BA0F4D3DBDB397470D12172',
            'carusogabriel' => 'BFDDD28642824F8118EF77909B67A5C12229118F',
        ];

        $expected = implode(' ', $keys);

        $command->run($input, $output);

        self::assertEquals($expected, $output->fetch());
    }

    public function testUnsigned()
    {
        $command = new GpgCommand();
        $input = new StringInput('4.4');
        $output = new BufferedOutput();

        $command->run($input, $output);

        self::assertEquals('', $output->fetch());
    }
}
