<?php


namespace Coff\SMF\Transition;

use Coff\SMF\Assertion\Assertion;
use Coff\SMF\Assertion\AssertionInterface;
use Coff\SMF\Exception\TransitionException;
use Coff\SMF\MachineInterface;
use Coff\SMF\StateEnum;

class Transition implements TransitionInterface
{
    /** @var StateEnum */
    protected $fromState;

    /** @var StateEnum */
    protected $toState;

    /** @var Assertion[] */
    protected $assertions = [];

    public function __construct(StateEnum $fromState, StateEnum $toState)
    {
        $this->fromState = $fromState;
        $this->toState = $toState;
    }

    /**
     * @return StateEnum
     */
    public function getFromState(): StateEnum
    {
        return $this->fromState;
    }

    /**
     * @param StateEnum $state
     * @return $this
     */
    public function setFromState(StateEnum $state)
    {
        $this->fromState = $state;

        return $this;
    }

    /**
     * @return StateEnum
     */
    public function getToState(): StateEnum
    {
        return $this->toState;
    }

    /**
     * @param StateEnum $state
     * @return $this
     */
    public function setToState(StateEnum $state)
    {
        $this->toState = $state;

        return $this;
    }

    /**
     * @param AssertionInterface $assertion
     * @param null $keyName
     * @return $this
     */
    public function addAssertion(AssertionInterface $assertion, $keyName = null)
    {
        if (null === $keyName) {
            $this->assertions[] = $assertion;
        } else {
            $this->assertions[$keyName] = $assertion;
        }

        return $this;
    }

    /**
     * Removes assertion by key, first they should be added with a key
     * @param $keyName
     * @return $this
     * @throws TransitionException
     */
    public function removeAssertion($keyName)
    {
        if (false === isset($this->assertions[$keyName])) {
            throw new TransitionException('Assertion by name ' . $keyName . ' not defined');
        }

        unset($this->assertions[$keyName]);

        return $this;
    }


    /**
     * Clears assertion array
     * @return $this
     */
    public function clearAssertions()
    {
        $this->assertions = [];

        return $this;
    }

    /**
     * Replace all assertions
     * @param array $assertions
     * @return $this
     * @throws TransitionException
     */
    public function replaceAssertions(array $assertions)
    {
        foreach ($assertions as $assertion) {
            if (!$assertion instanceof AssertionInterface) {
                throw new TransitionException('Only Assertion objects allowed!');
            }
        }

        $this->assertions = $assertions;

        return $this;
    }

    /**
     * @param MachineInterface $machine
     * @return bool
     */
    public function assert(MachineInterface $machine): bool
    {
        if (!$this->assertions) {
            return false;
        }

        // check all assertion objects attached
        foreach ($this->assertions as $assertion) {

            if (false === $assertion->make($machine, $this)) {
                return false;
            }
        }

        $this->onTransition($machine);

        // defaults to true otherwise
        return true;
    }

    public function onTransition(MachineInterface $machine)
    {
        // does nothing in in basic implementation
    }
}