<?php
/**
 * This code is licensed under the BSD 3-Clause License.
 *
 * Copyright (c) 2017, Maks Rafalko
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 *
 * * Neither the name of the copyright holder nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
 * DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER
 * CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY,
 * OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

declare(strict_types=1);

namespace Infection\Tests\Configuration;

use Infection\Configuration\Configuration;
use Infection\Configuration\Entry\Logs;
use Infection\Configuration\Entry\PhpUnit;
use Infection\Configuration\Entry\Source;
use Infection\Tests\Configuration\Entry\LogsAssertions;
use Infection\Tests\Configuration\Entry\PhpUnitAssertions;
use Infection\Tests\Configuration\Entry\SourceAssertions;

trait ConfigurationAssertions
{
    use LogsAssertions;
    use PhpUnitAssertions;
    use SourceAssertions;

    private function assertConfigurationStateIs(
        Configuration $configuration,
        ?int $expectedTimeout,
        Source $expectedSource,
        Logs $expectedLogs,
        string $expectedLogVerbosity,
        string $expectedTmpDir,
        PhpUnit $expectedPhpUnit,
        array $expectedMutators,
        ?string $expectedTestFramework,
        ?string $expectedBootstrap,
        ?string $expectedInitialTestsPhpOptions,
        ?string $expectedTestFrameworkOptions,
        ?string $expectedExistingCoveragePath,
        bool $expectedDebug,
        bool $expectedOnlyCovered,
        string $expectedFormatter,
        bool $expectedNoProgress,
        bool $expectedIgnoreMsiWithNoMutations,
        ?float $expectedMinMsi,
        bool $expectedShowMutations,
        ?float $expectedMinCoveredMsi
    ): void {
        $this->assertSame($expectedTimeout, $configuration->getProcessTimeout());
        $this->assertSourceStateIs(
            $configuration->getSource(),
            $expectedSource->getDirectories(),
            $expectedSource->getExcludes()
        );
        $this->assertLogsStateIs(
            $configuration->getLogs(),
            $expectedLogs->getTextLogFilePath(),
            $expectedLogs->getSummaryLogFilePath(),
            $expectedLogs->getDebugLogFilePath(),
            $expectedLogs->getPerMutatorFilePath(),
            $expectedLogs->getBadge()
        );
        $this->assertSame($expectedLogVerbosity, $configuration->getLogVerbosity());
        $this->assertSame($expectedTmpDir, $configuration->getTmpDir());
        $this->assertPhpUnitStateIs(
            $configuration->getPhpUnit(),
            $expectedPhpUnit->getConfigDir(),
            $expectedPhpUnit->getCustomPath()
        );
        $this->assertEqualsWithDelta($expectedMutators, $configuration->getMutators(), 10.);
        $this->assertSame($expectedTestFramework, $configuration->getTestFramework());
        $this->assertSame($expectedBootstrap, $configuration->getBootstrap());
        $this->assertSame($expectedInitialTestsPhpOptions, $configuration->getInitialTestsPhpOptions());
        $this->assertSame($expectedTestFrameworkOptions, $configuration->getTestFrameworkOptions());
        $this->assertSame($expectedExistingCoveragePath, $configuration->getExistingCoveragePath());
        $this->assertSame($expectedDebug, $configuration->isDebugEnabled());
        $this->assertSame($expectedOnlyCovered, $configuration->mutateOnlyCoveredCode());
        $this->assertSame($expectedFormatter, $configuration->getFormatter());
        $this->assertSame($expectedNoProgress, $configuration->showProgress());
        $this->assertSame($expectedIgnoreMsiWithNoMutations, $configuration->ignoreMsiWithNoMutations());
        $this->assertSame($expectedMinMsi, $configuration->getMinMsi());
        $this->assertSame($expectedShowMutations, $configuration->showMutations());
        $this->assertSame($expectedMinCoveredMsi, $configuration->getMinCoveredMsi());
    }
}
