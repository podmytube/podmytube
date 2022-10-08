<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Channel;

class AnalyticsController extends Controller
{
    public function show(Channel $channel)
    {
        $this->authorize($channel);

        // nothing here take a look to
        // app/Http/Livewire/Charts.php

        return view('analytics.show', compact('channel'));
    }
}
