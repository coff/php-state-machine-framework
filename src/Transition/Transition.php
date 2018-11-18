<?php


namespace Coff\SMF\Transition;

use Coff\SMF\StateEnum;

class Transition implements TransitionInterface
{
    /** @var StateEnum */
    protected $fromState;

    /** @var StateEnum */
    protected $toState;

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
     * @return bool
     */
    public function assert() : bool
    {
        // becomes true when transition object is even created with from/to states
        if ($this->fromState instanceof StateEnum && $this->toState instanceof StateEnum) {
            return true;
        } else {
            return false;
        }
    }
}