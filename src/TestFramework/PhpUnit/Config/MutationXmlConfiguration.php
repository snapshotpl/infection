<?php

declare(strict_types=1);


namespace Infection\TestFramework\PhpUnit\Config;

use Infection\TestFramework\Coverage\CodeCoverageData;
use Infection\TestFramework\PhpUnit\Config\Path\PathReplacer;

class MutationXmlConfiguration extends AbstractXmlConfiguration
{
    /**
     * @var string
     */
    private $customAutoloadFilePath;

    /**
     * @var array
     */
    private $coverageTests;

    public function __construct(string $tempDirectory, string $originalXmlConfigPath, PathReplacer $pathReplacer, string $customAutoloadFilePath, array $coverageTests)
    {
        parent::__construct($tempDirectory, $originalXmlConfigPath, $pathReplacer);

        $this->customAutoloadFilePath = $customAutoloadFilePath;
        $this->coverageTests = $coverageTests;
    }

    public function getXml() : string
    {
        $originalXml = file_get_contents($this->originalXmlConfigPath);

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($originalXml);

        $xPath = new \DOMXPath($dom);

        $this->replaceWithAbsolutePaths($xPath);
        $this->setCustomAutoLoaderPath($xPath);
        $this->setStopOnFailure($xPath);
        $this->deactivateColours($xPath);
        $this->removeExistingLoggers($dom, $xPath);
        $this->setFilteredTestsToRun($dom, $xPath);

        return $dom->saveXML();
    }

    private function setCustomAutoLoaderPath(\DOMXPath $xPath)
    {
        $node = $xPath->query('/phpunit/@bootstrap')[0];

        $node->nodeValue = $this->customAutoloadFilePath;
    }

    private function setFilteredTestsToRun(\DOMDocument $dom, \DOMXPath $xPath)
    {
        $this->removeExistingTestSuite($xPath);

        $this->addTestSuiteWIthFilteredTestFiles($dom, $xPath);
    }

    private function removeExistingTestSuite(\DOMXPath $xPath)
    {
        $nodes = $xPath->query('/phpunit/testsuites/testsuite');

        foreach ($nodes as $node) {
            $node->parentNode->removeChild($node);
        }
    }

    private function addTestSuiteWIthFilteredTestFiles(\DOMDocument $dom, \DOMXPath $xPath)
    {
        $loggingList = $xPath->query('/phpunit/testsuites');

        $testsuites = $loggingList->item(0);

        $testsuite = $dom->createElement('testsuite');
        $testsuite->setAttribute('name', 'Infection testsuite with filtered tests');

        $uniqueCoverageTests = $this->unique($this->coverageTests);

        // sort tests to run the fastest first
        usort(
            $uniqueCoverageTests,
            function (array $a, array $b) {
                if ($a['time'] === $b['time']) {
                    return 0;
                }

                return $a['time'] < $b['time'] ? -1 : 1;
            }
        );

        $uniqueTestFilePaths = array_column($uniqueCoverageTests, 'testFilePath');

        foreach ($uniqueTestFilePaths as $testFilePath) {
            $file = $dom->createElement('file', $testFilePath);

            $testsuite->appendChild($file);
        }

        $testsuites->appendChild($testsuite);
    }

    private function unique(array $coverageTests): array
    {
        $usedFileNames = [];
        $uniqueTests = [];

        foreach ($coverageTests as $coverageTest) {
            if (!in_array($coverageTest['testFilePath'], $usedFileNames, true)) {
                $uniqueTests[] = $coverageTest;
                $usedFileNames[] = $coverageTest['testFilePath'];
            }
        }

        return $uniqueTests;
    }
}