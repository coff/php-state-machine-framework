<?php


namespace Coff\SMF\Transition;


use Coff\SMF\MachineInterface;
use Coff\SMF\StateEnum;

interface TransitionInterface
{
    public function setFromState(StateEnum $state);

    public function setToState(StateEnum $state);

    public function getFromState(): StateEnum;

    public function getToState(): StateEnum;

    public function assert(MachineInterface $machine): bool;

    /**
     * Method called when this transition occurs. Allows e.g. external machine control
     * @param MachineInterface $machine
     */
    public function onTransition(MachineInterface $machine);
}