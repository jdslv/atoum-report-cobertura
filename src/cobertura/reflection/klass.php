<?php declare(strict_types=1);

namespace mageekguy\atoum\reports\cobertura\reflection;

use ReflectionClass;

class klass extends ReflectionClass
{
    public function getFileName(string $base = null): string
    {
        $path = parent::getFileName();

        if ($base) {
            $path = str_replace(rtrim($base, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR, '', $path);
        }

        return $path;
    }
}
