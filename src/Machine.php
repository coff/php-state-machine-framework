<?php

namespace Coff\SMF;

use Coff\SMF\Assertion\Assertion;
use Coff\SMF\Assertion\CommonCallbackAssertion;
use Coff\SMF\Assertion\DefaultCallbackAssertion;
use Coff\SMF\Exception\MachineException;
use Coff\SMF\Exception\TransitionException;
use Coff\SMF\Transition\Transition;
use Coff\SMF\Transition\TransitionInterface;

abstract class Machine implements MachineInterface
{
    /** @var StateEnum */
    private $machineState;

    /** @var StateEnum */
    private $initState;

    /** @var array $allowedTransitions allowed transitions map */
    private $allowedTransitions;


    /**
     * Allows certain transition by creating a new Transition object internally
     * @param StateEnum $stateFrom
     * @param StateEnum $stateTo
     * @param Assertion $assertion
     * @return $this
     */
    public function allowTransition(StateEnum $stateFrom, StateEnum $stateTo, Assertion $assertion = null)
    {
        $transition = new Transition($stateFrom, $stateTo);

        // Default assertion when no assertions
        if (null == $assertion) {
            $assertion = new DefaultCallbackAssertion();
        }

        $transition->addAssertion($assertion);

        // Wire it up
        switch (true) {
            case $assertion instanceof CommonCallbackAssertion:
                // no break
            case $assertion instanceof DefaultCallbackAssertion:
                // autowire these
                $assertion->setObject($this);
                $assertion->setTransition($transition);
                break;
        }

        try {
            $this->addTransition($transition);
        } catch (MachineException $e) {
            // It won't fire this exception when called from here anyway
        }

        return $this;
    }

    /**
     * Adds transition object to Machine
     * @param TransitionInterface $transition
     * @return $this
     * @throws MachineException
     */
    public function addTransition(TransitionInterface $transition)
    {

        if (!$transition->getFromState() instanceof StateEnum) {
            throw new MachineException('Transition is not ready to be set as allowed!');
        }

        if (!$transition->getToState() instanceof StateEnum) {
            throw new MachineException('Transition is not ready to be set as allowed!');
        }

        $this->allowedTransitions[(string)$transition->getFromState()][(string)$transition->getToState()] = $transition;

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
     * @throws MachineException
     */
    protected function setMachineState(StateEnum $newState)
    {

        if (false === $this->isTransitionAllowed($newState)) {
            throw new TransitionException('State transition from ' . (string)$this->getMachineState() . ' to ' . (string)$newState . 'is not allowed.');
        }

        $oldState = $this->machineState;

        $this->machineState = $newState;

        $this->onTransition($this->getTransition($oldState, $newState));

        return $this;
    }

    /**
     * Verifies if transition is allowed from current state to the state given in parameter
     * @param StateEnum $state
     * @return bool
     */
    public function isTransitionAllowed(StateEnum $state): bool
    {
        return isset($this->allowedTransitions[(string)$this->getMachineState()][$state]) ? true : false;
    }

    /**
     * Method called on any state transition occurence
     * @param Transition $transition
     * @return MachineInterface|void
     */
    public function onTransition(Transition $transition)
    {
        // Default implementation does nothing. This can be used to dispatch events in kind-of EventAwareMachine you can
        // implement yourself.
    }

    /**
     * @param StateEnum $stateFrom
     * @param StateEnum $stateTo
     * @return Transition
     * @throws MachineException
     */
    public function getTransition(StateEnum $stateFrom, StateEnum $stateTo)
    {
        if (isset($this->allowedTransitions[(string)$stateFrom][(string)$stateTo])) {
            return $this->allowedTransitions[(string)$stateFrom][(string)$stateTo];
        } else {
            throw new MachineException('No transition object for ' . $stateFrom . ' to ' . $stateTo);
        }
    }

    /**
     * Sets machine's initial state
     * @param StateEnum $state
     * @return $this|MachineInterface
     */
    public function setInitState(StateEnum $state)
    {
        $this->initState = $state;

        return $this;
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
     * @throws TransitionException
     */
    public function run()
    {

        do {
            $result = false;

            $allowedTrans = $this->getAllowedTransitions();

            /**
             * @var StateEnum $nextState
             * @var Transition $transition
             */
            foreach ($allowedTrans as $nextState => $transition) {

                $result = $transition->assert();

                if (true === $result) {
                    $this->machineState = $transition->getToState();

                    $this->onTransition($transition);
                }
            }

        } while (true == $result);

        return $this->machineState;
    }

    /**
     * Returns Transitions allowed for current state or state specified in parameter
     * @param StateEnum|null $state
     * @return array
     */
    public function getAllowedTransitions(StateEnum $state = null): array
    {

        if (null === $state) {
            $state = $this->getMachineState();
        }

        return $this->allowedTransitions[(string)$state];
    }
}
