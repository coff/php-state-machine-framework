<?php


namespace Coff\SMF\Assertion;

/**
 * False Assertion for unit testing
 */
class FalseAssertion extends Assertion
{
    public function make(): bool
    {
        return false;
    }
}