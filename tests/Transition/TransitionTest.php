<?php


namespace Coff\SMF\Test;

use Coff\SMF\Assertion\AlwaysTrueAssertion;
use Coff\SMF\Assertion\Assertion;
use Coff\SMF\Machine;
use Coff\SMF\Transition\Transition;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TransitionTest extends TestCase
{
    /** @var Machine|MockObject */
    protected $machine;

    /** @var Transition|MockObject */
    protected $transition;

    /** @var Assertion|MockObject */
    protected $assertion;

    public function setUp()
    {
        $this->machine = $this->createPartialMock(SampleMachine::class, ['onTransition']);
        $this->transition = $this->createPartialMock(Transition::class, ['onTransition']);
        $this->assertion = $this->createPartialMock(AlwaysTrueAssertion::class, ['make']);
    }


    public function test_assert_w_no_assertions() {

        $result = $this->transition->assert($this->machine);

        $this->assertFalse($result);
    }

    public function test_assert_w_assertion_true() {

        $this->transition->expects($this->once())
            ->method('onTransition')
            ->with($this->equalTo($this->machine));

        $this->assertion->expects($this->once())
            ->method( 'make')
            ->with($this->equalTo($this->machine), $this->equalTo($this->transition))
            ->willReturn(true);

        $this->transition->addAssertion($this->assertion);

        $result = $this->transition->assert($this->machine);

        $this->assertTrue($result);
    }

    public function test_assert_w_assertion_false() {

        $this->assertion->expects($this->once())
            ->method( 'make')
            ->with($this->equalTo($this->machine), $this->equalTo($this->transition))
            ->willReturn(false);

        $this->transition->addAssertion($this->assertion);

        $result = $this->transition->assert($this->machine);

        $this->assertFalse($result);
    }
}