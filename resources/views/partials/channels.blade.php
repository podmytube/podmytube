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
            <div class="text-center mt-2">
                <img class="card-img-top" src="{{$channel->vigUrl}}" style="max-width:300px;" alt="{{ __('messages.channel_vignette_alt') }}">
            </div>
            <div class="card-body text-center">
                <h5 class="card-title">{{ $channel->title() }}</h5>
                <h6 class="card-subtitle mb-2 text-muted">
                    {{ __('plans.'.$channel->subscription->plan->name) }}
                </h6>
                <a href="{{ $channel->podcastUrl() }}" target="_blank" class="btn btn-success btn">
                    <i class="fas fa-podcast fa-lg"></i> {{ __('messages.podcast_link_label') }}
                </a>

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
            <div class="card-footer p-0 text-center">
                <div class="btn-group " role="group" aria-label="link to your podcast.">
                    <a href="{{ route('channel.show', $channel) }}" class="btn btn-primary btn-sm">
                        <i class="far fa-eye"></i> {{ __('messages.button_show_channel_label') }}
                    </a>
                    <a href="{{ route('channel.edit', $channel) }}" class="btn btn-primary btn-sm">
                        <i class="far fa-edit"></i> {{ __('messages.button_edit_channel_label') }}
                    </a>
                    <a href="{{ route('channel.thumbs.edit', $channel) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-image"></i> {{ __('messages.button_edit_thumb_label') }}
                    </a>
                    <a href="{{ route('channel.medias.index', $channel) }}" class="btn btn-primary btn-sm">
                        <i class="far fa-list-alt"></i> {{ __('messages.button_view_episodes_label') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@else
<div class="text-center alert alert-secondary">
    <p>{{__('messages.no_channels_at_this_moment')}}</p>
    <a class="btn btn-success" href="{{ route('channel.create') }}"><i class="fas fa-plus"></i> {{ __('messages.button_create_channel_label') }} </a>
</div>
@endif