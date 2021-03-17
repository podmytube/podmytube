@extends('emails.layout')

@section('mailTitle', $mailTitle )

@section ('content')

<p>
    {{ __('emails.common_hello', ['name' => $channel->user->firstname]) }} 👋
</p>

<p>
    {{$mailTitle}}
</p>

<style>
.h-6 {
	height: 1.5rem;
}
.fill-current {
	fill: currentColor;
}
.w-auto {
	width: auto;
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

<div>
    follow @podmytube on twitter.
    ask for features on the podmytube public roadmap
</div>


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
    <p class="text-is-grey">
        {{ __('emails.monthlyReport_channelShouldUpgrade_callToAction') }}
    </p>
    <a href="{{ route('plans.index', $channel) }}" class="button bgsuccess">{{ __('emails.common_upgrade_my_plan') }}</a>
</div>
@endif

@else
<p> {{ __('emails.monthlyReport_no_media_published') }} </p>
@endif


@endsection