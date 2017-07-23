<?php
/**
 * Copyright © 2017 Maks Rafalko
 *
 * License: https://opensource.org/licenses/BSD-3-Clause New BSD License
 */
declare(strict_types=1);

namespace Infection\Finder;

use Symfony\Component\Process\ExecutableFinder;

class ComposerExecutableFinder extends AbstractExecutableFinder
{
    /**
     * @return string
     *
     * @throws \Exception
     */
    public function find()
    {
        $probable = ['composer', 'composer.phar'];
        $finder = new ExecutableFinder();
        $immediatePaths = [getcwd(), realpath(getcwd() . '/../'), realpath(getcwd() . '/../../')];

        foreach ($probable as $name) {
            if ($path = $finder->find($name, null, $immediatePaths)) {
                return $path;
            }
        }

        /**
         * Check for options without execute permissions and prefix the PHP
         * executable instead.
         */
        $result = $this->searchNonExecutables($probable, $immediatePaths);

        if (!is_null($result)) {
            return $result;
        }

        throw new \RuntimeException(
            'Unable to locate a Composer executable on local system. Ensure '
            . 'that Composer is installed and available.'
        );
    }
}
