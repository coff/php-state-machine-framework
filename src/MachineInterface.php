<?php

namespace Coff\SMF;

interface MachineInterface
{
    public function setDefaultState(StateEnum $state);

    public function getMachineState();

    public function isMachineState(StateEnum $state);

    public function assertState();
}