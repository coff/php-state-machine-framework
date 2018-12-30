<?php


namespace Coff\SMF\Assertion;


use Coff\SMF\MachineInterface;
use Coff\SMF\Transition\TransitionInterface;

interface AssertionInterface
{
    public function make(MachineInterface $machine, TransitionInterface $transition): bool;
}