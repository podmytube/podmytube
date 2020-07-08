@extends('emails.layout')

@section('mailTitle', $mailTitle )

@section ('content')

<p>
    {{ __('emails.common_hello', ['name' => $channel->user->name]) }}
</p>

<p>
    {{$mailTitle}}
</p>

@if ($publishedMedias->count())
<table>
    <thead>
        <tr>
            <th class="bordered" width="50%">Title</th>
            <th class="bordered" width="25%">Published</th>
            <th class="bordered" width="25%">Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($publishedMedias as $media)
        <tr>
            <td class="bordered" style="text-align:left;">{{ $media->title }}</td>
            <td class="bordered" style="text-align:center;">{{ $media->published_at->format(__('config.dateFormat')) }}</td>
            <td class="bordered" style="text-align:center;">
                @if (isset($media->grabbed_at))
                <span class="text-success">Ok</span>
                @else
                <span class="text-danger">Fail</span>
                @endif
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