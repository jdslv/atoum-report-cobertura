<?php

function fullPath(string ...$parts): string {
    return __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts);
}

$runner->addTestsFromDirectory(fullPath('tests', 'units'));

$runner
    ->getExtension(mageekguy\atoum\autoloop\extension::class)
        ->setWatchedFiles([fullPath('src')])
;
$runner->addExtension(new mageekguy\atoum\xml\extension($script));


# reports
if (extension_loaded('xdebug') === true) {
    $script->enableBranchAndPathCoverage();
    $script->noCodeCoverageInDirectories(fullPath('vendor'));

    if (!getenv('CI')) {
        // Show default report
        $script->addDefaultReport();

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
        # coverage report
        $covFile = fullPath('reports', 'cobertura.xml');
        $path = pathinfo($covFile, PATHINFO_DIRNAME);

        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        $cobertura = new mageekguy\atoum\reports\cobertura();
        $cobertura->addWriter(new mageekguy\atoum\writers\file($covFile));
        $runner->addReport($cobertura);

        # xunit report
        $xunitFile = fullPath('reports', 'junit.xml');
        $xunit = new mageekguy\atoum\reports\sonar\xunit();
        $xunit->addWriter(new mageekguy\atoum\writers\file($xunitFile));
        $runner->addReport($xunit);
    }
}
