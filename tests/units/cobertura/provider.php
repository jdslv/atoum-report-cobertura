<?php declare(strict_types=1);

namespace mageekguy\atoum\reports\cobertura\tests\units;

trait provider
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
        $filteredLines = [];
        $covered = $total = 0;

        for ($idx = 0; $idx < 20; $idx++) {
            $rand = random_int(0, 2);
            $item = 1;

            if ($rand) {
                $item = $rand * -1;
            }

            if ($item > -2) {
                $total++;
                $filteredLines[$idx] = (int) $item > 0;
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
            $filteredLines,
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
}
