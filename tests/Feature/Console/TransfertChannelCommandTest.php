<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Models\Channel;
use App\Models\Media;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use InvalidArgumentException;
use RuntimeException;
use Tests\Traits\Covers;

/**
 * @internal
 *
 * @coversNothing
 */
class TransfertChannelCommandTest extends CommandTestCase
{
    use Covers;
    use RefreshDatabase;

    protected Channel $fromChannel;
    protected User $user;
    protected Plan $plan;
    protected string $destChannelId;

    public function setUp(): void
    {
        parent::setUp();
        Storage::fake('remote');
        $this->seedCategories();

        $this->user = User::factory()->create();
        $this->plan = Plan::factory()->name('starter')->create();

        $this->fromChannel = Channel::factory()
            ->user($this->user)
            ->create([
                'channel_id' => self::JEANVIET_CHANNEL_ID,
            ])
        ;
        Subscription::factory()
            ->plan($this->plan)
            ->channel($this->fromChannel)
            ->create()
        ;
        $this->destChannelId = 'destination';
    }

    /** @test */
    public function missing_from_channel_id_should_throw_exception(): void
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('transfert:channel', ['dest_channel_id' => 'unknown_channel_id']);
    }

    /** @test */
    public function missing_dest_channel_id_should_throw_exception(): void
    {
        $this->expectException(RuntimeException::class);
        $this->artisan('transfert:channel', ['from_channel_id' => 'unknown_channel_id']);
    }

    /** @test */
    public function invalid_from_channel_id_should_throw_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->artisan('transfert:channel', [
            'from_channel_id' => 'unknown_channel_id',
            'dest_channel_id' => $this->destChannelId,
        ]);
    }

    /** @test */
    public function invalid_user_id_should_throw_exception(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->artisan('transfert:channel', [
            'from_channel_id' => self::JEANVIET_CHANNEL_ID,
            'dest_channel_id' => $this->destChannelId,
            '--user_id' => 'not_existing_user_id',
        ]);
    }

    /** @test */
    public function not_existing_destination_channel_should_be_created_with_same_user(): void
    {
        $this->artisan('transfert:channel', [
            'from_channel_id' => $this->fromChannel->id(),
            'dest_channel_id' => $this->destChannelId,
        ])->assertExitCode(0);

        $this->assertDatabaseHas('channels', [
            'channel_id' => $this->destChannelId,
            'user_id' => $this->fromChannel->user_id,
        ]);
    }

    /** @test */
    public function not_existing_destination_channel_should_be_created_with_specified_user(): void
    {
        $this->artisan('transfert:channel', [
            'from_channel_id' => $this->fromChannel->id(),
            'dest_channel_id' => $this->destChannelId,
            '--user_id' => $this->user->id,
        ])->assertExitCode(0);

        $this->assertDatabaseHas('channels', [
            'channel_id' => $this->destChannelId,
            'user_id' => $this->fromChannel->user_id,
        ]);
    }

    /** @test */
    public function no_thumb_should_have_been_ignored(): void
    {
        $this->artisan('transfert:channel', [
            'from_channel_id' => $this->fromChannel->id(),
            'dest_channel_id' => $this->destChannelId,
        ])->assertExitCode(0);

        $destChannel = Channel::byChannelId($this->destChannelId);
        $this->assertFalse($destChannel->hasCover());
    }

    /** @test */
    public function thumb_should_have_been_created(): void
    {
        $this->createCoverFor($this->fromChannel);
        // pushing cover on remote
        Storage::disk('remote')->put(
            path: $this->fromChannel->cover->remoteFilePath(),
            contents: Storage::disk('thumbs')->get($this->fromChannel->cover->relativePath())
        );

        $this->artisan('transfert:channel', [
            'from_channel_id' => $this->fromChannel->id(),
            'dest_channel_id' => $this->destChannelId,
        ])->assertExitCode(0);

        $destChannel = Channel::byChannelId($this->destChannelId);
        $this->assertTrue($destChannel->hasCover(), 'Destination channel should have cover');
        $this->assertEquals($this->fromChannel->cover->file_name, $destChannel->cover->file_name);

        $this->assertFalse(Storage::disk('remote')->exists($destChannel->cover->remoteFilePath()));
    }

    /** @test */
    public function thumb_files_should_have_been_copied(): void
    {
        $this->createCoverFor($this->fromChannel);
        // pushing cover on remote
        Storage::disk('remote')->put(
            path: $this->fromChannel->cover->remoteFilePath(),
            contents: Storage::disk('thumbs')->get($this->fromChannel->cover->relativePath())
        );

        $this->artisan('transfert:channel', [
            'from_channel_id' => $this->fromChannel->id(),
            'dest_channel_id' => $this->destChannelId,
            '--copy' => true,
        ])->assertExitCode(0);

        $destChannel = Channel::byChannelId($this->destChannelId);
        $this->assertTrue($destChannel->hasCover(), 'Destination channel should have cover');
        $this->assertEquals($this->fromChannel->cover->file_name, $destChannel->cover->file_name);

        $this->assertTrue(Storage::disk('remote')->exists($destChannel->cover->remoteFilePath()));
    }

    /** @test */
    public function medias_should_have_been_transferred(): void
    {
        // adding medias to from channel and pushing them on remote
        $expectedMediasNumber = 3;
        $this->addGrabbedMediasToChannel($this->fromChannel, $expectedMediasNumber);
        $this->fromChannel->medias->each(
            fn (Media $media) => Storage::disk('remote')
                ->put(
                    path: $media->remoteFilePath(),
                    contents: fixtures_path('Audio/l8i4O7_btaA.mp3')
                )
        );

        $this->artisan('transfert:channel', [
            'from_channel_id' => $this->fromChannel->id(),
            'dest_channel_id' => $this->destChannelId,
        ])->assertExitCode(0);

        // old channel should have no more medias
        $this->fromChannel->refresh();
        $this->assertCount(0, $this->fromChannel->medias);

        $destChannel = Channel::byChannelId($this->destChannelId);
        $this->assertNotNull($destChannel->medias);
        $this->assertCount($expectedMediasNumber, $destChannel->medias);

        $destChannelMediasFolder = config('app.mp3_path') . $destChannel->channel_id;

        $this->assertCount(0, Storage::disk('remote')->files($destChannelMediasFolder));
    }

    /** @test */
    public function medias_should_have_been_transferred_and_files_copied(): void
    {
        // adding medias to from channel and pushing them on remote
        $expectedMediasNumber = 3;
        $this->addGrabbedMediasToChannel($this->fromChannel, $expectedMediasNumber);
        $this->fromChannel->medias->each(
            fn (Media $media) => Storage::disk('remote')
                ->put(
                    path: $media->remoteFilePath(),
                    contents: fixtures_path('Audio/l8i4O7_btaA.mp3')
                )
        );

        $this->artisan('transfert:channel', [
            'from_channel_id' => $this->fromChannel->id(),
            'dest_channel_id' => $this->destChannelId,
            '--copy' => true,
        ])->assertExitCode(0);

        // old channel should have no more medias
        $this->fromChannel->refresh();
        $this->assertCount(0, $this->fromChannel->medias);

        $destChannel = Channel::byChannelId($this->destChannelId);
        $this->assertNotNull($destChannel->medias);
        $this->assertCount($expectedMediasNumber, $destChannel->medias);

        $fromChannelMediasFolder = config('app.mp3_path') . $this->fromChannel->channel_id;
        $destChannelMediasFolder = config('app.mp3_path') . $destChannel->channel_id;

        $this->assertCount($expectedMediasNumber, Storage::disk('remote')->files($destChannelMediasFolder));

        $destChannel->medias->each(function (Media $media) use ($fromChannelMediasFolder, $destChannelMediasFolder): void {
            $this->assertEquals(
                Storage::disk('remote')->size($fromChannelMediasFolder . '/' . $media->mediaFileName()),
                Storage::disk('remote')->size($destChannelMediasFolder . '/' . $media->mediaFileName())
            );
        });
    }
}
