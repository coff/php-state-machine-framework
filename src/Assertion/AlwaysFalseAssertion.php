<?php


namespace Coff\SMF\Assertion;

use Coff\SMF\MachineInterface;
use Coff\SMF\Transition\TransitionInterface;

/**
 * False Assertion for unit testing
 */
class AlwaysFalseAssertion extends Assertion
{
    public function make(MachineInterface $machine, TransitionInterface $transition): bool
    {
        return false;
    }
}