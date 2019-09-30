<div class="row align-items-center">
    <!--subhead row-->

    <div class="col-6">

        <h2> {{ __('messages.title_channel_index_label') }} </h2>

    </div>
    <!--/col-->

    <div class="col-3 text-center">
        <a href="{{ route('channel.create') }}">
            <button type="button" class="btn btn-primary">
                {{ __('messages.button_create_channel_label') }}
            </button>
        </a>
    </div>
    <!--/col-->

    <div class="col-3">

        <a href="mailto:contact@podmytube.com">
            <button type="button" class="btn btn-primary">
                {{ __('messages.button_need_assistance_label') }}
            </button>
        </a>

    </div>
    <!--/col-->

</div>
<!--/subhead row-->

<div class="container">
    <!--channel container-->

    @if ( !empty($channels) )

    <div class="card-group">

        @foreach ($channels as $channel)
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
                <a href="{{ route('channel.show', $channel) }}">
                    <button type="button" class="btn btn-primary btn-sm">
                        {{ __('messages.button_show_channel_label') }}
                    </button>
                </a>
                <a href="{{ route('channel.edit', $channel) }}">
                    <button type="button" class="btn btn-primary btn-sm">
                        {{ __('messages.button_edit_channel_label') }}
                    </button>
                </a>
                <a href="{{ route('channel.thumbs.edit', $channel) }}">
                    <button type="button" class="btn btn-primary btn-sm">
                        {{ __('messages.button_edit_thumb_label') }}
                    </button>
                </a>
            </div>
            <!--
                    <a href="{{ route('plans.index', $channel) }}">
                        <button type="button" class="btn btn-success">
                            {{ __('messages.button_upgrade_my_plan') }}
                        </button>
                    </a>
                    -->
        </div> <!-- /card body -->


        @endforeach

    </div> <!-- /card-group -->

    @else

    {{__('messages.no_channels_at_this_moment')}}

    @endif
</div>
<!--/channel container-->