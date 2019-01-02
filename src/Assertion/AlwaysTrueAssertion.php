<?php


namespace Coff\SMF\Assertion;


use Coff\SMF\MachineInterface;
use Coff\SMF\Transition\TransitionInterface;

/**
 * Class AlwaysTrueAssertion
 * Assertion always returning false thus causing transition cycle to stop
 */
class AlwaysTrueAssertion extends Assertion
{

    public function make(MachineInterface $machine, TransitionInterface $transition): bool
    {
        return true;
    }
}