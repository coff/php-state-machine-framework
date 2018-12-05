<?php


namespace Coff\SMF\Test;


use Coff\SMF\Assertion\AlwaysTrueAssertion;
use PHPUnit\Framework\TestCase;

class AlwaysTrueAssertionTest extends TestCase
{
    /** @var AlwaysTrueAssertion */
    protected $assertion;

    public function setUp()
    {
        $this->assertion = new AlwaysTrueAssertion();
    }


    public function test_make()
    {
        $this->assertEquals(true, $this->assertion->make());
    }
}