@extends('emails.layout')

@section('mailTitle', $mailTitle )

@section ('content')

<style>
.h-4 {
	height: 1.25rem;
}
.h-6 {
	height: 1.5rem;
}
.vamiddle{
    vertical-align:middle;
}
.fill-current {
	fill: currentColor;
}
.w-auto {
	width: auto;
}
.bold{
    font-weight: 600;
}
.button{
    background-color: #059669;
    color: #F9FAFB;
    box-shadow: 0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06) !important;
    text-transform: uppercase;
    padding-top: .625rem;
    padding-bottom: .625rem;
    padding-left: 1.25rem;
    padding-right: 1.25rem;
    font-size: .875rem;
    line-height: 1.25rem;
    letter-spacing: 0.10rem;
    border-radius: .375rem;
    /* display: flex;
    align-items: center; */
}
table,tr,td{
 	--border-opacity: 1;
	border-color: rgba(255, 255, 255, var(--border-opacity));
}
table {
  width: 100%;
}
td{
    padding: 0.5rem;
 	--text-opacity: 1;
	color: rgba(26, 32, 44, var(--text-opacity));
}
th {
    padding: 0.75rem;
 	--bg-opacity: 1;
	background-color: rgba(26, 32, 44, var(--bg-opacity));
    color: white;
}
tr:nth-child(even){background-color: #f2f2f2;}
</style>


<p>
    {{ __('emails.common_hello', ['name' => $channel->user->firstname]) }} 👋
</p>

<p>
    Here is your <span class="bold">{{ $period }}</span> report for <strong>{{ $channel->channel_name }}</strong>
</p>



<p>
    To know what's coming next on Podmytube, you should take a look (and vote) on 
    <a href="https://trello.com/b/g7YXh4OX/podmytube-roadmap" target="_blank">
        my public roadmap 
        <svg class="h-4 vamiddle" viewBox="0 0 256 256" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" preserveAspectRatio="xMidYMid">
            <defs>
                <linearGradient x1="50%" y1="0%" x2="50%" y2="100%" id="linearGradient-1">
                    <stop stop-color="#0091E6" offset="0%"></stop>
                    <stop stop-color="#0079BF" offset="100%"></stop>
                </linearGradient>
            </defs>
            <g>
                <g>
                    <rect fill="url(#linearGradient-1)" x="0" y="0" width="256" height="256" rx="25"></rect>
                    <rect fill="#FFFFFF" x="144.64" y="33.28" width="78.08" height="112" rx="12"></rect>
                    <rect fill="#FFFFFF" x="33.28" y="33.28" width="78.08" height="176" rx="12"></rect>
                </g>
            </g>
        </svg>
    </a> or follow 
    <a href="https://twitter.com/podmytube" target="_blank">
        @podmytube
        <svg class="vamiddle" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" xmlns="http://www.w3.org/2000/svg" height="24" width="24" version="1.1" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/">
            <g transform="translate(0 -1028.4)">
                <g>
                    <path d="m4 1031.4c-1.1046 0-2 0.9-2 2v16c0 1.1 0.8954 2 2 2h16c1.105 0 2-0.9 2-2v-16c0-1.1-0.895-2-2-2h-16z" fill="#1499ca"/>
                    <path d="m4 2c-1.1046 0-2 0.8954-2 2v16c0 1.105 0.8954 2 2 2h16c1.105 0 2-0.895 2-2v-16c0-1.1046-0.895-2-2-2h-16z" transform="translate(0 1028.4)" fill="#1ab2e8"/>
                    <path fill="#1499ca" d="m15.474 1036.5c-0.811 0-1.532 0.2-2.107 0.8-0.569 0.6-0.835 1.3-0.835 2.1 0 0.2 0.024 0.4 0.072 0.7-1.198-0.1-2.325-0.4-3.378-0.9-1.0475-0.6-1.9556-1.3-2.6882-2.2-0.2664 0.4-0.3996 0.9-0.3996 1.5 0 0.5 0.1272 1 0.3633 1.4s0.5449 0.7 0.9445 1c-0.4723 0-0.8961-0.1-1.3078-0.4v0.1c0 0.7 0.2119 1.3 0.6539 1.9 0.448 0.5 1.0292 0.8 1.7073 1-0.2543 0-0.5388 0.1-0.7992 0.1-0.1695 0-0.3451 0-0.5448-0.1 0.1876 0.6 0.5509 1.1 1.0534 1.5s1.0716 0.5 1.7074 0.6c-1.0656 0.8-2.3068 1.2-3.669 1.2h-0.6902c1.3622 0.8 2.8637 1.3 4.504 1.3 1.042 0 2.029-0.2 2.943-0.5s1.677-0.8 2.325-1.3c0.648-0.6 1.205-1.2 1.671-1.9 0.472-0.8 0.823-1.5 1.053-2.3s0.363-1.6 0.363-2.4c0-0.1-0.03-0.3-0.036-0.4 0.575-0.4 1.084-0.8 1.49-1.4-0.564 0.2-1.145 0.3-1.708 0.4 0.636-0.4 1.084-0.9 1.308-1.6-0.581 0.3-1.193 0.6-1.853 0.7-0.581-0.6-1.295-0.9-2.143-0.9z"/>
                    <path fill="#ecf0f1" d="m15.474 1035.5c-0.811 0-1.531 0.2-2.107 0.8-0.569 0.6-0.835 1.3-0.835 2.1 0 0.2 0.024 0.4 0.073 0.7-1.199-0.1-2.325-0.4-3.3788-0.9-1.0474-0.6-1.9556-1.3-2.6882-2.2-0.2664 0.4-0.3995 0.9-0.3995 1.5 0 0.5 0.1271 1 0.3632 1.4s0.5449 0.7 0.9445 1c-0.4722 0-0.8961-0.1-1.3077-0.4v0.1c0 0.7 0.2119 1.3 0.6538 1.9 0.448 0.5 1.0293 0.8 1.7074 1-0.2543 0-0.5389 0.1-0.7992 0.1-0.1695 0-0.3451 0-0.5449-0.1 0.1877 0.6 0.5509 1.1 1.0534 1.5 0.5026 0.4 1.0717 0.5 1.7074 0.6-1.0656 0.8-2.3067 1.2-3.669 1.2h-0.6902c1.3623 0.8 2.8637 1.3 4.5048 1.3 1.041 0 2.028-0.2 2.942-0.5s1.677-0.8 2.325-1.3c0.648-0.6 1.205-1.2 1.671-1.9 0.472-0.8 0.823-1.5 1.053-2.3 0.231-0.8 0.364-1.6 0.364-2.4 0-0.1-0.031-0.3-0.037-0.4 0.576-0.4 1.084-0.8 1.49-1.4-0.563 0.2-1.145 0.3-1.708 0.4 0.636-0.4 1.084-0.9 1.308-1.6-0.581 0.3-1.193 0.6-1.852 0.7-0.582-0.6-1.296-0.9-2.144-0.9z"/>
                </g>
            </g>
        </svg>
    </a>.
</p>


@if ($publishedMedias->count())
<table >
    <thead>
        <tr>
            <th width="50%">Title</th>
            <th width="25%">Published</th>
            <th width="25%">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($publishedMedias as $media)
        <tr>
            <td style="text-align:left;">{{ $media->title }}</td>
            <td style="text-align:center;">{{ $media->published_at->format(__('config.dateFormat')) }}</td>
            <td style="text-align:center;">
                @include('svg.media_status_'.$media->status, [
                            'cssClass' => 'h-6 w-auto inline fill-current',
                            'comment' => $media->statusComment()
                            ])
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@if ($shouldChannelBeUpgraded)
<div style="text-align:center;">
    <p>
        {{ __('emails.monthlyReport_channelShouldUpgrade_callToAction') }}
    </p>
    <a href="{{ route('plans.index', $channel) }}" class="button">
        Upgrade
    </a>
</div>
@endif

@else
<p> {{ __('emails.monthlyReport_no_media_published') }} </p>
@endif


@endsection