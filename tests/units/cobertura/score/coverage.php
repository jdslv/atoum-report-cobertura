<?php declare(strict_types=1);

namespace mageekguy\atoum\reports\cobertura\tests\units\score;

use mageekguy\atoum;

class coverage extends atoum\test
{
    public function branchesData()
    {
        $branches = [];
        $branchesCovered = $branchesTotal = 0;
        $opsCovered = $opsTotal = 0;

        $ops = [];

        for ($idx = 0; $idx < 10; $idx++) {
            $randOp = random_int(1, 5);
            $hit = random_int(0, 1);

            $branches[$opsTotal] = [
                'op_start' => $opsTotal,
                'op_end' => $opsTotal + $randOp,
                'line_start' => 0,
                'line_end' => 0,
                'hit' => $hit,
                'out' => [],
                'out_hit' => [],
            ];

            for ($i = 0; $i <= $randOp; $i++) {
                $branches[$opsTotal]['out'][] = random_int(1, 9);
                $branches[$opsTotal]['out_hit'][] = $h = random_int(0, 1);

                $branchesCovered += $h;
                $branchesTotal++;
            }

            if ($hit) {
                $ops = array_unique(array_merge($ops, range($opsTotal, $opsTotal + $randOp)));
            }

            $opsTotal += $randOp + 1;
            $opsCovered = count($ops);
        }

        return [
            $branches,
            $branchesCovered,
            $branchesTotal,
            $opsCovered,
            $opsTotal,
        ];
    }

    public function linesData()
    {
        $lines = [];
        $covered = $total = 0;

        for ($idx = 0; $idx < 20; $idx++) {
            $rand = random_int(0, 2);
            $item = 1;

            if ($rand) {
                $item = $rand * -1;
            }

            if ($item > -2) {
                $total++;
            }

            if ($item > 0) {
                $covered++;
            }

            $lines[] = $item;
        }

        return [
            $lines,
            $covered,
            $total,
        ];
    }

    public function pathsData()
    {
        $paths = [];
        $covered = $total = 0;

        for ($idx = 0; $idx < 10; $idx++) {
            $rand = random_int(1, 5);
            $hit = random_int(0, 1);

            $total++;
            $covered += $hit;

            $paths[] = [
                'path' => range(0, $rand),
                'hit' => $hit,
            ];
        }

        return [
            $paths,
            $covered,
            $total,
        ];
    }

    public function test__construct()
    {
        $this
            ->if($this->newTestedInstance)
            ->then
                ->assert('Lines')
                    ->integer($this->testedInstance->coveredLines)
                        ->isZero

                    ->integer($this->testedInstance->totalLines)
                        ->isZero

                    ->float($this->testedInstance->lineRate)
                        ->isZero

                ->assert('Branches')
                    ->integer($this->testedInstance->coveredBranches)
                        ->isZero

                    ->integer($this->testedInstance->totalBranches)
                        ->isZero

                    ->float($this->testedInstance->branchRate)
                        ->isZero

                ->assert('Paths')
                    ->integer($this->testedInstance->coveredPaths)
                        ->isZero

                    ->integer($this->testedInstance->totalPaths)
                        ->isZero

                    ->float($this->testedInstance->pathRate)
                        ->isZero

                ->assert('Ops')
                    ->integer($this->testedInstance->coveredOps)
                        ->isZero

                    ->integer($this->testedInstance->totalOps)
                        ->isZero

                    ->float($this->testedInstance->opRate)
                        ->isZero
        ;
    }

    public function testBranchesAreAvailable()
    {
        $this
            ->if($this->newTestedInstance)
            ->and([$branches, $branchesCovered, $branchesTotal, $opsCovered, $opsTotal] = $this->branchesData())
            ->then
                ->object($this->testedInstance->branchesAreAvailable([]))
                    ->isTestedInstance

                ->integer($this->testedInstance->coveredBranches)
                    ->isZero

                ->integer($this->testedInstance->totalBranches)
                    ->isZero

                ->float($this->testedInstance->branchRate)
                    ->isZero

                ->integer($this->testedInstance->coveredOps)
                    ->isZero

                ->integer($this->testedInstance->totalOps)
                    ->isZero

                ->float($this->testedInstance->opRate)
                    ->isZero

                ->object($this->testedInstance->branchesAreAvailable($branches))
                    ->isTestedInstance

                ->integer($this->testedInstance->coveredBranches)
                    ->isEqualTo($branchesCovered)

                ->integer($this->testedInstance->totalBranches)
                    ->isEqualTo($branchesTotal)

                ->float($this->testedInstance->branchRate)
                    ->isEqualTo($branchesCovered / $branchesTotal)

                ->integer($this->testedInstance->coveredOps)
                    ->isEqualTo($opsCovered)

                ->integer($this->testedInstance->totalOps)
                    ->isEqualTo($opsTotal)

                ->float($this->testedInstance->opRate)
                    ->isEqualTo($opsCovered / $opsTotal)
        ;
    }

