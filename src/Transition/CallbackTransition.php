<?php


namespace Coff\SMF\Transition;


use Coff\SMF\StateEnum;

class CallbackTransition extends Transition
{

    /** @var callable */
    protected $callback;


    public function __construct(StateEnum $from, StateEnum $to, callable $callback)
    {
        $this->fromState = $from;
        $this->toState = $to;
        $this->callback = $callback;
    }

    public function assert(): bool
    {
        return $this->callback();
    }
}