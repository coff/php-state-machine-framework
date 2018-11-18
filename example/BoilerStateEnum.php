<?php


namespace Coff\SMF\Example;


use Coff\SMF\StateEnum;

class BoilerStateEnum extends StateEnum
{

    const   __default   = self::COLD;
    const   COLD         = 0,
            WARMUP       = 1,
            HOT          = 2,
            COOLDOWN     = 3;

}
