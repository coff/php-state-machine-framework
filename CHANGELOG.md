# CHANGELOG

## dev-decoupled-transition-cycle

- [BC break] decoupled transition `Schema` from actual state machine for easier DI of a whole schema
- [BC break] removed `Machine::allowTransition()` and several other transition related methods from `MachineInterface` 
  and `Machine` implementation
- refactored Exception classes into one hierarchy with one global `SMFException`
- [BC break] `CommonCallbackAssertion` and `DefaultCallbackAssertion` rebuilt to run-time machine object so methods
  `setObject()` and `setTransition` are no longer valid
- [BC break] `Transition` class now uses run-time machine object so method `assert()` got additional parameter `$machine`
- [BC break] `Schema::addTransition()` (before `Machine::addTransition()`) now throws `ConfigurationException` instead of
  `MachineException`
- added `TransitionInterface::onTransition()` method
- [BC break] `Machine::setMachineState` now throws `ConfigurationException` when `Schema` object not set
- unit tests rewritten
- [BC break] `Transition::assert()` doesn't throw `TransitionException` anymore, false is returned if there are no 
  assertions defined
- `Transition` unit tests written 

## v1.0.0

- `MachineTest`: more detailed tests implemented 
- added installation procedure to README.md  
- added `MachineInterface::runOnce()` and `Machine::runOnce()` to allow just one pass over possible transitions
- started actual CHANGELOG.md
- small fix for example used in documentation (missing `$machine->init()` call)
- implemented tests for assertion classes
- updated and extended documentation (README.md)

## v0.3

- README.md updated
- created CODE_OF_CONDUCT.md
- created CONTRIBUTING.md

## v0.2

- defined php version requirements
- fixed `Machine::setMachineState()` behavior with exactly same state given in param
- implemented basic tests

## v0.1

- basic functionality and architecture implementation, first working version