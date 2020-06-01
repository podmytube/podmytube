<?php

namespace Tests\Unit;

use App\Exceptions\InvalidUrlException;
use App\Exceptions\QueryFailureException;
use App\Modules\Query;
use Tests\TestCase;

class QueryModuleTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function testQueryingGoogleShouldBeOk()
    {
        $this->assertEquals(
            0,
            Query::create('https://www.google.com')
                ->run()
                ->errorCode()
        );
    }

    public function testQueryingInvalidUrlShouldFail()
    {
        $this->expectException(InvalidUrlException::class);
        Query::create('this domain is invalid');
    }

    public function testQueryingNonExistingDomainShouldFail()
    {
        $this->expectException(QueryFailureException::class);
        Query::create('http://www.this-domain-will-never-exist.fr')->run();
    }
}
