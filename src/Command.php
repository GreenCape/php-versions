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

use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The base command provides common functionality
 *
 * @package     GreenCape\PHPVersions
 * @subpackage  Command
 * @since       Class available since Release 1.5.0
 */
abstract class Command extends SymfonyCommand
{
    /**
     * Clone of Symfony command's execute method to add return type
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // @codeCoverageIgnoreStart
        throw new LogicException('You must override the execute() method in the concrete command class.');

        // @codeCoverageIgnoreEnd
    }

    /**
     * @param InputInterface $input
     * @return int
     */
    protected function getVerbosity(InputInterface $input): int
    {
        if ($input->hasParameterOption(['--quiet', '-q'], true)) {
            return PhpVersions::VERBOSITY_SILENT;
        }

        if ($input->hasParameterOption('-vvv', true)
            || $input->hasParameterOption('--verbose=3', true)
            || 3 === $input->getParameterOption('--verbose', false, true)) {
            return PhpVersions::VERBOSITY_DEBUG;
        }

        if ($input->hasParameterOption('-vv', true)
            || $input->hasParameterOption('--verbose=2', true)
            || 2 === $input->getParameterOption('--verbose', false, true)) {
            return PhpVersions::VERBOSITY_VERBOSE;
        }

        return PhpVersions::VERBOSITY_NORMAL;
    }

    /**
     * @param InputInterface $input
     * @return string
     */
    protected function getVersion(InputInterface $input): string
    {
        $versions = $input->getArgument('php');
        $version = array_shift($versions);

        if (empty($version)) {
            $version = 'latest';
        }

        return $version;
    }
}
