<?php

/*
 * This file is part of Casa-Parks/Extract-Translations.
 *
 * (c) Connor S. Parks
 */

namespace Tests;

use Mockery;
use PHPUnit_Framework_TestCase;

class ServiceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tear down the test case.
     *
     * @return void
     */
    public function tearDown()
    {
        Mockery::close();
    }
}
