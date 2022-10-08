<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Jobs\MediaCleaning;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;

/**
 * @internal
 *
 * @coversNothing
 */
class CleanFreeChannelMediasCommandTest extends CommandTestCase
{
    use RefreshDatabase;

    protected string $command = 'medias:clean';

    /** @test */
    public function command_is_running_fine(): void
    {
        Bus::fake();
        $expectedNumberOfMediasToDelete = 4;
        $channel = $this->createChannelWithPlan();

        // medias grabbed X monthes ago => should be deleted
        $mediasToBeDeleted = Media::factory($expectedNumberOfMediasToDelete)
            ->channel($channel)
            ->grabbedAt(now()->subMonths(5))
            ->create()
        ;

        // medias grabbed recently
        Media::factory($expectedNumberOfMediasToDelete)
            ->channel($channel)
            ->grabbedAt(now())
            ->create()
        ;

        $this->artisan($this->command)->assertExitCode(0);
        Bus::assertDispatched(MediaCleaning::class, $expectedNumberOfMediasToDelete);

        $mediasToBeDeleted->each(function (Media $media): void {
            Bus::assertDispatched(
                fn (MediaCleaning $job) => $job->mediaToDelete->youtube_id === $media->youtube_id
            );
        });
    }
}
