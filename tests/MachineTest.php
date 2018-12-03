<?php


namespace Coff\SMF\Test;


use Coff\SMF\Exception\MachineException;
use Coff\SMF\Exception\TransitionException;
use Coff\SMF\Transition\Transition;
use PHPUnit\Framework\TestCase;

class MachineTest extends TestCase
{
    /** @var SampleMachine */
    protected $machine;

    /** @var SampleStateEnum */
    protected $x, $y;

    public function setUp()
    {
        $this->machine = new SampleMachine();
        $this->x = SampleStateEnum::ONE();
        $this->y = SampleStateEnum::TWO();
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

    public function test_setInitState()
    {
        $this->machine->setInitState($this->x);

        $this->assertInstanceOf(SampleStateEnum::class, $this->machine->getInitState());
        $this->assertEquals($this->x, $this->machine->getInitState());
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
     * @throws \Coff\SMF\Exception\MachineException
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


}