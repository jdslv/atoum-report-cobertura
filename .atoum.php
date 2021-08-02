<?php

function fullPath(string ...$parts): string {
    return __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts);
}

$sourceDir = fullPath('src');
$testsDir = fullPath('tests', 'units');

$runner
    ->addTestsFromDirectory($testsDir)
    ->addExtension(new atoum\atoum\xml\extension($script))
    ->getExtension(atoum\atoum\autoloop\extension::class)
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
        $coverage = new atoum\atoum\reports\coverage\html();
        $coverage
            ->addWriter(new atoum\atoum\writers\std\out())
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

        $cobertura = new atoum\atoum\reports\cobertura();
        $cobertura->addWriter(new atoum\atoum\writers\file($covFile));
        $runner->addReport($cobertura);

        // xunit report
        $xunitFile = fullPath('reports', 'junit.xml');
        $xunit = new atoum\atoum\reports\sonar\xunit();
        $xunit->addWriter(new atoum\atoum\writers\file($xunitFile));
        $runner->addReport($xunit);
    }
}
