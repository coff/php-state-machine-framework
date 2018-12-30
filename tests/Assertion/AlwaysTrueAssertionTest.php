<?php


namespace Coff\SMF\Test;


use Coff\SMF\Assertion\AlwaysTrueAssertion;
use Coff\SMF\Machine;
use Coff\SMF\Transition\Transition;
use PHPUnit\Framework\TestCase;

class AlwaysTrueAssertionTest extends TestCase
{
    /** @var AlwaysTrueAssertion */
    protected $assertion;

    public function setUp()
    {
        $this->assertion = new AlwaysTrueAssertion();
    }


    public function test_make()
    {
        /** @var Machine $machineMock */
        $machineMock = $this->createMock(SampleMachine::class);

        /** @var Transition $transitionMock */
        $transitionMock = $this->createMock(Transition::class);

        $this->assertEquals(true, $this->assertion->make($machineMock, $transitionMock));
    }
}