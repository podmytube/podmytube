<?php

namespace Tests\Unit;

use Tests\TestCase;

/**
 * This class is the class test for the GetConfTest class.
 * This class should be running on all environnment
 */

class GetConfTest extends TestCase
{

    protected $defaultDbUser = 'podUser';
    protected $testDbUser = 'podUserTests';

    protected $requiredProperties = [
        "APP_NAME",
        "APP_ENV",
        "APP_KEY",
        "APP_DEBUG",
        "APP_URL",
        "APP_PODCAST_URL",

        "VIRTUAL_HOST",

        "DB_CONNECTION",
        "DB_HOST",
        "DB_PORT",
        "DB_DATABASE",
        "DB_USERNAME",
        "DB_PASSWORD",
        "DB_CHARSET",

        "YOUTUBE_API_KEY",

        "MAIL_DRIVER",
        "MAILGUN_DOMAIN",
        "MAILGUN_SECRET",

        "STRIPE_KEY",
        "STRIPE_SECRET",

        "SENTRY_LARAVEL_DSN",
    ];

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testRequiredProperties()
    {
        foreach ($this->requiredProperties as $property) {
            $this->assertNotEmpty(
                getenv($property),
                "Property {$property} is empty, it shouldn't"
            );
        }
    }

}
