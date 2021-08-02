<?php declare(strict_types=1);

namespace atoum\atoum\reports;

use atoum\atoum;
use atoum\atoum\reports\cobertura\reflection;
use atoum\atoum\reports\cobertura\score;
use atoum\atoum\reports\cobertura\xml;

class cobertura extends atoum\reports\coverage
{
    protected $score = null;

    public function build($event): self
    {
        if ($event !== atoum\runner::runStop) {
            return $this;
        }

        $document = new xml\document;
        $document->addSource($this->adapter->getcwd());

        $files = $this->score->getClasses();
        $methods = $this->score->getMethods() ?? [];
        $branches = $this->score->getBranches() ?? [];
        $paths = $this->score->getPaths() ?? [];
        $classes = $this->uniqueKeys($methods, $branches, $paths);

        foreach ($classes as $className) {
            if (!array_key_exists($className, $methods)) {
                $methods[$className] = [];
            }

            if (!array_key_exists($className, $branches)) {
                $branches[$className] = [];
            }

            if (!array_key_exists($className, $paths)) {
                $paths[$className] = [];
            }

            $classMethods = $this->uniqueKeys($methods[$className], $branches[$className], $paths[$className]);
            $reflectedClass = new reflection\klass($className);
            $namespace = $reflectedClass->getNamespaceName();

            foreach ($classMethods as $methodName) {
                $reflectedMethod = new reflection\method($className, $methodName);

                $methodCoverage = new score\coverage;
                $methodCoverage->branchesAreAvailable($branches[$className][$methodName] ?? []);
                $methodCoverage->linesAreAvailable($methods[$className][$methodName] ?? []);
                $methodCoverage->pathsAreAvailable($paths[$className][$methodName] ?? []);

                $document->addMethod($reflectedMethod, $methodCoverage);
            }
        }

        $this->string = $document->toXML();

        return $this;
    }

    public function handleEvent($event, atoum\observable $observable)
    {
        $this->score = ($event !== atoum\runner::runStop ? null : $observable->getScore());

        return parent::handleEvent($event, $observable);
    }

    public function uniqueKeys(array ...$data): array
    {
        return array_unique(array_keys(array_merge(...$data)));
    }
}
