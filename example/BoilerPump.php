<?php

namespace Coff\SMF\Example;

use Coff\SMF\Machine;

class BoilerPump extends Machine
{
    public function on() {
        $this->setMachineState(PumpStateEnum::ON());
    }

    public function off() {
        $this->setMachineState(PumpStateEnum::OFF());
    }
}
