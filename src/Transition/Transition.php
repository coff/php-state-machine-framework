<?php


namespace Coff\SMF\Transition;

use Coff\SMF\Assertion\Assertion;
use Coff\SMF\Assertion\AssertionInterface;
use Coff\SMF\Exception\TransitionException;
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
     * @param StateEnum $state
     * @return $this
     */
    public function setFromState(StateEnum $state)
    {
        $this->fromState = $state;

        return $this;
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
     * @return StateEnum
     */
    public function getFromState() : StateEnum
    {
        return $this->fromState;
    }

    /**
     * @return StateEnum
     */
    public function getToState() : StateEnum
    {
        return $this->toState;
    }

    /**
     * @param Assertion $assertion
     * @param null $keyName
     * @return $this
     */
    public function addAssertion(Assertion $assertion, $keyName=null)
    {
        if (null === $keyName)
        {
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
        if (false === isset($this->assertions[$keyName]))
        {
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
     */
    public function replaceAssertions(array $assertions)
    {
        $this->assertions = $assertions;

        return $this;
    }

    /**
     * @return bool
     * @throws TransitionException
     */
    public function assert() : bool
    {
        if (!$this->assertions) {
            throw new TransitionException('No assertions defined');
        }

        // check all assertion objects attached
        foreach ($this->assertions as $assertion) {
            if (!$assertion instanceof AssertionInterface)
            {
                throw new TransitionException('Assertion has to implement AssertionInterface');
            }

            if (false === $assertion->make())
            {
                return false;
            }
        }

        // defaults to true otherwise
        return true;
    }
}