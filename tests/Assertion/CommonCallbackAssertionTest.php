<?php


namespace Coff\SMF\Test;


use Coff\SMF\Assertion\CommonCallbackAssertion;
use Coff\SMF\Machine;
use Coff\SMF\Transition\Transition;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommonCallbackAssertionTest extends TestCase
{
    /** @var CommonCallbackAssertion */
    protected $assertion;

    /** @var Transition */
    protected $transition;

    /** @var Machine|MockObject */
    protected $object;

    public function setUp()
    {
        $this->assertion = new CommonCallbackAssertion();
        $this->transition = new Transition(SampleStateEnum::ONE(), SampleStateEnum::TWO());

        $this->object = $this->createMock(SampleMachine::class);
    }

    /**
     *
     */
    public function test_make()
    {
        $this->object
            ->method('assertTransition')
            ->with($this->equalTo($this->transition))
            ->willReturn(true);

        $this->assertEquals(true, $this->assertion->make($this->object, $this->transition));
    }

}