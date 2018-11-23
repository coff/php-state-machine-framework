
*Remark: It's a work-in-progress project! Wanna contribute? Let me know.*

# State Machine Framework for PHP

Maybe state machines are not what you usually do with PHP but when you do... just use this.


## Usage example

### State dictionary

```php
    
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
            
            // defines machine's allowed behavior, when no Assertion is given uses DefaultCallbackAssertion
            $this
                ->allowTransition(PetitionEnum::DRAFT(), PetitionEnum::SENT())
                ->allowTransition(PetitionEnum::DRAFT(), PetitionEnum::CANCELED())
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
    