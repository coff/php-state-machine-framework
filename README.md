# State Machine Framework for PHP

Maybe state machines are not what you usually do with PHP but when you do... just use this.
This simple yet powerful framework will keep your state machines within their desired state 
transition cycles. You can also modify their behavior whilst running or just configure them 
dynamically and launch. You can define state transition conditions you want based upon 
anonymous functions or class methods. Fire events on each state transition with your favorite
event dispatcher.


## Usage example

### States' dictionary

```php
    
    /**
     * @method static DRAFT()
     * @method static SENT()
     * ... // this should make your IDE believe those method exist
     *
     */
    class PetitionEnum extends StateEnum {
        const __default     = self::DRAFT,
              DRAFT         = 'draft',
              SENT          = 'sent',
              VOTED         = 'voted',
              ACCEPTED      = 'accepted',
              REJECTED      = 'rejected',
              CANCELED      = 'canceled';
              
    }
```
### Machine class

```php
    class Petition extends Machine {
        
        protected $votesYes, $votesNo;
    
        public function init() {
            $this->setInitState(PetitionEnum::DRAFT());
            
            // defines machine's allowed behavior
            $this
                // prevents changing state upon assertion when AlwaysFalseAssertion is given
                ->allowTransition(PetitionEnum::DRAFT(), PetitionEnum::SENT(), new AlwaysFalseAssertion())
                ->allowTransition(PetitionEnum::DRAFT(), PetitionEnum::CANCELED(), new AlwaysFalseAssertion())
                
                // when no Assertion is given uses DefaultCallbackAssertion which calls assertXToY methods
                ->allowTransition(PetitionEnum::SENT(), PetitionEnum::VOTED())
                ->allowTransition(PetitionEnum::VOTED(), PetitionEnum::ACCEPTED())
                ->allowTransition(PetitionEnum::VOTED(), PetitionEnum::REJECTED())
                ;
            
        }
        
        public function send() 
        {
            // shall throw an exception if current state is not DRAFT because it wasn't allowed transition
            $this->setMachineState(PetitionEnum::SENT());
        }
        
        public function cancel() 
        {
            // shall throw an exception if current state is not DRAFT because it wasn't allowed transition
            $this->setMachineState(PetitionEnum::CANCELED());
        }

        
        public function setVotes($ya, $nay) 
        {
            $this->votesYes = $ya;
            $this->votesNo = $nay;
        }
        
        public function assertSentToVoted() 
        {
            // Method name used here is based upon DefaultCallbackAssertion. This can be changed though.
            return (null !== $this->votesYes && null !== $this->votesNo) ? true : false;
        }
        
        
        public function assertVotedToAccepted()
        {
            return $this->votesYes > $this->votesNo ? true : false;
        }
        
        public function assertVotedToRejected() 
        {
            return $this->votesYes <= $this->votesNo ? true : false;
        }
        
        public function onTransition(Transition $transition) 
        {
            /* for purpose of this example we only echo this transition but you can 
               easily dispatch an event from here */ 
            echo 'State changed from ' . $transition->getFromState() . ' to ' . $transition->getToState() . PHP_EOL;
        }
    }
```

### Machine in-use    
    
```php

    $p = new Petition();
    $p->init();
    
    $p->run();
    // <nothing happens>
    
    $p->send();
    // State changed from draft to sent
    
    $p->run();
    // <nothing happens>
    
    $p->setVotes(5,1);
    
    $p->run();
    // State changed from sent to voted
    // State changed from voted to accepted
    
```    
    
