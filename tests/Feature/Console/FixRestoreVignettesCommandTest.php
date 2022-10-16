<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Jobs\CreateVignetteFromThumbJob;
use App\Models\Channel;
use App\Models\Thumb;
use Google\Service\Storagetransfer\TransferJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\Traits\Covers;

/**
 * @internal
 *
 * @coversNothing
 */
class FixRestoreVignettesCommandTest extends CommandTestCase
{
    use Covers;
    use RefreshDatabase;

    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = Channel::factory()->create();
    }

    /** @test */
    public function channel_with_no_cover_should_stay_unchanged(): void
    {
        $this->assertFalse($this->channel->hasCover());

        $this->artisan('fix:restore-vignettes')->assertExitCode(0);
        $this->channel->refresh();

        $this->assertFalse($this->channel->hasCover());
        $this->assertEquals(defaultVignetteUrl(), $this->channel->vignette_url);
    }

    /** @test */
    public function channel_with_vignette_file_should_stay_unchanged(): void
    {
        $thumb = Thumb::factory()->create();
        $this->channel->attachCover($thumb);

        // touching vignette file
        $previousVignetteFilePath = $this->vignetteFilePath($this->channel);
        Storage::disk('vignettes')->put($this->vignetteFilePath($this->channel), '');

        $this->assertTrue($this->channel->hasVignette());

        $this->artisan('fix:restore-vignettes')->assertExitCode(0);
        $this->channel->refresh();

        $this->assertTrue($this->channel->hasVignette());
        $this->assertEquals($previousVignetteFilePath, $this->channel->vignetteRelativePath());
    }

    /** @test */
    public function channel_with_cover_but_no_vignette_should_dispatch(): void
    {
        $this->markTestIncomplete('job chaining');
        Bus::fake(CreateVignetteFromThumbJob::class);

        $channel = Channel::factory()->create();
        Thumb::factory()->channel($channel)->create();

        $this->assertFalse($channel->hasVignette());

        $this->artisan('fix:restore-vignettes')->assertExitCode(0);

        Queue::assertPushedOn('podwww', TransferJob::class);
        Bus::assertDispatched(CreateVignetteFromThumbJob::class, 1);
    }
}