    public function testLinesAreAvailable()
    {
        $this
            ->if($this->newTestedInstance)
            ->and([$lines, $covered, $total] = $this->linesData())
            ->and($filteredLines = [])
            ->when(function() use ($lines, &$filteredLines) {
                foreach ($lines as $lineNumber => $hit) {
                    if ($hit > -2) {
                        $filteredLines[$lineNumber] = $hit > 0;
                    }
                }
            })
            ->then
                ->object($this->testedInstance->linesAreAvailable([]))
                    ->isTestedInstance

                ->integer($this->testedInstance->coveredLines)
                    ->isZero

                ->integer($this->testedInstance->totalLines)
                    ->isZero

                ->float($this->testedInstance->lineRate)
                    ->isZero

                ->object($this->testedInstance->linesAreAvailable($lines))
                    ->isTestedInstance

                ->integer($this->testedInstance->coveredLines)
                    ->isEqualTo($covered)

                ->integer($this->testedInstance->totalLines)
                    ->isEqualTo($total)

                ->float($this->testedInstance->lineRate)
                    ->isEqualTo($covered / $total)

                ->array($this->testedInstance->lines)
                    ->isEqualTo($filteredLines)
        ;
    }

    public function testMerge()
    {
        $this
            ->given([$branches, $branchesCovered, $branchesTotal, $opsCovered, $opsTotal] = $this->branchesData())
            ->and([$lines, $linesCovered, $linesTotal] = $this->linesData())
            ->and([$paths, $pathsCovered, $pathsTotal] = $this->pathsData())

            ->if($obj1 = $this->newTestedInstance)
            ->and($obj1->branchesAreAvailable($branches))

            ->if($obj2 = $this->newTestedInstance)
            ->and($obj2->linesAreAvailable($lines))

            ->if($obj3 = $this->newTestedInstance)
            ->and($obj3->pathsAreAvailable($paths))

            ->if($this->newTestedInstance)
            ->then
                ->object($this->testedInstance->merge($obj1, $obj2, $obj3))
                    ->isTestedInstance

                ->integer($this->testedInstance->coveredLines)
                    ->isIdenticalTo($linesCovered)

                ->integer($this->testedInstance->totalLines)
                    ->isIdenticalTo($linesTotal)

                ->float($this->testedInstance->lineRate)
                    ->isIdenticalTo($linesCovered / $linesTotal)

                ->integer($this->testedInstance->coveredBranches)
                    ->isIdenticalTo($branchesCovered)

                ->integer($this->testedInstance->totalBranches)
                    ->isIdenticalTo($branchesTotal)

                ->float($this->testedInstance->branchRate)
                    ->isIdenticalTo($branchesCovered / $branchesTotal)

                ->integer($this->testedInstance->coveredPaths)
                    ->isIdenticalTo($pathsCovered)

                ->integer($this->testedInstance->totalPaths)
                    ->isIdenticalTo($pathsTotal)

                ->float($this->testedInstance->pathRate)
                    ->isIdenticalTo($pathsCovered / $pathsTotal)

                ->integer($this->testedInstance->coveredOps)
                    ->isIdenticalTo($opsCovered)

                ->integer($this->testedInstance->totalOps)
                    ->isIdenticalTo($opsTotal)

                ->float($this->testedInstance->opRate)
                    ->isIdenticalTo($opsCovered / $opsTotal)

                ->object($this->testedInstance->merge($obj1))
                    ->isTestedInstance

                ->integer($this->testedInstance->coveredOps)
                    ->isIdenticalTo($opsCovered * 2)

                ->integer($this->testedInstance->totalOps)
                    ->isIdenticalTo($opsTotal * 2)

                ->float($this->testedInstance->opRate)
                    ->isIdenticalTo($opsCovered / $opsTotal)
        ;
    }

