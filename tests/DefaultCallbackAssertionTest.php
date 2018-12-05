<?php


namespace Coff\SMF\Test;


use Coff\SMF\Assertion\DefaultCallbackAssertion;
use Coff\SMF\Exception\AssertionException;
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
        $this->transition = new Transition(SampleStateEnum::ONE(), SampleStateEnum::TWO());

        $this->object = $this->createMock(SampleMachine::class);
    }

    /**
     * @throws AssertionException
     */
    public function test_make()
    {
        $this->assertion->setObject($this->object);
        $this->assertion->setTransition($this->transition);

        $this->object
            ->method('assertOneToTwo')// transition from state ONE to state TWO
            ->with($this->equalTo($this->transition))
            ->willReturn(true);

        $this->assertEquals(true, $this->assertion->make());
    }

    /**
     * @throws AssertionException
     */
    public function test_make_not_configured()
    {
        $this->expectException(AssertionException::class);

        $this->assertion->make();
    }
}