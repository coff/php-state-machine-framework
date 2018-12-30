<?php

namespace Coff\SMF\Schema;

use Coff\SMF\Assertion\Assertion;
use Coff\SMF\Assertion\DefaultCallbackAssertion;
use Coff\SMF\Exception\ConfigurationException;
use Coff\SMF\Exception\SchemaException;
use Coff\SMF\StateEnum;
use Coff\SMF\Transition\Transition;
use Coff\SMF\Transition\TransitionInterface;

/**
 * Class Schema
 *
 * Class represents complete state machine's transition diagram. Keeping it decoupled from actual state machine
 * allows us to conveniently handle DI of state transitions between state machines' objects or classes.
 */
class Schema
{
    /** @var StateEnum|null */
    protected $initState = null;

    /** @var array */
    protected $allowedTransitions = [];

    /**
     * @return StateEnum|null
     */
    public function getInitState()
    {
        return $this->initState;
    }

    /**
     * @param StateEnum|null $initState
     * @return Schema
     */
    public function setInitState(StateEnum $initState): Schema
    {
        $this->initState = $initState;
        return $this;
    }

    /**
     * Returns Transitions allowed for current state or state specified in parameter
     * @param StateEnum $state
     * @return array
     */
    public function getAllowedTransitions(StateEnum $state): array
    {

        return isset($this->allowedTransitions[(string)$state]) ? $this->allowedTransitions[(string)$state] : [];
    }


    /**
     * Verifies if transition is allowed from current state to the state given in parameter
     * @param StateEnum $fromState
     * @param StateEnum $toState
     * @return bool
     */
    public function isTransitionAllowed(StateEnum $fromState, StateEnum $toState): bool
    {
        return isset($this->allowedTransitions[(string)$fromState][(string)$toState]) ? true : false;
    }


    /**
     * @param StateEnum $stateFrom
     * @param StateEnum $stateTo
     * @return Transition
     * @throws SchemaException
     */
    public function getTransition(StateEnum $stateFrom, StateEnum $stateTo)
    {
        if (isset($this->allowedTransitions[(string)$stateFrom][(string)$stateTo])) {
            return $this->allowedTransitions[(string)$stateFrom][(string)$stateTo];
        } else {
            throw new SchemaException('No transition object for ' . $stateFrom . ' to ' . $stateTo);
        }
    }

    /**
     * Allows certain transition by creating a new Transition object internally
     * @param StateEnum $stateFrom
     * @param StateEnum $stateTo
     * @param Assertion $assertion
     * @return $this
     */
    public function allowTransition(StateEnum $stateFrom, StateEnum $stateTo, Assertion $assertion = null)
    {
        try {
            $transition = new Transition($stateFrom, $stateTo);

            // Default assertion when no assertions
            if (null == $assertion) {
                $assertion = new DefaultCallbackAssertion();
            }

            $transition->addAssertion($assertion);

            $this->addTransition($transition);

        } catch (ConfigurationException $e) {
            // configuration exception is only thrown when addTransition is called with unconfigured transition in param
            // this method prevents this so we don't need to worry about it
        }
        return $this;
    }


    /**
     * Adds transition object to Machine
     * @param TransitionInterface $transition
     * @return $this
     * @throws ConfigurationException
     */
    public function addTransition(TransitionInterface $transition)
    {

        if (!$transition->getFromState() instanceof StateEnum) {
            throw new ConfigurationException('Transition is not ready to be set as allowed!');
        }

        if (!$transition->getToState() instanceof StateEnum) {
            throw new ConfigurationException('Transition is not ready to be set as allowed!');
        }

        $this->allowedTransitions[(string)$transition->getFromState()][(string)$transition->getToState()] = $transition;

        return $this;
    }
}