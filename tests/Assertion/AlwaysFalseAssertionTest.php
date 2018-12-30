<?php


namespace Coff\SMF\Test;


use Coff\SMF\Assertion\AlwaysFalseAssertion;
use Coff\SMF\Machine;
use Coff\SMF\Transition\Transition;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AlwaysFalseAssertionTest extends TestCase
{
    /** @var AlwaysFalseAssertion */
    protected $assertion;

    public function setUp()
    {
        $this->assertion = new AlwaysFalseAssertion();
    }


    public function test_make()
    {
        /** @var Machine|MockObject $machineMock */
        $machineMock = $this->createMock(SampleMachine::class);

        /** @var Transition|MockObject $transitionMock */
        $transitionMock = $this->createMock(Transition::class);

        $this->assertEquals(false, $this->assertion->make($machineMock, $transitionMock));
    }
}