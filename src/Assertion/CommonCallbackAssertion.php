<?php


namespace Coff\SMF\Assertion;


use Coff\SMF\Exception\AssertionException;
use Coff\SMF\MachineInterface;
use Coff\SMF\Transition\TransitionInterface;

class CommonCallbackAssertion extends CallbackAssertion
{
    /** @var MachineInterface $object */
    protected $object;

    /** @var TransitionInterface $transition */
    protected $transition;
    
    public function __construct()
    {
        parent::__construct();
    }
    
    
    public function setObject(MachineInterface $object) 
    {
        $this->object = $object;
        
        return $this;    
    }
    
    public function setTransition(TransitionInterface $transition) 
    {
        $this->transition = $transition;    
        
        return $this;
    }

    /**
     * @return bool
     * @throws AssertionException
     */
    public function make(): bool
    {
        if ($this->object instanceof MachineInterface && $this->transition instanceof TransitionInterface) 
        {
            return call_user_func_array([$this->object,'assertTransition'], [$this->transition]);
        }
        else 
        {
            throw new AssertionException('Callback not configured!');
        }
    }

}