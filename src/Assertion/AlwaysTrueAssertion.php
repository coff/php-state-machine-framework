<?php


namespace Coff\SMF\Assertion;


use Coff\SMF\MachineInterface;
use Coff\SMF\Transition\TransitionInterface;

class AlwaysTrueAssertion extends Assertion
{

    public function make(MachineInterface $machine, TransitionInterface $transition): bool
    {
        return true;
    }
}