<?php


namespace Coff\SMF\Assertion;

use Coff\SMF\MachineInterface;
use Coff\SMF\Transition\TransitionInterface;

class DefaultCallbackAssertion extends CallbackAssertion
{
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * @param MachineInterface $machine
     * @param TransitionInterface $transition
     * @return bool
     */
    public function make(MachineInterface $machine, TransitionInterface $transition): bool
    {
        return call_user_func_array([
            $machine,
            'assert' . ucfirst($transition->getFromState()) . 'To' . ucfirst($transition->getToState()),
        ], [$transition]);
    }
}