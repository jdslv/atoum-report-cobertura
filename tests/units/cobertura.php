<?php declare(strict_types=1);

namespace atoum\atoum\reports\tests\units;

use atoum;

class cobertura extends atoum\atoum\test
{
    public function testClass()
    {
        $this
            ->testedClass
                ->extends(atoum\atoum\reports\coverage::class)
        ;
    }
}
