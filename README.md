
# State Machine Framework for PHP

State Machines are not what you usually do with PHP but when you do... just use this.

  
It's a work-in-progress project! Wanna contribute? Let me know.

Usage example:

```php
<?php 
    
    class PetitionEnum extends StateEnum {
        const __default     = self::DRAFT,
              DRAFT         = 'draft',
              SENT          = 'sent',
              VOTED         = 'voted',
              ACCEPTED      = 'accepted',
              REJECTED      = 'rejected',
              CANCELED      = 'canceled';
              
    }

    class Petition extends Machine {
        
        protected $votesYes, $votesNo;
    
        public function init() {
            $this->setInitState(PetitionEnum::DRAFT());
            
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
            if (null !== $this->votesYes && null !== $this->votesNo)
            {
                return true;
            }
            
            return false;
        }
        
        
        public function assertVotedToAccepted()
        {
            if ($this->votesYes > $this->votesNo) 
            { 
                return true;
            }
            
            return false;
        }
        
        public function assertVotedToRejected() 
        {
            if ($this->votesYes <= $this->votesNo)
            {
                return true;
            }    
            
            return false;
        }
        
        public function onTransition(Transition $transition) 
        {
            /* for purpose of this example we only echo this transition but you can 
               easily dispatch an event from here */ 
            echo 'State changed from ' . $transition->getFromState() . ' to ' . $transition->getToState() . PHP_EOL;
        }
    }
    
    
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
    