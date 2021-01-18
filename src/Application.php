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
 * @package     GreenCape\PHPVersions
 * @author      Niels Braczek <nbraczek@bsds.de>
 * @copyright   (C) 2017 GreenCape, Niels Braczek <nbraczek@bsds.de>
 * @license     http://opensource.org/licenses/MIT The MIT license (MIT)
 * @since       File available since Release 1.3.0
 */

namespace GreenCape\PHPVersions;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The main PHP Versions CLI application.
 *
 * @package     GreenCape\PHPVersions
 * @subpackage  Core
 * @since       Class available since Release 1.3.0
 */
class Application extends BaseApplication
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct('PHP Versions', '1.3.0');
        $this->setCatchExceptions(false);
        $this->addPlugins(__DIR__ . '/Commands');
    }

    /**
     * Dynamically add all commands from a path
     *
     * @param string $path The directory with the plugins
     *
     * @return  void
     */
    private function addPlugins(string $path): void
    {
        foreach (glob($path . '/*.php') as $filename) {
            require_once $filename;
            $commandClass = __NAMESPACE__ . '\\Commands\\' . basename($filename, '.php');
            $command = new $commandClass;
            $this->add($command);
        }
    }

    /**
     * Runs the current application.
     *
     * @param InputInterface|null $input An InputInterface instance
     * @param OutputInterface|null $output An OutputInterface instance
     *
     * @return  integer  0 if everything went fine, or an error code
     */
    public function run(InputInterface $input = null, OutputInterface $output = null): int
    {
        try {
            parent::run($input, $output);

            return 0;
        } catch (\Exception $e) {
            if (null === $output) {
                $output = new ConsoleOutput();
            }
            $message = array(
                $this->getLongVersion(),
                '',
                $e->getMessage(),
                ''
            );
            $output->writeln($message);

            return 1;
        }
    }

    /**
     * Returns the long version of the application.
     *
     * @return string The long application version
     */
    public function getLongVersion(): string
    {
        return parent::getLongVersion() . ' by Niels Braczek';
    }
}
