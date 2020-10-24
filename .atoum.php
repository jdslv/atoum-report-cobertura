<?php

function fullPath(string ...$parts): string {
    return __DIR__ . DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts);
}

$script->addDefaultReport();

$runner->addTestsFromDirectory(fullPath('tests', 'units'));

$runner
    ->getExtension(mageekguy\atoum\autoloop\extension::class)
        ->setWatchedFiles([fullPath('src')])
;
