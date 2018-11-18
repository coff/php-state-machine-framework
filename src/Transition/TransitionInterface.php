<?php


namespace Coff\SMF\Transition;


use Coff\SMF\StateEnum;

interface TransitionInterface
{
    public function setFromState(StateEnum $state);

    public function setToState(StateEnum $state);

    public function getFromState() : StateEnum;

    public function getToState() : StateEnum;

    public function assert() : bool;
}