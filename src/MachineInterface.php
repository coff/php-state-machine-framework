<?php

namespace Coff\SMF;

use Coff\SMF\Exception\TransitionException;
use Coff\SMF\Transition\Transition;

interface MachineInterface
{
    /**
     * @param StateEnum $state
     * @return $this
     */
    public function setInitState(StateEnum $state);


    /**
     * Returns Machine's initial state
     * @return StateEnum
     */
    public function getInitState();

    /**
     * Returns
     * @return StateEnum
     */
    public function getMachineState(): StateEnum;

    /**
     * Checks whether machine is in certain state
     * @param StateEnum $state
     * @return bool
     */
    public function isMachineState(StateEnum $state): bool;

    /**
     * Default transition assertion method for CommonCallbackAssertion
     * @param Transition $transition
     * @return bool
     */
    public function assertTransition(Transition $transition): bool;

    /**
     * This method is called on any state transition
     * @param Transition $transition
     * @return $this
     */
    public function onTransition(Transition $transition);

    /**
     * Runs state machine. Performs assertions for all transitions defined for current state.
     */
    public function run();

    /**
     * Makes one pass through transitions available in current state, makes assertions and eventually changes state
     *
     * @return boolean
     * @throws TransitionException
     */
    public function runOnce(): bool;

}
