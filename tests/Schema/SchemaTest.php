<?php

namespace Coff\SMF\Test;

use Coff\SMF\Exception\SchemaException;
use Coff\SMF\Schema\Schema;
use Coff\SMF\StateEnum;
use Coff\SMF\Transition\Transition;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    /** @var Schema */
    protected $object;

    /** @var StateEnum */
    protected $x, $y, $z;

    public function setUp()
    {
        $this->object = new Schema();
        $this->x = SampleStateEnum::ONE();
        $this->y = SampleStateEnum::TWO();
        $this->z = SampleStateEnum::THREE();
    }

    /**
     * @throws SchemaException
     */
    public function test_allowTransition()
    {
        $this->object->allowTransition($this->x, $this->y);

        $this->assertInstanceOf(Transition::class, $this->object->getTransition($this->x, $this->y));
    }

    public function test_isTransitionAllowed_no()
    {
        $this->object->allowTransition($this->x, $this->y);

        $this->assertFalse($this->object->isTransitionAllowed($this->x, $this->z));
    }

    public function test_isTransitionAllowed_yes()
    {
        $this->object->allowTransition($this->x, $this->y);

        $this->assertTrue($this->object->isTransitionAllowed($this->x, $this->y));
    }

    public function test_isTransitionAllowed_same()
    {
        // can't transit to the same state
        $this->assertFalse($this->object->isTransitionAllowed($this->x, $this->x));
    }
}