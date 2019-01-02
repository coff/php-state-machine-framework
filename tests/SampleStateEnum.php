<?php


namespace Coff\SMF\Test;


use Coff\SMF\StateEnum;

/**
 * Sample State's dictionary
 *
 * @method static ONE()
 * @method static TWO()
 * @method static THREE()
 */
class SampleStateEnum extends StateEnum
{
    const   __default = self::ONE,
        ONE = 'one',
        TWO = 'two',
        THREE = 'three';
}