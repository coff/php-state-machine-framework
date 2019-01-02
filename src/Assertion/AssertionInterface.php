<?php


namespace Coff\SMF\Assertion;


use Coff\SMF\MachineInterface;
use Coff\SMF\Transition\TransitionInterface;

interface AssertionInterface
{
    /**
     * @param MachineInterface $machine
     * @param TransitionInterface $transition
     * @return bool
     */
    public function make(MachineInterface $machine, TransitionInterface $transition): bool;
}