<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;


/** 
 * TODO
 * Write tests for database, no time right now
 */
class ApiLibraryControllerTest extends WebTestCase
{
    public function testPlaceholder(): void
    {
        $this->assertTrue(true);
    }

    protected static function createKernel(array $options = []): KernelInterface
    {
        return new \App\Kernel('test', true);
    }
}