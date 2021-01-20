<?php

use GreenCape\PHPVersions\Application;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ApplicationTest extends TestCase
{
    public function testNormal(): void
    {
        $application = new Application();
        $application->setAutoExit(false);

        $input = new StringInput('version 7');
        $output = new BufferedOutput();

        $application->run($input, $output);

        self::assertEquals('7.4.14', $output->fetch());
    }

    public function testVerbose(): void
    {
        $application = new Application();
        $application->setAutoExit(false);

        $input = new StringInput('-v version 7');
        $output = new BufferedOutput();

        $application->run($input, $output);

        self::assertEquals('7.4.14', $output->fetch());
    }

    public function testVeryVerbose(): void
    {
        $application = new Application();
        $application->setAutoExit(false);

        $input = new StringInput('-vv version 7');
        $output = new BufferedOutput();

        $application->run($input, $output);

        self::assertMatchesRegularExpression("~^Reading from cache \(.*/\.php_versions\)\n7\.4\.14~", $output->fetch());
    }

    public function testDebug(): void
    {
        $application = new Application();
        $application->setAutoExit(false);

        $input = new StringInput('-vvv version 7');
        $output = new BufferedOutput();

        $application->run($input, $output);

        self::assertMatchesRegularExpression("~^Reading from cache \(.*/\.php_versions\)\n7\.4\.14~", $output->fetch());
    }

    public function testQuiet(): void
    {
        $application = new Application();
        $application->setAutoExit(false);

        $input = new StringInput('--quiet version 7');
        $output = new BufferedOutput();

        $application->run($input, $output);

        self::assertEquals('', $output->fetch());
    }

    public function testVersion()
    {
        $application = new Application();
        $application->setAutoExit(false);

        self::assertMatchesRegularExpression('~^PHP Versions <info>\d+\.\d+\.\d+</info> by Niels Braczek~', $application->getLongVersion());
    }
}
