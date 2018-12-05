<?php


namespace Coff\SMF\Test;


use Coff\SMF\Assertion\AlwaysFalseAssertion;
use PHPUnit\Framework\TestCase;

class AlwaysFalseAssertionTest extends TestCase
{
    /** @var AlwaysFalseAssertion */
    protected $assertion;

    public function setUp()
    {
        $this->assertion = new AlwaysFalseAssertion();
    }


    public function test_make()
    {
        $this->assertEquals(false, $this->assertion->make());
    }
}