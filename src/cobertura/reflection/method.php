<?php declare(strict_types=1);

namespace mageekguy\atoum\reports\cobertura\reflection;

use ReflectionMethod;

class method extends ReflectionMethod
{
    protected $currentClass;

    public function __construct($class, string $name = null)
    {
        if (!$name) {
            parent::__construct($class);

            [$class, $name] = explode('::', $class);
        } else {
            parent::__construct($class, $name);
        }

        $this->currentClass = $class;
    }

    public function getCurrentClass(): klass
    {
        return new klass($this->currentClass);
    }

    public function getFullName(): string
    {
        return $this->getCurrentClass()->getName() . '::' . $this->getName();
    }

    public function getShortSignature(): string
    {
        return vsprintf('%s(%s)%s', [
            $this->getShortName(),
            $this->getSignatureParameters(),
            $this->getSignatureReturnType(),
        ]);
    }

    public function getSignature(): string
    {
        return vsprintf('%s::%s(%s)%s', [
            $this->getCurrentClass()->getName(),
            $this->getName(),
            $this->getSignatureParameters(),
            $this->getSignatureReturnType(),
        ]);
    }

    protected function getSignatureParameters(): string
    {
        $parameters = [];

        foreach ($this->getParameters() as $parameter) {
            $parts = [];
            $name = '';

            if ($parameter->isPassedByReference()) {
                $name .= '&';
            }

            if ($parameter->isVariadic()) {
                $name .= '...';
            }

            $name .= '$' . $parameter->getName();

            if ($parameter->hasType()) {
                $parts[] = (string) $parameter->getType();
            }

            $parts[] = $name;

            if ($parameter->isOptional() && !$parameter->isVariadic()) {
                $parts[] = '=';

                $default = $parameter->getDefaultValue();

                if ($parameter->isDefaultValueConstant()) {
                    $parts[] = $parameter->getDefaultValueConstantName();
                } elseif ($parameter->allowsNull()) {
                    $parts[] = 'null';
                } elseif ($parameter->isArray()) {
                    $parts[] = '[]';
                } elseif (is_bool($default)) {
                    $parts[] = $default ? 'true' : 'false';
                } elseif (is_int($default) || is_float($default)) {
                    $parts[] = $default;
                } else {
                    $parts[] = sprintf("'%s'", $default);
                }
            }

            $parameters[] = implode(' ', $parts);
        }

        return implode(', ', $parameters);
    }

    protected function getSignatureReturnType(): string
    {
        if (!$this->hasReturnType()) {
            return '';
        }

        $type = $this->getReturnType();

        if (is_object($type) && method_exists($type, 'getName')) {
            $name = $type->getName();
        } else {
            $name = (string) $type;
        }

        return ': ' . ($type->allowsNull() ? '?' : '') . $name;
    }
}
