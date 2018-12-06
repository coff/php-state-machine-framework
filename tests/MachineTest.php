<?php


namespace Coff\SMF\Test;


use Coff\SMF\Assertion\AlwaysFalseAssertion;
use Coff\SMF\Assertion\AlwaysTrueAssertion;
use Coff\SMF\Assertion\CommonCallbackAssertion;
use Coff\SMF\Assertion\DefaultCallbackAssertion;
use Coff\SMF\Exception\MachineException;
use Coff\SMF\Exception\TransitionException;
use Coff\SMF\Transition\Transition;
use PHPUnit\Framework\TestCase;

class MachineTest extends TestCase
{
    /** @var SampleMachine */
    protected $machine;

    /** @var SampleStateEnum */
    protected $x, $y, $z;

    public function setUp()
    {
        $this->machine = new SampleMachine();
        $this->x = SampleStateEnum::ONE();
        $this->y = SampleStateEnum::TWO();
        $this->z = SampleStateEnum::THREE();
    }

    public function test_setInitState()
    {
        $this->machine->setInitState($this->x);

        $this->assertInstanceOf(SampleStateEnum::class, $this->machine->getInitState());
        $this->assertEquals($this->x, $this->machine->getInitState());
    }

    /**
     * @depends test_setInitState
     */
    public function test_setInitState_sets_state_on_no_state()
    {
        $this->machine->setInitState($this->x);

        $this->assertInstanceOf(SampleStateEnum::class, $this->machine->getMachineState());
        $this->assertEquals($this->x, $this->machine->getMachineState());
    }

    /**
     * @depends test_setInitState_sets_state_on_no_state
     */
    public function test_setInitState_doesnt_set_state_when_has_state()
    {

        // sets initial state and current
        $this->machine->setInitState($this->x);

        // should only set initial state but not current one
        $this->machine->setInitState($this->y);

        $this->assertEquals($this->x, $this->machine->getMachineState());
        $this->assertEquals($this->y, $this->machine->getInitState());
    }

    /**
     * @depends test_setInitState
     */
    public function test_isMachineState()
    {
        $this->machine->setInitState($x = SampleStateEnum::ONE());

        $this->assertEquals(true, $this->machine->isMachineState($x));
    }

    /**
     * @depends test_setInitState
     * @depends test_isMachineState
     */
    public function test_isMachineState_one_state_not_same_object()
    {
        $this->machine->setInitState(SampleStateEnum::ONE());

        $this->assertEquals(true, $this->machine->isMachineState(SampleStateEnum::ONE()));
    }

    /**
     * @throws MachineException
     */
    public function test_allowTransition()
    {
        $this->machine->allowTransition($this->x, $this->y);

        $this->assertInstanceOf(Transition::class, $this->machine->getTransition($this->x, $this->y));
    }

    /**
     * @depends test_setInitState
     * @throws TransitionException
     * @throws MachineException
     */
    public function test_setMachineState_not_allowed_transition()
    {

        $this->machine->setInitState(SampleStateEnum::ONE());
        $this->expectException(TransitionException::class);
        $this->machine->setMachineState(SampleStateEnum::TWO());
    }

    /**
     * @depends test_allowTransition
     * @depends test_setInitState
     * @throws MachineException
     * @throws TransitionException
     */
    public function test_setMachineState_calls_onTransition()
    {
        $this->machine = $this->createPartialMock(SampleMachine::class, ['onTransition']);

        $this->machine->allowTransition($this->x, $this->y);

        $transition = $this->machine->getTransition($this->x, $this->y);

        $this->machine->expects($this->once())
            ->method('onTransition')
            ->with($this->equalTo($transition));

        $this->machine->setInitState($this->x);

        $this->machine->setMachineState($this->y);
    }

    /**
     * @depends test_setInitState
     */
    public function test_isTransitionAllowed()
    {
        $this->machine->setInitState($this->x);
        $this->machine->allowTransition($this->x, $this->y);

        $this->assertTrue($this->machine->isTransitionAllowed($this->y));
    }

    /**
     * @depends test_setInitState
     */
    public function test_isTransitionAllowed_not()
    {
        $this->machine->setInitState($this->x);
        $this->machine->allowTransition($this->x, $this->y);

        $this->assertFalse($this->machine->isTransitionAllowed($this->z));
    }

    /**
     * @depends test_setInitState
     */
    public function test_isTransitionAllowed_same()
    {
        $this->machine->setInitState($this->x);

        // can't transit to the same state
        $this->assertFalse($this->machine->isTransitionAllowed($this->x));
    }

    /**
     * @depends test_setInitState
     * @depends test_allowTransition
     * @throws TransitionException
     * @throws MachineException
     */
    public function test_run_automatic_transition_through_n_states_succ()
    {
        $this->machine = $this->createPartialMock(SampleMachine::class, ['onTransition']);

        $this->machine->setInitState($this->x);

        $this->machine->allowTransition($this->x, $this->y, new AlwaysTrueAssertion());
        $this->machine->allowTransition($this->y, $this->z, new AlwaysTrueAssertion());


        $transition1 = $this->machine->getTransition($this->x, $this->y);
        $transition2 = $this->machine->getTransition($this->y, $this->z);

        $this->machine->expects($this->at(0))
            ->method('onTransition')
            ->with($this->equalTo($transition1));

        $this->machine->expects($this->at(1))
            ->method('onTransition')
            ->with($this->equalTo($transition2));

        $this->machine->run();

        $this->assertTrue($this->machine->isMachineState($this->z));

    }

