<?php

namespace Cnimmo\TestOnlyChanged;

use Cnimmo\GranularTestsuites\TestsuiteGranulariser;
use Cnimmo\ListDeps\DependencyFinder;

class RelatedTestsFinder {

    public TestsuiteGranulariser $granulariser;

    public function __construct(string $testConfigPath)
    {
        $this->granulariser = new TestsuiteGranulariser($testConfigPath);
    }

    public function findRelatedTests(array $changedFiles, string | null $rootPath, array $ignorePaths, bool $allowMissing)
    {
        $this->granulariser->granularise('./phpunit.generated.xml', true);

        $unchangedTestFilePaths = [];
        $changedTestFilePaths = [];
        foreach ($this->granulariser->allTestFilePaths as $testFilePath) {
            if (in_array(realpath($testFilePath), $changedFiles)) {
                $changedTestFilePaths[] = $testFilePath;
            } else {
                $unchangedTestFilePaths[] = $testFilePath;
            }
        }

        $dependenciesByTestFile = (new DependencyFinder($rootPath, $ignorePaths, $unchangedTestFilePaths, $allowMissing))->findDependencies();

        return [
            ...array_keys(
                array_filter($dependenciesByTestFile, function ($dependentFiles) use ($changedFiles) {
                    $isDependent = count(array_intersect($changedFiles, $dependentFiles)) >= 1;
                    return $isDependent;
                })
            ),
            ...$changedTestFilePaths
        ];
    }
}
