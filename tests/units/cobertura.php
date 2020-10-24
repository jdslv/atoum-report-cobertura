<?php declare(strict_types=1);

namespace atoum\reports\tests\units;

use mageekguy\atoum;

class cobertura extends atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(atoum\reports\coverage::class)
        ;
    }
}
