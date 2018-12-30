<?php

namespace Coff\SMF;

use Coff\SMF\Schema\Schema;
use Coff\SMF\Exception\ConfigurationException;
use Coff\SMF\Exception\TransitionException;
use Coff\SMF\Transition\Transition;

abstract class Machine implements MachineInterface
{
    /** @var Schema */
    protected $schema;
    /** @var StateEnum */
    private $machineState;

    /**
     * @return $this
     * @throws ConfigurationException
     */
    public function init()
    {
        if (!$this->schema instanceof Schema) {
            throw new ConfigurationException("Machine's state transition Schema missing");
        }

        $initState = $this->schema->getInitState();

        if (!$initState instanceof StateEnum) {
            throw new ConfigurationException("InitState not set");
        }

        $this->machineState = $initState;

        return $this;
    }

    /**
     * @return Schema
     */
    public function getSchema(): Schema
    {
        return $this->schema;
    }

    /**
     * @param Schema $schema
     * @return Machine
     */
    public function setSchema(Schema $schema): Machine
    {
        $this->schema = $schema;
        return $this;
    }


    /**
     * Verifies if machine's current state is equal to state given in parameter
     *
     * @param StateEnum $state
     * @return bool
     */
    public function isMachineState(StateEnum $state): bool
    {
        return (string)$this->getMachineState() == (string)$state ? true : false;
    }

    /**
     * Returns machine's current state
     * @return StateEnum
     */
    public function getMachineState(): StateEnum
    {
        return $this->machineState;
    }

    /**
     * Machine-internal method for setting new state. Normally this should only be allowed from within the machine
     * assertState() method. To set machine's state externally create dedicated methods like:
     *
     * public function on() {
     *      $this->setMachineState(...);
     * }
     *
     * @param $newState
     * @return $this
     * @throws TransitionException
     * @throws ConfigurationException
     * @throws Exception\SchemaException
     */
    protected function setMachineState(StateEnum $newState)
    {
        // just return if state doesn't change
        if ($this->machineState->is((string)$newState)) {
            return $this;
        }

        if (!$this->schema instanceof Schema) {
            throw new ConfigurationException('State transition schema not set');
        }

        if (false === $this->schema->isTransitionAllowed($this->machineState, $newState)) {
            throw new TransitionException('State transition from ' . (string)$this->getMachineState() . ' to ' . (string)$newState . ' is not allowed.');
        }

        $oldState = $this->machineState;

        $this->machineState = $newState;

        $transition = $this->schema->getTransition($oldState, $newState);

        // no assert() call in manual setMachineState call

        // we tell machine object that transition happened
        $this->onTransition($transition);

        // we tell transition object that it happened to our machine object
        $transition->onTransition($this);

        return $this;
    }


    /**
     * Method called on any state transition occurrence
     * @param Transition $transition
     * @return MachineInterface|void
     */
    public function onTransition(Transition $transition)
    {
        // Default implementation does nothing. This can be used to dispatch events in kind-of EventAwareMachine you can
        // implement yourself.
    }


    /**
     * Default handler method for CommonCallbackAssertion
     * @param Transition $transition
     * @return bool
     */
    public function assertTransition(Transition $transition): bool
    {
        // default method just returns true
        return true;
    }

    /**
     * Runs the machine as far as following transitions return true
     * @return StateEnum
     */
    public function run()
    {
        do {
            $result = $this->runOnce();

        } while (true === $result);

        return $this->machineState;
    }

    /**
     * Makes one pass through transitions available in current state
     *
     * @return boolean
     */
    public function runOnce(): bool
    {
        $result = false;

        $allowedTrans = $this->schema->getAllowedTransitions($this->getMachineState());

        /**
         * @var StateEnum $nextState
         * @var Transition $transition
         */
        foreach ($allowedTrans as $nextState => $transition) {

            $result = $transition->assert($this);

            if (true === $result) {
                $this->machineState = $transition->getToState();

                $this->onTransition($transition);

                return $result;
            }
        }

        return $result;
    }

}