    /**
     * @depends test_setInitState
     * @depends test_allowTransition
     * @throws TransitionException
     * @throws MachineException
     */
    public function test_runOnce_automatic_transition_through_1_state_succ()
    {
        $this->machine = $this->createPartialMock(SampleMachine::class, ['onTransition']);

        $this->machine->setInitState($this->x);

        $this->machine->allowTransition($this->x, $this->y, new AlwaysTrueAssertion());
        $this->machine->allowTransition($this->y, $this->z, new AlwaysTrueAssertion());

        $transition = $this->machine->getTransition($this->x, $this->y);

        $this->machine->expects($this->once())
            ->method('onTransition')
            ->with($this->equalTo($transition));


        $this->machine->runOnce();

        $this->assertTrue($this->machine->isMachineState($this->y));

    }

    /**
     * @depends test_setInitState
     * @depends test_allowTransition
     * @throws TransitionException
     * @throws MachineException
     */
    public function test_run_automatic_transition_through_n_states_stops()
    {
        $this->machine = $this->createPartialMock(SampleMachine::class, ['onTransition']);

        $this->machine->setInitState($this->x);

        $this->machine->allowTransition($this->x, $this->y, new AlwaysTrueAssertion());
        $this->machine->allowTransition($this->y, $this->z, new AlwaysFalseAssertion());

        $transition1 = $this->machine->getTransition($this->x, $this->y);

        $this->machine->expects($this->once())
            ->method('onTransition')
            ->with($this->equalTo($transition1));

        $this->machine->run();

        $this->assertFalse($this->machine->isMachineState($this->z));
        $this->assertTrue($this->machine->isMachineState($this->y));

    }

    /**
     * @depends test_allowTransition
     * @depends test_setInitState
     * @throws MachineException
     * @throws TransitionException
     */
    public function test_run_DefaultCallbackAssertion_succ()
    {
        $this->machine = $this->createPartialMock(SampleMachine::class, ['assertOneToTwo']);

        $this->machine->allowTransition($this->x, $this->y, new DefaultCallbackAssertion());

        $transition = $this->machine->getTransition($this->x, $this->y);

        $this->machine->expects($this->once())
            ->method('assertOneToTwo')
            ->with($this->equalTo($transition))
            ->willReturn(true);

        $this->machine->setInitState($this->x);

        $this->machine->run();

        $this->assertTrue($this->machine->isMachineState($this->y));
    }

    /**
     * @depends test_allowTransition
     * @depends test_setInitState
     * @throws MachineException
     * @throws TransitionException
     */
    public function test_run_DefaultCallbackAssertion_fail()
    {
        $this->machine = $this->createPartialMock(SampleMachine::class, ['assertOneToTwo']);

        $this->machine->allowTransition($this->x, $this->y, new DefaultCallbackAssertion());

        $transition = $this->machine->getTransition($this->x, $this->y);

        $this->machine->expects($this->once())
            ->method('assertOneToTwo')
            ->with($this->equalTo($transition))
            ->willReturn(false);

        $this->machine->setInitState($this->x);

        $this->machine->run();

        $this->assertTrue($this->machine->isMachineState($this->x));
    }

    /**
     * @depends test_allowTransition
     * @depends test_setInitState
     * @throws MachineException
     * @throws TransitionException
     */
    public function test_run_CommonCallbackAssertion_succ()
    {
        $this->machine = $this->createPartialMock(SampleMachine::class, ['assertTransition']);

        $this->machine->allowTransition($this->x, $this->y, new CommonCallbackAssertion());

        $transition = $this->machine->getTransition($this->x, $this->y);

        $this->machine->expects($this->once())
            ->method('assertTransition')
            ->with($this->equalTo($transition))
            ->willReturn(true);

        $this->machine->setInitState($this->x);

        $this->machine->run();

        $this->assertTrue($this->machine->isMachineState($this->y));
    }

    /**
     * @depends test_allowTransition
     * @depends test_setInitState
     * @throws MachineException
     * @throws TransitionException
     */
    public function test_run_CommonCallbackAssertion_fail()
    {
        $this->machine = $this->createPartialMock(SampleMachine::class, ['assertTransition']);

        $this->machine->allowTransition($this->x, $this->y, new CommonCallbackAssertion());

        $transition = $this->machine->getTransition($this->x, $this->y);

        $this->machine->expects($this->once())
            ->method('assertTransition')
            ->with($this->equalTo($transition))
            ->willReturn(false);

        $this->machine->setInitState($this->x);

        $this->machine->run();

        $this->assertTrue($this->machine->isMachineState($this->x));
    }
}