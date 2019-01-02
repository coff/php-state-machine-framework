<?php


namespace Coff\SMF\Test;


use Coff\SMF\Exception\ConfigurationException;
use Coff\SMF\Exception\SchemaException;
use Coff\SMF\Exception\TransitionException;
use Coff\SMF\Schema\Schema;
use Coff\SMF\Transition\Transition;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MachineTest extends TestCase
{
    /** @var SampleMachine */
    protected $machine;

    /** @var Schema|MockObject */
    protected $schema;

    /** @var SampleStateEnum */
    protected $x, $y, $z;

    /**
     * @throws ConfigurationException
     */
    public function setUp()
    {
        $this->machine = new SampleMachine();
        $this->x = SampleStateEnum::ONE();
        $this->y = SampleStateEnum::TWO();
        $this->z = SampleStateEnum::THREE();

        $this->schema = $this->createPartialMock(Schema::class, ['getInitState']);

        $this->schema->expects($this->once())
            ->method('getInitState')
            ->willReturn($this->x);

        $this->machine->setSchema($this->schema);
        $this->machine->init();
    }

    /**
     * @throws ConfigurationException
     */
    public function test_init_on_schema_not_set()
    {
        $this->machine = new SampleMachine();

        $this->expectException(ConfigurationException::class);
        $this->machine->init();
    }

    /**
     * @throws ConfigurationException
     */
    public function test_init_on_schema_set_bot_no_init_state()
    {
        $this->machine = new SampleMachine();
        $this->expectException(ConfigurationException::class);

        $this->machine->setSchema(new Schema());
        $this->machine->init();
    }


    /**
     *
     */
    public function test_init_on_proper_schema()
    {
        $this->assertEquals($this->x, $this->machine->getMachineState());
    }

    /**
     * @depends test_init_on_proper_schema
     */
    public function test_isMachineState()
    {
        $this->assertEquals(true, $this->machine->isMachineState($this->x));
    }

    /**
     * @depends test_init_on_proper_schema
     * @throws ConfigurationException
     */
    public function test_isMachineState_one_state_not_same_object()
    {
        $this->machine->setSchema($schema = new Schema());
        $schema->setInitState(SampleStateEnum::ONE());
        $this->machine->init();

        $this->assertEquals(true, $this->machine->isMachineState(SampleStateEnum::ONE()));
    }


    /**
     * @depends test_init_on_proper_schema
     * @throws ConfigurationException
     * @throws TransitionException
     * @throws SchemaException
     */
    public function test_setMachineState_not_allowed_transition()
    {
        $this->expectException(TransitionException::class);
        $this->machine->setMachineState(SampleStateEnum::TWO());
    }

    /**
     * @throws ConfigurationException
     * @throws SchemaException
     * @throws TransitionException
     */
    public function test_setMachineState_calls_onTransition()
    {
        $this->machine = $this->createPartialMock(SampleMachine::class, ['onTransition']);

        $transition1 = $this->createPartialMock(Transition::class, ['assert', 'getToState', 'onTransition']);

        /**
         * $transition1->expects($this->once())
         * ->method('assert')
         * ->willReturn(true);
         * or should we expect assert() here too? @todo
         **/

        $transition1->expects($this->once())
            ->method('onTransition')
            ->with($this->equalTo($this->machine));

        /** @var Schema|MockObject $schema */
        $schema = $this->createPartialMock(Schema::class, ['isTransitionAllowed', 'getInitState', 'getTransition']);

        $schema->expects($this->once())
            ->method('getInitState')
            ->willReturn($this->x);

        $schema->expects($this->once())
            ->method('isTransitionAllowed')
            ->with($this->equalTo($this->x), $this->equalTo($this->y))
            ->willReturn(true);

        $schema->expects($this->once())
            ->method('getTransition')
            ->with($this->equalTo($this->x), $this->equalTo($this->y))
            ->willReturn($transition1);

        $this->machine->setSchema($schema);
        $this->machine->init();

        $this->machine->expects($this->once())
            ->method('onTransition')
            ->with($this->equalTo($transition1));

        $this->machine->setMachineState($this->y);
    }

    /**
     * @throws ConfigurationException
     */
    public function test_runOnce_automatic_transition_through_1_state_succ()
    {
        $this->machine = $this->createPartialMock(SampleMachine::class, ['onTransition']);

        $transition1 = $this->createPartialMock(Transition::class, ['assert', 'getToState']);

        $transition1->expects($this->once())
            ->method('getToState')
            ->willReturn($this->y);

        $transition1->expects($this->once())
            ->method('assert')
            ->with($this->equalTo($this->machine))
            ->willReturn(true);

        /** @var Schema|MockObject $schema */
        $schema = $this->createPartialMock(Schema::class, ['getAllowedTransitions', 'getInitState']);

        $schema->expects($this->once())
            ->method('getInitState')
            ->willReturn($this->x);

        $schema->expects($this->once())
            ->method('getAllowedTransitions')
            ->with($this->equalTo($this->x))
            ->willReturn([(string)$this->y => $transition1]);

        $this->machine->setSchema($schema);
        $this->machine->init();

        $this->machine->expects($this->once())
            ->method('onTransition')
            ->with($this->equalTo($transition1));


        $this->machine->runOnce();

        $this->assertTrue($this->machine->isMachineState($this->y));

    }

    /**
     * @throws ConfigurationException
     */
    public function test_runOnce_returns_on_first_assert_true()
    {
        $this->machine = $this->createPartialMock(SampleMachine::class, ['onTransition']);

        $transition1 = $this->createPartialMock(Transition::class, ['assert', 'getToState']);

        $transition1->expects($this->once())
            ->method('getToState')
            ->willReturn($this->y);

        $transition1->expects($this->once())
            ->method('assert')
            ->with($this->equalTo($this->machine))
            ->willReturn(true);

        $transition2 = $this->createPartialMock(Transition::class, ['assert', 'getToState']);

        $transition2->expects($this->never())
            ->method('assert');

        /** @var Schema|MockObject $schema */
        $schema = $this->createPartialMock(Schema::class, ['getAllowedTransitions', 'getInitState']);

        $schema->expects($this->once())
            ->method('getInitState')
            ->willReturn($this->x);

        $schema->expects($this->once())
            ->method('getAllowedTransitions')
            ->with($this->equalTo($this->x))
            ->willReturn([(string)$this->y => $transition1, (string)$this->z => $transition2]);

        $this->machine->setSchema($schema);
        $this->machine->init();

        $this->machine->expects($this->once())
            ->method('onTransition')
            ->with($this->equalTo($transition1));


        $this->machine->runOnce();

        $this->assertTrue($this->machine->isMachineState($this->y));

    }

    /**
     * @depends test_runOnce_automatic_transition_through_1_state_succ
     * @throws ConfigurationException
     */
    public function test_run_automatic_transition_through_n_states_succ()
    {
        $this->machine = $this->createPartialMock(SampleMachine::class, ['onTransition']);

        $transition1 = $this->createPartialMock(Transition::class, ['assert', 'getToState',]);
        $transition2 = $this->createPartialMock(Transition::class, ['assert', 'getToState',]);

        $transition1->expects($this->once())
            ->method('getToState')
            ->willReturn($this->y);

        $transition1->expects($this->once())
            ->method('assert')
            ->with($this->equalTo($this->machine))
            ->willReturn(true);

        $transition2->expects($this->once())
            ->method('getToState')
            ->willReturn($this->z);

        $transition2->expects($this->once())
            ->method('assert')
            ->with($this->equalTo($this->machine))
            ->willReturn(true);

        /** @var Schema|MockObject $schema */
        $schema = $this->createPartialMock(Schema::class, ['getAllowedTransitions', 'getInitState']);

        $schema->expects($this->at(0))
            ->method('getInitState')
            ->willReturn($this->x);

        $schema->expects($this->at(1))
            ->method('getAllowedTransitions')
            ->with($this->equalTo($this->x))
            ->willReturn([(string)$this->y => $transition1]);

        $schema->expects($this->at(2))
            ->method('getAllowedTransitions')
            ->with($this->equalTo($this->y))
            ->willReturn([(string)$this->z => $transition2]);

        $this->machine->setSchema($schema);
        $this->machine->init();

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
     * @depends test_run_automatic_transition_through_n_states_succ
     * @throws ConfigurationException
     */
    public function test_run_automatic_transition_through_n_states_stops()
    {
        $this->machine = $this->createPartialMock(SampleMachine::class, ['onTransition']);

        $transition1 = $this->createPartialMock(Transition::class, ['assert', 'getToState']);
        $transition2 = $this->createPartialMock(Transition::class, ['assert', 'getToState']);

        $transition1->expects($this->once())
            ->method('getToState')
            ->willReturn($this->y);

        $transition1->expects($this->once())
            ->method('assert')
            ->with($this->equalTo($this->machine))
            ->willReturn(true);

        $transition2->expects($this->once())
            ->method('assert')
            ->with($this->equalTo($this->machine))
            ->willReturn(false);

        /** @var Schema|MockObject $schema */
        $schema = $this->createPartialMock(Schema::class, ['getAllowedTransitions', 'getInitState']);

        $schema->expects($this->at(0))
            ->method('getInitState')
            ->willReturn($this->x);

        $schema->expects($this->at(1))
            ->method('getAllowedTransitions')
            ->with($this->equalTo($this->x))
            ->willReturn([(string)$this->y => $transition1]);

        $schema->expects($this->at(2))
            ->method('getAllowedTransitions')
            ->with($this->equalTo($this->y))
            ->willReturn([(string)$this->z => $transition2]);

        $this->machine->setSchema($schema);
        $this->machine->init();

        $this->machine->expects($this->once())
            ->method('onTransition')
            ->with($this->equalTo($transition1));

        $this->machine->run();

        $this->assertTrue($this->machine->isMachineState($this->y));

    }
}