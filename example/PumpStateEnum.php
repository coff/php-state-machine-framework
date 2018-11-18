<?php

namespace Coff\SMF\Example;

use Coff\SMF\StateEnum;

class PumpStateEnum extends StateEnum
{
    const   __default   = self::OFF;
    const   OFF         = 0,
            ON          = 1;

}