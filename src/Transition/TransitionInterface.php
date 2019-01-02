<?php


namespace Coff\SMF\Transition;


use Coff\SMF\MachineInterface;
use Coff\SMF\StateEnum;

interface TransitionInterface
{
    /**
     * @param StateEnum $state
     * @return $this
     */
    public function setFromState(StateEnum $state);

    /**
     * @param StateEnum $state
     * @return $this
     */
    public function setToState(StateEnum $state);

    /**
     * @return StateEnum
     */
    public function getFromState(): StateEnum;

    /**
     * @return StateEnum
     */
    public function getToState(): StateEnum;

    /**
     * @param MachineInterface $machine
     * @return bool
     */
    public function assert(MachineInterface $machine): bool;

    /**
     * Method called when this transition occurs. Allows e.g. external machine control
     * @param MachineInterface $machine
     */
    public function onTransition(MachineInterface $machine);
}