    public function testOnUpdate()
    {
        $this
            ->given([$branches, $branchesCovered, $branchesTotal, $opsCovered, $opsTotal] = $this->branchesData())
            ->and([$lines, $linesCovered, $linesTotal] = $this->linesData())
            ->and([$paths, $pathsCovered, $pathsTotal] = $this->pathsData())

            ->and($this->function->call_user_func = uniqid())

            ->if($callable = 'get_class')
            ->if($closure = function () {})
            ->and($class = new class {
                public function method() {}
            })

            ->if($this->newTestedInstance)
            ->then
                ->assert('With a callable string')
                    ->object($this->testedInstance->onUpdate($callable))
                        ->isTestedInstance

                    ->assert('With a callable string / On branches')
                        ->object($this->testedInstance->branchesAreAvailable($branches))
                            ->isTestedInstance

                        ->function('call_user_func')
                            ->wasCalledWithIdenticalArguments($callable, $this->testedInstance)
                                ->atLeastOnce

                    ->assert('With a callable string / On lines')
                        ->object($this->testedInstance->linesAreAvailable($lines))
                            ->isTestedInstance

                        ->function('call_user_func')
                            ->wasCalledWithIdenticalArguments($callable, $this->testedInstance)
                                ->once

                    ->assert('With a callable string / On paths')
                        ->object($this->testedInstance->pathsAreAvailable($paths))
                            ->isTestedInstance

                        ->function('call_user_func')
                            ->wasCalledWithIdenticalArguments($callable, $this->testedInstance)
                                ->once

                ->assert('With an anonymous function')
                    ->object($this->testedInstance->onUpdate($closure))
                        ->isTestedInstance

                    ->assert('With an anonymous function / On branches')
                        ->object($this->testedInstance->branchesAreAvailable($branches))
                            ->isTestedInstance

                        ->function('call_user_func')
                            ->wasCalledWithIdenticalArguments($closure, $this->testedInstance)
                                ->atLeastOnce

                    ->assert('With an anonymous function / On lines')
                        ->object($this->testedInstance->linesAreAvailable($lines))
                            ->isTestedInstance

                        ->function('call_user_func')
                            ->wasCalledWithIdenticalArguments($closure, $this->testedInstance)
                                ->once

                    ->assert('With an anonymous function / On paths')
                        ->object($this->testedInstance->pathsAreAvailable($paths))
                            ->isTestedInstance

                        ->function('call_user_func')
                            ->wasCalledWithIdenticalArguments($closure, $this->testedInstance)
                                ->once

                ->assert('With a method')
                    ->object($this->testedInstance->onUpdate([$class, 'method']))
                        ->isTestedInstance

                    ->assert('With a method / On branches')
                        ->object($this->testedInstance->branchesAreAvailable($branches))
                            ->isTestedInstance

                        ->function('call_user_func')
                            ->wasCalledWithIdenticalArguments([$class, 'method'], $this->testedInstance)
                                ->atLeastOnce

                    ->assert('With a method / On lines')
                        ->object($this->testedInstance->linesAreAvailable($lines))
                            ->isTestedInstance

                        ->function('call_user_func')
                            ->wasCalledWithIdenticalArguments([$class, 'method'], $this->testedInstance)
                                ->once

                    ->assert('With a method / On paths')
                        ->object($this->testedInstance->pathsAreAvailable($paths))
                            ->isTestedInstance

                        ->function('call_user_func')
                            ->wasCalledWithIdenticalArguments([$class, 'method'], $this->testedInstance)
                                ->once
        ;
    }

    public function testPathsAreAvailable()
    {
        $this
            ->if($this->newTestedInstance)
            ->and([$paths, $covered, $total] = $this->pathsData())
            ->then
                ->object($this->testedInstance->pathsAreAvailable([]))
                    ->isTestedInstance

                ->integer($this->testedInstance->coveredPaths)
                    ->isZero

                ->integer($this->testedInstance->totalPaths)
                    ->isZero

                ->float($this->testedInstance->pathRate)
                    ->isZero

                ->object($this->testedInstance->pathsAreAvailable($paths))
                    ->isTestedInstance

                ->integer($this->testedInstance->coveredPaths)
                    ->isEqualTo($covered)

                ->integer($this->testedInstance->totalPaths)
                    ->isEqualTo($total)

                ->float($this->testedInstance->pathRate)
                    ->isEqualTo($covered / $total)
        ;
    }
}
