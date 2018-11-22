<?php

namespace Coff\SMF\Assertion;


use Coff\SMF\Exception\AssertionException;

class CallbackAssertion extends Assertion
{

    /** @var callable */
    protected $callback;

    /** @var array */
    protected $params;

    public function __construct(callable $callback = [], array $params = [])
    {
        $this->callback = $callback;

        $this->params = $params;
    }

    public function setCallback(callable $callback) {
        $this->callback = $callback;

        return $this;
    }

    public function setParams($params = []) {
        $this->params = $params;

        return $this;
    }

    public function make(): bool
    {
        if ($this->callback) {
            return call_user_func_array($this->callback, $this->params);
        }
        else {
            throw new AssertionException('Callback not configured!');
        }
    }
}