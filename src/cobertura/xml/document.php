<?php declare(strict_types=1);

namespace mageekguy\atoum\reports\cobertura\xml;

use DOMElement;
use DOMImplementation;
use mageekguy\atoum\adapter;
use mageekguy\atoum\reports\cobertura\reflection;
use mageekguy\atoum\reports\cobertura\score;

class document
{
    protected $adapter = null;
    protected $classes = [];
    protected $document;
    protected $lines = [];
    protected $methods = [];
    protected $packages = [];
    protected $root;
    protected $score;

    public function __construct()
    {
        $link = 'https://raw.githubusercontent.com/cobertura/cobertura/master/cobertura/src/site/htdocs/xml/coverage-04.dtd';

        $implem = new DOMImplementation;
        $dtd = $implem->createDocumentType('coverage', '', $link);

        $this->document = $implem->createDocument('', 'coverage', $dtd);
        $this->document->encoding = 'UTF-8';
        $this->document->formatOutput = true;
        $this->document->standalone = true;

        $this->root = $this->document->documentElement;
        $this->root->setAttribute('version', '1.0');
        $this->root->setAttribute('timestamp', (string) time());

        $this->score = new score\coverage;
        $this->score->onUpdate(function ($score) {
            $this->root->setAttribute('line-rate', $this->formatRate($score->lineRate));
            $this->root->setAttribute('lines-covered', (string) $score->coveredLines);
            $this->root->setAttribute('lines-valid', (string) $score->totalLines);
            $this->root->setAttribute('branch-rate', $this->formatRate($score->branchRate));
            $this->root->setAttribute('branches-covered', (string) $score->coveredBranches);
            $this->root->setAttribute('branches-valid', (string) $score->totalBranches);
            $this->root->setAttribute('complexity', '0');
        });

        $comments = [
            'Generated by jdslv/atoum-report-cobertura',
            date('r'),
        ];
        $reduce = function($carry, $item) {
            return max($carry, strlen($item) + 2);
        };
        $len = array_reduce($comments, $reduce, 0);

        foreach ($comments as $comment) {
            $this->root->appendChild($this->document->createComment(str_pad($comment, $len, ' ', STR_PAD_BOTH)));
        }

        $this->setAdapter();
    }

    public function __toString(): string
    {
        return $this->toXML();
    }

    public function addMethod(reflection\method $method, score\coverage $coverage): self
    {
        $klass = $method->getCurrentClass();
        $name = $method->getFullName();

        $this->getClassScore($klass)->merge($coverage);

        if (array_key_exists($name, $this->methods)) {
            $elem = $this->methods[$name];
        } else {
            $elem = $this->document->createElement('method');

            $elem->setAttribute('name', $method->getName());
            $elem->setAttribute('signature', $method->getSignature());
            $elem->setAttribute('branch-rate', $this->formatRate($coverage->branchRate));
            $elem->setAttribute('line-rate', $this->formatRate($coverage->lineRate));
            $elem->setAttribute('complexity', '0');
            $this->getMethodsElement($klass)->appendChild($elem);

            $this->methods[$name] = $elem;
        }

        $lines = $this->getElement($elem, 'lines');

        if (!array_key_exists($name, $this->lines)) {
            $this->lines[$name] = [];
        }

        foreach ($coverage->lines as $lineNumber => $hit) {
            if (!array_key_exists($lineNumber, $this->lines[$name])) {
                $line = $this->document->createElement('line');

                $line->setAttribute('number', (string) $lineNumber);
                $line->setAttribute('hits', '0');
                $lines->appendChild($line);

                $this->lines[$name][$lineNumber] = $line;
            }

            $line = $this->lines[$name][$lineNumber];
            $hits = $hit + (int) $line->getAttribute('hits');

            $line->setAttribute('hits', (string) $hits);
        }

        return $this;
    }

    public function addSource(string ...$sources): self
    {
        $elem = $this->document->getElementsByTagName('sources');

        if (!$elem->length) {
            $parent = $this->document->createElement('sources');
            $this->root->appendChild($parent);
        } else {
            $parent = $elem[0];
        }

        foreach ($sources as $source) {
            $parent->appendChild($this->document->createElement('source', $source));
        }

        return $this;
    }

    public function formatRate(float $rate): string
    {
        return (string) round($rate, 5);
    }

    protected function getClassData(reflection\klass $klass): array
    {
        if (!array_key_exists($klass->getShortName(), $this->classes)) {
            $elem = $this->document->createElement('class');

            $elem->setAttribute('name', $klass->getShortName());
            $elem->setAttribute('filename', $klass->getFileName($this->adapter->getcwd()));
            $this->getClassesElement($klass)->appendChild($elem);

            $score = new score\coverage;
            $score->onUpdate(function ($s) use ($elem, $klass) {
                $elem->setAttribute('branch-rate', $this->formatRate($s->branchRate));
                $elem->setAttribute('line-rate', $this->formatRate($s->lineRate));
                $elem->setAttribute('complexity', '0');
                $this->getPackageScore($klass)->merge($s);
            });

            $this->classes[$klass->getShortName()] = [
                'element' => $elem,
                'score' => $score,
            ];
        }

        return $this->classes[$klass->getShortName()];
    }

    protected function getClassElement(reflection\klass $klass): DOMElement
    {
        return $this->getClassData($klass)['element'];
    }

    protected function getClassScore(reflection\klass $klass): score\coverage
    {
        return $this->getClassData($klass)['score'];
    }

    protected function getClassesElement(reflection\klass $klass): DOMElement
    {
        return $this->getElement($this->getPackageElement($klass), 'classes');
    }

    protected function getElement(DOMElement $source, string $type): DOMElement
    {
        $list = $source->getElementsByTagName($type);

        if (!$list->length) {
            $elem = $this->document->createElement($type);
            $source->appendChild($elem);
        } else {
            $elem = $list[0];
        }

        return $elem;
    }

    protected function getMethodsElement(reflection\klass $klass): DOMElement
    {
        return $this->getElement($this->getClassElement($klass), 'methods');
    }

    protected function getPackageData(reflection\klass $klass): array
    {
        $ns = $klass->getNamespaceName();

        if (!array_key_exists($ns, $this->packages)) {
            $elem = $this->getElement($this->getPackagesElement(), 'package');

            $elem->setAttribute('name', $ns);

            $score = new score\coverage;
            $score->onUpdate(function ($s) use ($elem) {
                $elem->setAttribute('branch-rate', $this->formatRate($s->branchRate));
                $elem->setAttribute('line-rate', $this->formatRate($s->lineRate));
                $elem->setAttribute('complexity', '0');
                $this->score->merge($s);
            });

            $this->packages[$ns] = [
                'element' => $elem,
                'score' => $score,
            ];
        }

        return $this->packages[$ns];
    }

    protected function getPackageElement(reflection\klass $klass): DOMElement
    {
        return $this->getPackageData($klass)['element'];
    }

    protected function getPackageScore(reflection\klass $klass): score\coverage
    {
        return $this->getPackageData($klass)['score'];
    }

    protected function getPackagesElement(): DOMElement
    {
        return $this->getElement($this->root, 'packages');
    }

    public function setAdapter(adapter $adapter = null)
    {
        $this->adapter = $adapter ?: new adapter;

        return $this;
    }

    public function toXML(): string
    {
        return $this->document->saveXML();
    }
}