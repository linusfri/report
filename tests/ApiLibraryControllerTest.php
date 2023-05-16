<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use App\Kernel;

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

    /**
     * createKernel
     *
     * @param array<mixed> $options
     * @return KernelInterface
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        return new Kernel('test', true);
    }
}