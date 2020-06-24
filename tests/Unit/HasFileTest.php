<?php

namespace Tests\Unit;

use App\Thumb;
use App\Traits\HasFile;
use Tests\TestCase;

class HasFileTest extends TestCase
{
    /** @var Object $hasFileObj */
    protected $hasFileObj;

    public function setUp(): void
    {
        parent::setup();
        $this->hasFileObj = new class {
            use HasFile;
        };
    }

    public function testFoo()
    {
        $this->assertTrue(true);
    }

    /*  public function testDefaultThumbShouldExists()
    {
        $this->assertTrue(
            $this->hasFileObj
                ->defineFileRequirements('thumbs', Thumb::DEFAULT_THUMB_FILE)
                ->exists()
        );
    }

    public function testUnknownFileShouldNotExists()
    {
        $this->assertFalse(
            $this->hasFileObj
                ->defineFileRequirements(
                    'thumbs',
                    'i_told_you_1_million_times_this_file_will_never_exist'
                )
                ->exists()
        );
    }

    public function testUnknownDiskShouldThrowException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->hasFileObj
            ->defineFileRequirements(
                'this_disk_will_probably_never_exists_too',
                'whatever'
            )
            ->exists();
    } */
}
