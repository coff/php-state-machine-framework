<?php

namespace Coff\SMF\Assertion;

/**
 * Basic Assertion Class
 *
 * Its job is to always return true.
 *
 *
 * @package Coff\SMF\Assertion
 */
class Assertion implements AssertionInterface
{

    public function make() : bool
    {
        return true;
    }
}