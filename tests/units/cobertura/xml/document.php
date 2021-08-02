<?php declare(strict_types=1);

namespace atoum\atoum\reports\cobertura\tests\units\xml;

use atoum;
use atoum\atoum\reports\cobertura\reflection;
use atoum\atoum\reports\cobertura\score;
use atoum\atoum\reports\cobertura\tests\units\provider;
use atoum\atoum\reports\cobertura\xml\document as testedClass;

class document extends atoum\atoum\test
{
    use provider;

    public function test__toString()
    {
        $this
            ->xml((string) $this->newTestedInstance)
                ->root
                    ->attributes
                        ->hasSize(2)
                        ->hasKeys(['timestamp', 'version'])
                        ->string['version']
                            ->isIdenticalTo('1.0')
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

                ->xml((string) $this->testedInstance)
                    ->xpath('//packages')
                        ->hasSize(1)
                        ->first
                            ->isTag('packages')
                            ->attributes
                                ->hasSize(0)

                    ->xpath('//packages/package')
                        ->hasSize(1)
                        ->first
                            ->isTag('package')
                            ->attributes
                                ->hasSize(4)
                                ->string['name']->isIdenticalTo(atoum\atoum\reports\cobertura\xml::class)
                                ->string['branch-rate']->isIdenticalTo($branchRate)
                                ->string['line-rate']->isIdenticalTo($lineRate)
                                ->string['complexity']->isIdenticalTo('0')

                    ->xpath('//packages/package/classes')
                        ->hasSize(1)
                        ->first
                            ->isTag('classes')
                            ->attributes
                                ->hasSize(0)

                    ->xpath('//packages/package/classes/class')
                        ->hasSize(1)
                        ->first
                            ->isTag('class')
                            ->attributes
                                ->hasSize(5)
                                ->string['name']->isIdenticalTo('document')
                                ->string['filename']->isIdenticalTo('src/cobertura/xml/document.php')
                                ->string['branch-rate']->isIdenticalTo($branchRate)
                                ->string['line-rate']->isIdenticalTo($lineRate)
                                ->string['complexity']->isIdenticalTo('0')

                    ->xpath('//packages/package/classes/class/methods')
                        ->hasSize(1)
                        ->first
                            ->isTag('methods')
                            ->attributes
                                ->hasSize(0)

                    ->xpath('//packages/package/classes/class/methods/method')
                        ->hasSize(1)
                        ->first
                            ->isTag('method')
                            ->attributes
                                ->hasSize(5)
                                ->string['name']
                                    ->isIdenticalTo('addMethod')
                                ->string['signature']
                                    ->isIdenticalTo(vsprintf('%s::%s(%s $method, %s $coverage): self', [
                                        testedClass::class,
                                        'addMethod',
                                        reflection\method::class,
                                        score\coverage::class,
                                    ]))
                                ->string['branch-rate']
                                    ->isIdenticalTo($branchRate)
                                ->string['line-rate']
                                    ->isIdenticalTo($lineRate)
                                ->string['complexity']
                                    ->isIdenticalTo('0')

                    ->xpath('//packages/package/classes/class/methods/method/lines')
                        ->hasSize(1)
                        ->first
                            ->isTag('lines')
                            ->attributes
                                ->hasSize(0)
        ;

        $line = $this
            ->xml((string) $this->testedInstance)
                ->xpath('//packages/package/classes/class/methods/method/lines/line')
                    ->hasSize(count($filteredLines))
        ;

        foreach ($filteredLines as $lineNumber => $hit) {
            $line
                ->next
                    ->isTag('line')
                    ->attributes
                        ->hasSize(2)
                        ->hasKeys(['number', 'hits'])

                        ->string['number']
                            ->isEqualTo($lineNumber)
                        ->string['hits']
                            ->isEqualTo($hit)
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

                ->xml((string) $this->testedInstance)
                    ->xpath('//sources')
                        ->hasSize(1)
                        ->first
                        ->isTag('sources')
                            ->attributes
                                ->hasSize(0)

                    ->xpath('//sources/source')
                        ->hasSize(2)
                        ->first
                            ->isTag('source')
                            ->attributes
                                ->isEmpty
                            ->content
                                ->isIdenticalTo($path1)
                        ->next
                            ->isTag('source')
                            ->attributes
                                ->isEmpty
                            ->content
                                ->isIdenticalTo($path2)

                ->object($this->testedInstance->addSource($path3))
                    ->isTestedInstance

                ->xml((string) $this->testedInstance)
                    ->xpath('//sources/source')
                        ->hasSize(3)
                        ->first
                            ->isTag('source')
                            ->attributes
                                ->isEmpty
                            ->content
                                ->isIdenticalTo($path1)
                        ->next
                            ->isTag('source')
                            ->attributes
                                ->isEmpty
                            ->content
                                ->isIdenticalTo($path2)
                        ->next
                            ->isTag('source')
                            ->attributes
                                ->isEmpty
                            ->content
                                ->isIdenticalTo($path3)
        ;
    }

    public function testToXML()
    {
        $this
            ->xml($this->newTestedInstance->toXML())
                ->root
                    ->isTag('coverage')
                    ->attributes
                        ->hasSize(2)
                        ->hasKeys(['timestamp', 'version'])
                        ->string['version']
                            ->isIdenticalTo('1.0')
        ;
    }
}
