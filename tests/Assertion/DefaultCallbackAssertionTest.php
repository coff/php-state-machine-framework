<?php


namespace Coff\SMF\Test;


use Coff\SMF\Assertion\DefaultCallbackAssertion;
use Coff\SMF\Machine;
use Coff\SMF\Transition\Transition;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DefaultCallbackAssertionTest extends TestCase
{
    /** @var DefaultCallbackAssertion */
    protected $assertion;

    /** @var Transition */
    protected $transition;

    /** @var Machine|MockObject */
    protected $object;

    public function setUp()
    {
        $this->assertion = new DefaultCallbackAssertion();

        $this->object = $this->createMock(SampleMachine::class);
        $this->transition = new Transition(SampleStateEnum::ONE(), SampleStateEnum::TWO());
    }

    public function test_make()
    {
        $this->object
            ->method('assertOneToTwo')// transition from state ONE to state TWO
            ->with($this->equalTo($this->transition))
            ->willReturn(true);

        $this->assertEquals(true, $this->assertion->make($this->object, $this->transition));

    }
}