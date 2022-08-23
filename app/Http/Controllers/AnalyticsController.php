<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Channel;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function show(Request $request, Channel $channel)
    {
        $this->authorize($channel);
        // nothing here take a look to
        // app/Http/Livewire/Charts.php

        return view('analytics.show', compact('channel'));
    }
}
