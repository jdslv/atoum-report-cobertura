<?php

function fullPath(string ...$parts): string {
    return __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts);
}

$sourceDir = fullPath('src');
$testsDir = fullPath('tests', 'units');

$runner
    ->addTestsFromDirectory($testsDir)
    ->addExtension(new mageekguy\atoum\xml\extension($script))
    ->getExtension(mageekguy\atoum\autoloop\extension::class)
        ->setWatchedFiles([$sourceDir])
;


// Show default report
$script->addDefaultReport();


# reports
if (extension_loaded('xdebug') === true) {
    $script->enableBranchAndPathCoverage();
    $script->noCodeCoverageInDirectories(fullPath('vendor'));

    if (!getenv('CI')) {
        $path = fullPath('reports', 'coverage');

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        // HTML report
        $coverage = new mageekguy\atoum\reports\coverage\html();
        $coverage
            ->addWriter(new mageekguy\atoum\writers\std\out())
            ->setOutPutDirectory($path)
        ;
        $runner->addReport($coverage);
    } else {
        // coverage report
        $covFile = fullPath('reports', 'cobertura.xml');
        $path = pathinfo($covFile, PATHINFO_DIRNAME);

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $cobertura = new mageekguy\atoum\reports\cobertura();
        $cobertura->addWriter(new mageekguy\atoum\writers\file($covFile));
        $runner->addReport($cobertura);

        // xunit report
        $xunitFile = fullPath('reports', 'junit.xml');
        $xunit = new mageekguy\atoum\reports\sonar\xunit();
        $xunit->addWriter(new mageekguy\atoum\writers\file($xunitFile));
        $runner->addReport($xunit);

        // coveralls
        $token = getenv('COVERALLS_TOKEN');

        if ($token) {
            $branch = getenv('CI_COMMIT_BRANCH');

            $coveralls = new mageekguy\atoum\reports\asynchronous\coveralls($sourceDir, $token);
            $coveralls
                ->setServiceName('gitlab-ci')
                ->setServiceJobId(getenv('CI_JOB_ID') ?: null)
                ->addDefaultWriter()
            ;

            if ($branch) {
                $coveralls->setBranchFinder(function () {
                    return $branch;
                });
            }

            $runner->addReport($coveralls);
        }
    }
}
