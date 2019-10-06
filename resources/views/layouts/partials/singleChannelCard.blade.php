<div class="card">
    <img class="card-img-top" src="{{$channel->vigUrl}}" alt="{{ __('messages.channel_vignette_alt') }}">

    <div class="card-body">
        <h5 class="card-title">
            @if ( $channel->podcast_title )
            {{ $channel->podcast_title }}
            @else
            {{ $channel->channel_name }}
            @endif
        </h5>

        @if ($channel->isQuotaExceeded)
        <div class="alert alert-danger" role="alert">
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
        <div class="btn-group" role="group" aria-label="Basic example">
            <a href="{{ route('channel.show', $channel) }}"  class="btn btn-primary btn-sm">{{ __('messages.button_show_channel_label') }}</a>
            <a href="{{ route('channel.edit', $channel) }}"  class="btn btn-primary btn-sm">{{ __('messages.button_edit_channel_label') }}</a>
            <a href="{{ route('channel.thumbs.edit', $channel) }}"  class="btn btn-primary btn-sm">{{ __('messages.button_edit_thumb_label') }}</a>
            <a href="{{ route('channel.medias.index', $channel) }}"  class="btn btn-primary btn-sm">{{ __('messages.button_view_episodes_label') }}</a>
        </div>
    </div>
    <!--
    <a href="{{ route('plans.index', $channel) }}">
        <button type="button" class="btn btn-success">
            {{ __('messages.button_upgrade_my_plan') }}
        </button>
    </a>
    -->
</div> <!-- /card body -->