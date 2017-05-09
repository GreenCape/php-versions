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
class HashCommand extends Command
{
    /**
     * Configure the options for the version command
     *
     * @return  void
     */
    protected function configure()
    {
        $this
            ->setName('hash')
            ->setDescription('Get the sha256 or md5 hash for a PHP distribution file')
            ->addArgument(
                'php',
                InputOption::VALUE_OPTIONAL,
                'The PHP version to get the info for. Defaults to \'latest\''
            )->addOption(
                'format',
                'f',
                InputOption::VALUE_OPTIONAL,
                'The compression format, one of \'bz2\', \'gz\', or \'xz\''
            )->addOption(
                'type',
                't',
                InputOption::VALUE_OPTIONAL,
                'The requested hash type, one of \'sha256\' (default) or \'md5\''
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
        $phpVersions = new PhpVersions();

        $versions = $input->getArgument('php');
        $version = array_shift($versions);
        if (empty($version)) {
            $version = 'latest';
        }

        $type = $input->getOption('type');
        if (empty($type)) {
            $type = 'sha256';
        }

        $format = $input->getOption('format');
        if (empty($format)) {
            $format = null;
        }

        $info = $phpVersions->getSourceInfo($version, $format);

        if (!isset($info[$type])) {
            throw new \RuntimeException("No info about $type hash");
        }

        $output->write($info[$type]);
    }
}
