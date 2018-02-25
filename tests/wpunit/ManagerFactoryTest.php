<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

use Codeception\TestCase\WPTestCase;

class ManagerFactoryTest extends WPTestCase
{
    /** @test */
    public function it_makes_a_manager_instance()
    {
        $actual = ManagerFactory::make();

        $this->assertInstanceOf(Manager::class, $actual);
    }
}
