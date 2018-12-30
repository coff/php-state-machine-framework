<?php

namespace Coff\SMF\Assertion;


use Coff\SMF\Exception\AssertionException;
use Coff\SMF\MachineInterface;
use Coff\SMF\Transition\TransitionInterface;

class CallbackAssertion extends Assertion
{

    /** @var callable */
    protected $callback;

    /** @var array */
    protected $extraParams = [];

    public function __construct(callable $callback = null, array $extra_params = [])
    {
        $this->callback = $callback;

        $this->extraParams = $extra_params;
    }

    public function setCallback(callable $callback)
    {
        $this->callback = $callback;

        return $this;
    }

    public function setExtraParams($extra_params = [])
    {
        $this->extraParams = $extra_params;

        return $this;
    }

    /**
     * @param MachineInterface $machine
     * @param TransitionInterface $transition
     * @return bool
     * @throws AssertionException
     */
    public function make(MachineInterface $machine, TransitionInterface $transition): bool
    {
        if ($this->callback) {
            return call_user_func_array($this->callback, array_merge([$machine, $transition], $this->extraParams));
        } else {
            throw new AssertionException('Callback not configured!');
        }
    }
}