<?php
/**
 * GreenCape PHP Versions Command Line Interface
 *
 * MIT License
 *
 * Copyright (c) 2012-2015, Niels Braczek <nbraczek@bsds.de>. All rights reserved.
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
 * @package     GreenCape\JoomlaCLI
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   (C) 2017 GreenCape, Niels Braczek <nbraczek@bsds.de>
 * @license     http://opensource.org/licenses/MIT The MIT license (MIT)
 * @since       File available since Release 1.3.0
 */

namespace GreenCape\PHPVersions;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The download url command returns the download url for a PHP version.
 *
 * @package     GreenCape\JoomlaCLI
 * @subpackage  Command
 * @since       Class available since Release 1.3.0
 */
class DownloadUrlCommand extends Command
{
    /**
     * Configure the options for the version command
     *
     * @return  void
     */
    protected function configure()
    {
        $this
            ->setName('download-url')
            ->setDescription('Get the download URL for a PHP version or its signature file')
            ->addArgument(
                'php',
                InputOption::VALUE_OPTIONAL,
                'The PHP version to get the info for. Defaults to \'latest\''
            )->addOption(
                'asc',
                null,
                InputOption::VALUE_NONE,
                'Get the URL for the signature file instead of the PHP source'
            )->addOption(
                'format',
                'f',
                InputOption::VALUE_OPTIONAL,
                'The requested compression format, one of \'bz2\', \'gz\', or \'xz\''
            )->addOption(
                'url',
                'u',
                InputOption::VALUE_NONE,
                'If set, the full URL is returned. if not, just the filename'
            );
    }

    /**
     * Execute the version command
     *
     * @param   InputInterface $input An InputInterface instance
     * @param   OutputInterface $output An OutputInterface instance
     *
     * @return  void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $verbosity = PhpVersions::VERBOSITY_NORMAL;
        $verbosity = $input->getOption('verbose') == 2 ? PhpVersions::VERBOSITY_VERBOSE : $verbosity;
        $verbosity = $input->getOption('verbose') == 3 ? PhpVersions::VERBOSITY_DEBUG : $verbosity;
        $verbosity = $input->getOption('quiet') ? PhpVersions::VERBOSITY_SILENT : $verbosity;
        $phpVersions = new PhpVersions(null, $verbosity);

        $versions = $input->getArgument('php');
        $version = array_shift($versions);
        if (empty($version)) {
            $version = 'latest';
        }

        $format = $input->getOption('format');
        if (empty($format)) {
            $format = null;
        }

        $info = $phpVersions->getSourceInfo($version, $format);

        $filename = $info['filename'];
        if ($input->getOption('asc')) {
            $filename .= '.asc';
        }

        $result = $filename;
        if ($input->getOption('url')) {
            $result = "https://secure.php.net/get/$filename/from/this/mirror";
            $info = $phpVersions->getInfo($version);
            if ($info['museum']) {
                $result = "http://museum.php.net/php5/$filename";
            }
        }

        $output->write($result);
    }
}
