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
    <table class="table table-striped">
        <thead class="thead-dark">
            <tr>
                <th scope="col">{{__('medias.index_table_col_media_title')}}</th>
                <th scope="col">{{__('medias.index_table_col_media_published_at')}}</th>
                <th scope="col">{{__('medias.index_table_col_media_grabbed_at')}}</th>
            </tr>
        </thead>
        <tbody>
        @foreach ($publishedMedias as $media)
        <tr>
            <td>{{ $media->title }}</td>
            <td>{{ $media->published_at->format(__('config.dateFormat')) }}</td>
            <td>
                @if (isset($media->grabbed_at))
                {{ $media->grabbed_at->format(__('config.dateFormat')) }}
                @else
                <span class="badge badge-pill badge-danger">{{__('medias.index_episode_not_included_badge')}}</span>
                @endif
            </td>
        </tr>
        @endforeach
        </tbody>
    </table>

    @if ($shouldChannelBeUpgraded)
    <div style="text-align:center;">
        <p class="text-is-grey">
            {{__('emails.monthlyReport_channelShouldUpgrade_callToAction')}}
        </p>
        <a href="{{ route('plans.index', $channel) }}" class="button bgsuccess">{{ __('emails.common_upgrade_my_plan') }}</a>
    </div>
    @endif

@else
<p>No media published this month</p>
@endif

@endsection