<?php declare(strict_types=1);

namespace mageekguy\atoum\reports\cobertura\tests\units\xml;

use mageekguy\atoum;
use mageekguy\atoum\reports\cobertura\reflection;
use mageekguy\atoum\reports\cobertura\score;
use mageekguy\atoum\reports\cobertura\tests\units\provider;
use mageekguy\atoum\reports\cobertura\xml\document as testedClass;

class document extends atoum\test
{
    use provider;

    public function test__toString()
    {
        $this
            ->xml((string) $this->newTestedInstance)
                ->xpath('/coverage')
                    ->hasSize(1)
                    ->item(0)
                        ->attributes()
                            ->hasSize(2)
                            ->string['version']->isIdenticalTo('1.0')
                            ->string['timestamp']
        ;
    }

    public function testAddMethod()
    {
        $this
            ->given($method = new reflection\method(testedClass::class, 'addMethod'))

            ->given([$branches, $branchesCovered, $branchesTotal, $opsCovered, $opsTotal] = $this->branchesData())
            ->and([$lines, $linesCovered, $linesTotal, $filteredLines] = $this->linesData())

            ->and($score = new score\coverage)
            ->and($score->branchesAreAvailable($branches))
            ->and($score->linesAreAvailable($lines))

            ->and($branchRate = (string) round($branchesCovered / $branchesTotal, 5))
            ->and($lineRate = (string) round($linesCovered / $linesTotal, 5))

            ->if($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->addMethod($method, $score))
                    ->isTestedInstance
        ;

        // Split forced by xml extension

        $xml = $this->xml((string) $this->testedInstance);

        // Packages
        $xml
            ->xpath('//packages')
                ->hasSize(1)
                ->item(0)
                    ->attributes()
                        ->hasSize(0)
        ;

        $xml
            ->xpath('//packages/package')
                ->hasSize(1)
                ->item(0)
                    ->attributes()
                        ->hasSize(4)
                        ->string['name']->isIdenticalTo('mageekguy\atoum\reports\cobertura\xml')
                        ->string['branch-rate']->isIdenticalTo($branchRate)
                        ->string['line-rate']->isIdenticalTo($lineRate)
                        ->string['complexity']->isIdenticalTo('0')
        ;

        // Classes
        $xml
            ->xpath('//packages/package/classes')
                ->hasSize(1)
                ->item(0)
                    ->attributes()
                        ->hasSize(0)
        ;

        $xml
            ->xpath('//packages/package/classes/class')
                ->hasSize(1)
                ->item(0)
                    ->attributes()
                        ->hasSize(5)
                        ->string['name']->isIdenticalTo('document')
                        ->string['filename']->isIdenticalTo('src/cobertura/xml/document.php')
                        ->string['branch-rate']->isIdenticalTo($branchRate)
                        ->string['line-rate']->isIdenticalTo($lineRate)
                        ->string['complexity']->isIdenticalTo('0')
        ;

        // Methodes
        $xml
            ->xpath('//packages/package/classes/class/methods')
                ->hasSize(1)
                ->item(0)
                    ->attributes()
                        ->hasSize(0)
        ;

        $xml
            ->xpath('//packages/package/classes/class/methods/method')
                ->hasSize(1)
                ->item(0)
                    ->attributes()
                        ->hasSize(5)
                        ->string['name']->isIdenticalTo('addMethod')
                        ->string['signature']->isIdenticalTo(vsprintf('%s::addMethod(%s $method, %s $coverage): self', [
                            testedClass::class,
                            reflection\method::class,
                            score\coverage::class,
                        ]))
                        ->string['branch-rate']->isIdenticalTo($branchRate)
                        ->string['line-rate']->isIdenticalTo($lineRate)
                        ->string['complexity']->isIdenticalTo('0')
        ;

        // Lines
        $xml
            ->xpath('//packages/package/classes/class/methods/method/lines')
                ->hasSize(1)
                ->item(0)
                    ->attributes()
                        ->hasSize(0)
        ;

        $linesElements = $xml
            ->xpath('//packages/package/classes/class/methods/method/lines/line')
                ->hasSize(count($filteredLines))
        ;

        $idx = 0;
        foreach ($filteredLines as $lineNumber => $hit) {
            $linesElements
                ->item($idx++)
                    ->attributes()
                        ->hasSize(2)
                        ->string['number']->isEqualTo($lineNumber)
                        ->string['hits']->isEqualTo($hit)
            ;
        }
    }

    public function testAddSource()
    {
        $this
            ->if($this->newTestedInstance)
            ->and($path1 = uniqid())
            ->and($path2 = uniqid())
            ->and($path3 = uniqid())
            ->then
                ->object($this->testedInstance->addSource($path1, $path2))
                    ->isTestedInstance
        ;

        // Split forced by xml extension

        $xml = $this->xml((string) $this->testedInstance);

        $xml
            ->xpath('//sources')
                ->hasSize(1)
                ->item(0)
                    ->attributes()
                        ->hasSize(0)
        ;

        $source = $xml->xpath('//sources/source')->hasSize(2);

        $source->item(0)->attributes()->hasSize(0);
        $source->item(0)->nodeValue->isIdenticalTo($path1);
        $source->item(1)->attributes()->hasSize(0);
        $source->item(1)->nodeValue->isIdenticalTo($path2);

        $this->testedInstance->addSource($path3);

        $xml = $this->xml((string) $this->testedInstance);

        $xml
            ->xpath('//sources')
                ->hasSize(1)
                ->item(0)
                    ->attributes()
                        ->hasSize(0)
        ;

        $source = $xml->xpath('//sources/source')->hasSize(3);

        $source->item(0)->attributes()->hasSize(0);
        $source->item(0)->nodeValue->isIdenticalTo($path1);
        $source->item(1)->attributes()->hasSize(0);
        $source->item(1)->nodeValue->isIdenticalTo($path2);
        $source->item(2)->attributes()->hasSize(0);
        $source->item(2)->nodeValue->isIdenticalTo($path3);
    }

    public function testToXML()
    {
        $this
            ->xml($this->newTestedInstance->toXML())
                ->xpath('/coverage')
                    ->hasSize(1)
                    ->item(0)
                        ->attributes()
                            ->hasSize(2)
                            ->string['version']->isIdenticalTo('1.0')
                            ->string['timestamp']
        ;
    }
}
