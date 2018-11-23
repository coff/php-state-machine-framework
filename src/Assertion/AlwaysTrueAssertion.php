<?php


namespace Coff\SMF\Assertion;


class AlwaysTrueAssertion extends Assertion
{

    public function make() : bool
    {
        return true;
    }
}