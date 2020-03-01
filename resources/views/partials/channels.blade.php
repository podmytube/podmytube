<div class="row align-items-center">
    <div class="col">
        <h2> {{ __('messages.title_channel_index_label') }} </h2>
    </div>    
</div>
<!--/subhead row-->


@if (count($channels))
<div class="row">
    @foreach ($channels as $channel)
    <div class="col-12 col-md-6">
        <div class="card shadow-md">
            <div class="text-center">
                <img class="card-img-top" src="{{$channel->vigUrl}}" style="max-width:300px;" alt="{{ __('messages.channel_vignette_alt') }}">
            </div>
            <div class="card-body text-center">
                <h5 class="card-title">{{$channel->title()}}</h5>
                <a href="{{ $channel->podcastUrl() }}" target="_blank" class="btn btn-primary btn">{{ __('messages.podcast_link_label') }}</a>

                @if ($channel->isQuotaExceeded)
                <div class="alert alert-danger mt-2" role="alert">
                    <p>{{ __('messages.danger_podcast_is_no_more_updated') }}</p>
                    <p class=" text-center">
                        <a class="btn btn-success text-center" href="{{ route('plans.index', ['channel' => $channel]) }}" role="button">
                            {{ __('messages.button_i_want_to_upgrade_now') }}
                        </a>
                    </p>
                </div>
                @endif
                
            </div>
            <div class="card-footer text-center">
                <div class="btn-group " role="group" aria-label="link to your podcast.">
                    <a href="{{ route('channel.show', $channel) }}" class="btn btn-primary btn-sm">{{ __('messages.button_show_channel_label') }}</a>
                    <a href="{{ route('channel.edit', $channel) }}" class="btn btn-primary btn-sm">{{ __('messages.button_edit_channel_label') }}</a>
                    <a href="{{ route('channel.thumbs.edit', $channel) }}" class="btn btn-primary btn-sm">{{ __('messages.button_edit_thumb_label') }}</a>
                    <a href="{{ route('channel.medias.index', $channel) }}" class="btn btn-primary btn-sm">{{ __('messages.button_view_episodes_label') }}</a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center alert alert-secondary">
    {{__('messages.no_channels_at_this_moment')}}
</div>
@endif