<?php


namespace Coff\SMF\Test;


use Coff\SMF\Assertion\CallbackAssertion;
use Coff\SMF\Exception\AssertionException;
use Coff\SMF\Transition\Transition;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CallbackAssertionTest extends TestCase
{
    /** @var CallbackAssertion */
    protected $assertion;

    /** @var SampleMachine|MockObject */
    protected $object;

    /** @var Transition|MockObject */
    protected $transition;


    public function setUp()
    {
        $this->assertion = new CallbackAssertion();
        $this->object = $this->createMock(SampleMachine::class);
        $this->transition = $this->createMock(Transition::class);
    }

    /**
     * @throws AssertionException
     */
    public function test_make()
    {
        $this->object
            ->method('test')// transition from state ONE to state TWO
            ->with($this->equalTo($this->object), $this->equalTo($this->transition), $this->equalTo('paramX'))
            ->willReturn(true);

        $this->assertion->setCallback([$this->object, 'test']);
        $this->assertion->setExtraParams(['paramX']);

        $this->assertEquals(true, $this->assertion->make($this->object, $this->transition));
    }

    /**
     * @throws AssertionException
     */
    public function test_make_not_configured()
    {
        $this->expectException(AssertionException::class);

        $this->assertion->make($this->object, $this->transition);
    }
}