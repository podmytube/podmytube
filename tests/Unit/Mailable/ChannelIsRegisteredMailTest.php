<?php

declare(strict_types=1);

namespace Tests\Unit\Mailable;

use App\Mail\ChannelIsRegisteredMail;
use App\Models\Channel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class ChannelIsRegisteredMailTest extends TestCase
{
    use RefreshDatabase;

    protected Channel $channel;

    public function setUp(): void
    {
        parent::setUp();
        $this->channel = $this->createChannelWithPlan();
    }

    /** @test */
    public function channel_is_registered_email_is_fine(): void
    {
        $mailContent = new ChannelIsRegisteredMail($this->channel);

        $mailContent->assertSeeInOrderInHtml([
            "Congratulations {$this->channel->user->firstname}",
            "Channel <b>{$this->channel->title()}</b> is now registered.",
            'In a few minutes, you channel will be validated then your podcast will include your last episodes. ',
            '<b>One last word</b>. If you want to register your podcast on iTunes (You should !) you will have to :',
            'Select your podcast category',
            'Add a podcast illustration (1400x1400 minimum 3000x3000 maximum)',
            '<a href="http://dashboard.pmt.local/channel/' . $this->channel->youtube_id . '/edit" class="button bgsuccess">',
            '<a href="http://dashboard.pmt.local/channel/' . $this->channel->youtube_id . '/cover/edit" class="button bgsuccess">',
            'If you have any question, feel free to answer this email.',
            'Cheers.',
            'Fred',
        ]);
    }
}
