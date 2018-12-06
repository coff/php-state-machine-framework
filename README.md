# State Machine Framework for PHP

Maybe state machines are not what you usually do with PHP but when you do... just use this.
This simple yet powerful framework will keep your state machines within their desired state 
transition cycles. You can also modify their behavior whilst running or just configure them 
dynamically and launch. You can define state transition conditions you want based upon 
anonymous functions or class methods. Fire events on each state transition with your favorite
event dispatcher.

## Installation

Easiest way is to use composer:

```bash
$ composer require coff/state-machine-framework
```

Alternatively just clone repository:
```bash
$ git clone https://github.com/coff/php-state-machine-framework.git
```

and then checkout specific tag:
```bash
$ git checkout tags/v0.3
```

## Usage example

### States' dictionary

For clarity each state machine should have its own state dictionary defined.

```php
    
    /**
     * @method static DRAFT()
     * @method static SENT()
     * ... // this should make your IDE believe these methods exist
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
              
              // States names should be defined lowercase without spaces and special characters due to 
              // automatically determined assertion method names when in use with DefaultCallbackAssertion
              // @todo replace DefaultCallbackAssertion behavior in this matter 
              
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
            // condition for transition from state VOTED to ACCEPTED
            return $this->votesYes > $this->votesNo ? true : false;
        }
        
        public function assertVotedToRejected() 
        {
            // condition for transition from state VOTED to REJECTED
            return $this->votesYes <= $this->votesNo ? true : false;
        }
        
        public function onTransition(Transition $transition) 
        {
            // for purpose of this example we only echo this transition but you can easily dispatch an event from here 
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

### Transition object

Each transition object should have one or more assertion objects attached.

*Remark: By default (when no assertion object is given as parameter) `Machine::allowTransition()` method attaches `DefaultCallbackAssertion`.*

    
### Assertion behaviors

#### AlwaysTrueAssertion

Results in automatic transition when machine is launched. 

Be aware:

```php
    $machine
        ->allowTransition(MachineEnum::ONE(), MachineEnum::TWO(), new AlwaysTrueAssertion)
        ->allowTransition(MachineEnum::TWO(), MachineEnum::ONE(), new AlwaysTrueAssertion)
        
    $machine->run();
    // this will result in endless loop of state changes
```

#### AlwaysFalseAssertion

Results in no transition when machine is launched. Use this kind of assertion when machine state is supposed to be 
changed upon `setMachineState()` method call only. 

#### DefaultCallbackAssertion

Calls `assertXToY` method on machine object (or other object if specified) and makes transition decision upon its return.

#### CommonCallbackAssertion

Calls `assertTransition` method on machine object (or other object if specified) and makes transition decision upon its return.

#### CallbackAssertion

Calls user specified method to assert if state transition should proceed.
