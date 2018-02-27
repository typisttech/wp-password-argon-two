<?php

declare(strict_types=1);

namespace TypistTech\WPPasswordArgonTwo;

use Codeception\TestCase\WPTestCase;

class MDFiveValidatorTest extends WPTestCase
{
    private const DUMMY_PASSWORD = 'pa5$word';
    private const DUMMY_CIPHERTEXT = '04b1287c833a5a335bbc9f4284b0fc8c';

    /** @test */
    public function it_implements_password_validator_interface()
    {
        $validator = new MDFiveValidator();

        $this->assertInstanceOf(ValidatorInterface::class, $validator);
    }

    /** @test */
    public function it_checks_correct_password()
    {
        $validator = new MDFiveValidator();

        $isValid = $validator->isValid(self::DUMMY_PASSWORD, self::DUMMY_CIPHERTEXT);

        $this->assertTrue($isValid);
    }

    /** @test */
    public function it_checks_incorrect_password()
    {
        $validator = new MDFiveValidator();

        $isValid = $validator->isValid('incorrect password', self::DUMMY_CIPHERTEXT);

        $this->assertFalse($isValid);
    }
}
