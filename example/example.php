<?php

namespace Coff\SMF\Example;

use Coff\SMF\Transition\CallbackTransition;
use \Coff\SMF\Transition\Transition;

    $boiler =  new Boiler();

    /* basic state cycle */
    $boiler
        ->setDefaultState(BoilerStateEnum::COLD())
        ->allowTransition(new CallbackTransition(BoilerStateEnum::COLD(), BoilerStateEnum::WARMUP(), function() {

        }))
        ->allowTransition(new CallbackTransition(BoilerStateEnum::WARMUP(), BoilerStateEnum::HOT(), function() {

        }))
        ->allowTransition(new CallbackTransition(BoilerStateEnum::HOT(), BoilerStateEnum::COOLDOWN(), function() {

        }))
        ->allowTransition(new CallbackTransition(BoilerStateEnum::COOLDOWN(), BoilerStateEnum::COLD(), function() {

        }))
    ;

    /* fail state cycle */
    $boiler
        ->allowTransition(new CallbackTransition(BoilerStateEnum::WARMUP(), BoilerStateEnum::COLD(), function() {

        }))
    ;



    $boilerPump = new BoilerPump();

    $boilerPump
        ->setDefaultState(PumpStateEnum::OFF())
        ->allowTransition(new Transition(PumpStateEnum::OFF(), PumpStateEnum::ON()))
        ->allowTransition(new Transition(PumpStateEnum::ON(), PumpStateEnum::OFF()));


