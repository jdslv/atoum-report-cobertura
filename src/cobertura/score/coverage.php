<?php declare(strict_types=1);

namespace atoum\atoum\reports\cobertura\score;

class coverage
{
    protected $totalLines;
    protected $coveredLines = 0;
    protected $totalBranches;
    protected $coveredBranches = 0;
    protected $totalPaths;
    protected $coveredPaths = 0;
    protected $totalOps;
    protected $coveredOps = [];
    protected $additionalCoveredOps = 0;
    protected $lines = [];
    protected $update;

    public function __get(string $property)
    {
        if ($property === 'coveredOps') {
            return count($this->coveredOps) + $this->additionalCoveredOps;
        }

        if (property_exists($this, $property)) {
            return $this->{$property} ?? 0;
        }

        $numerator = 0;
        $denominator = 0;

        switch ($property) {
            case 'branchRate':
                $numerator = $this->coveredBranches;
                $denominator = $this->totalBranches;
                break;

            case 'lineRate':
                $numerator = $this->coveredLines;
                $denominator = $this->totalLines;
                break;

            case 'opRate':
                $numerator = count($this->coveredOps) + $this->additionalCoveredOps;
                $denominator = $this->totalOps;
                break;

            case 'pathRate':
                $numerator = $this->coveredPaths;
                $denominator = $this->totalPaths;
                break;
        }

        if (!$denominator) {
            return (float) 0;
        }

        return $numerator / $denominator;
    }

    public function branchesAreAvailable(array $branches): self
    {
        foreach ($branches as $branch) {
            $this->branchIsAvailable($branch);
        }

        return $this;
    }

    public function branchIsAvailable(array $branch): self
    {
        if (!$this->totalOps) {
            $this->totalOps = 1;
        }

        if ($branch['op_end'] + 1 > $this->totalOps) {
            $this->totalOps = $branch['op_end'] + 1;
        }

        if ($branch['hit'] > 0) {
            $range = range($branch['op_start'], $branch['op_end']);
            $this->coveredOps = array_unique(array_merge($this->coveredOps, $range));
        }

        foreach ($branch['out_hit'] as $out) {
            $this->totalBranches++;
            $this->coveredBranches += $out > 0;
        }

        if ($this->update) {
            call_user_func($this->update, $this);
        }

        return $this;
    }

    public function linesAreAvailable(array $lines): self
    {
        foreach ($lines as $lineNumber => $hit) {
            if ($hit === -2) {
                continue;
            }

            $this->lines[$lineNumber] = $covered = $hit > 0;
            $this->totalLines++;

            if ($covered) {
                $this->coveredLines++;
            }
        }

        if ($this->update) {
            call_user_func($this->update, $this);
        }

        return $this;
    }

    public function merge(self ...$coverages): self
    {
        foreach ($coverages as $coverage) {
            if ($coverage->totalLines) {
                $this->totalLines += $coverage->totalLines;
                $this->coveredLines += $coverage->coveredLines;
            }

            if ($coverage->totalBranches) {
                $this->totalBranches += $coverage->totalBranches;
                $this->coveredBranches += $coverage->coveredBranches;
            }

            if ($coverage->totalPaths) {
                $this->totalPaths += $coverage->totalPaths;
                $this->coveredPaths += $coverage->coveredPaths;
            }

            if ($coverage->totalOps) {
                $this->totalOps += $coverage->totalOps;
                $this->additionalCoveredOps += count($coverage->coveredOps ?? []) + $coverage->additionalCoveredOps;
            }
        }

        if ($this->update) {
            call_user_func($this->update, $this);
        }

        return $this;
    }

    public function onUpdate(callable $update): self
    {
        $this->update = $update;

        return $this;
    }

    public function pathsAreAvailable(array $paths): self
    {
        $this->totalPaths += count($paths);

        $this->coveredPaths += count(array_filter($paths, function ($path) {
            return $path['hit'] > 0;
        }));

        if ($this->update) {
            call_user_func($this->update, $this);
        }

        return $this;
    }
}
