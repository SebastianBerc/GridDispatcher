<?php

namespace SebastianBerc\GridDispatcher\Tests;

/**
 * Class TestCase
 *
 * @author  Sebastian Berć <sebastian.berc@gmail.com>
 * @package SebastianBerc\GridDispatcher\Tests
 */
class TestCase extends \PHPUnit_Framework_TestCase
{
    protected function asPublic($className, $method)
    {
        $method = (new \ReflectionClass($className))->getMethod($method);
        $method->setAccessible(true);

        return $method;
    }
}
