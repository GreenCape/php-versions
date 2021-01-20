<?php

use GreenCape\PHPVersions\Commands\DownloadUrlCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class DownloadUrlTest extends TestCase
{
    private function getLatest(): string
    {
        static $latest = null;

        if ($latest === null) {
            $majorVersions = json_decode(
                file_get_contents('https://www.php.net/releases/index.php?json'),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            $latest = $majorVersions[max(array_keys($majorVersions))]['version'];
        }

        return $latest;
    }

    public function testLatest()
    {
        $latest = $this->getLatest();

        $command = new DownloadUrlCommand();
        $input = new StringInput('');
        $output = new BufferedOutput();

        $command->run($input, $output);

        self::assertEquals("php-{$latest}.tar.xz", $output->fetch());
    }

    public function testLatestAsc()
    {
        $latest = $this->getLatest();

        $command = new DownloadUrlCommand();
        $input = new StringInput('--asc');
        $output = new BufferedOutput();

        $command->run($input, $output);

        self::assertEquals("php-{$latest}.tar.xz.asc", $output->fetch());
    }

    public function testLatestUrl()
    {
        $latest = $this->getLatest();

        $command = new DownloadUrlCommand();
        $input = new StringInput('--url');
        $output = new BufferedOutput();

        $command->run($input, $output);

        self::assertEquals("https://secure.php.net/get/php-{$latest}.tar.xz/from/this/mirror", $output->fetch());
    }

    public function testMuseumUrl()
    {
        $command = new DownloadUrlCommand();
        $input = new StringInput(' --url 4.4');
        $output = new BufferedOutput();

        $command->run($input, $output);

        self::assertEquals('http://museum.php.net/php4/php-4.4.9.tar.bz2', $output->fetch());
    }

    public function testMuseumUrlGz()
    {
        $command = new DownloadUrlCommand();
        $input = new StringInput(' --format=gz 4.4');
        $output = new BufferedOutput();

        $command->run($input, $output);

        self::assertEquals('php-4.4.9.tar.gz', $output->fetch());
    }

    public function testMuseumUrlUnknown()
    {
        $command = new DownloadUrlCommand();
        $input = new StringInput(' --format=unknown 4.4');
        $output = new BufferedOutput();

        $command->run($input, $output);

        self::assertEquals('php-4.4.9.tar.bz2', $output->fetch());
    }
}
