<?php


namespace Coff\SMF;


use Coff\SMF\Exception\MachineException;
use Coff\SMF\Exception\TransitionException;
use Coff\SMF\Transition\TransitionInterface;

abstract class Machine implements MachineInterface
{
    /** @var StateEnum */
    private $machineState;

    /** @var StateEnum */
    private $defaultState;

    /** @var array $allowedTransitions allowed transitions map */
    private $allowedTransitions;



    public function allowTransition(TransitionInterface $transition) {

        if (!$transition->getFromState() instanceof StateEnum) {
            throw new MachineException('Transition is not ready to be set as allowed!');
        }

        if (!$transition->getToState() instanceof StateEnum) {
            throw new MachineException('Transition is not ready to be set as allowed!');
        }


        $this->allowedTransitions[(string)$transition->getFromState()][(string)$transition->getToState()] = $transition;

        return $this;
    }


    public function getAllowedTransitions(StateEnum $state = null) : array {

        if (null === $state) {
            $state = $this->getMachineState();
        }

        return $this->allowedTransitions[(string)$state];
    }

    public function isTransitionAllowed(StateEnum $state) {
        return isset($this->allowedTransitions[(string)$this->getMachineState()][$state]) ? true : false;
    }

    public function getMachineState()
    {
        return $this->machineState;
    }

    public function isMachineState(StateEnum $state)
    {
        return (string) $this->getMachineState() == (string) $state ? true : false;
    }

    public function setDefaultState(StateEnum $state) {
        $this->defaultState = $state;

        return $this;
    }

    public function assertState() {

        $allowedTrans = $this->getAllowedTransitions();

        /**
         * @var StateEnum $nextState
         * @var Criteria $transition
         */
        foreach ($allowedTrans as $nextState => $transition) {


            if (true === $transition->assert()) {
                $this->machineState = $transition->getToState();

                return $nextState;
            }
        }

        return false;
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
     */
    protected function setMachineState(StateEnum $newState) {

        if (false === $this->isTransitionAllowed($newState)){
            throw new TransitionException('State transition from ' . (string) $this->getMachineState() . ' to ' . (string) $newState . 'is not allowed.');
        }

        $this->machineState = $newState;

        return $this;
    }
}
