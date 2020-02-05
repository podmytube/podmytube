<div class="row align-items-center">
    <div class="col-8">
        <h2> {{ __('messages.title_channel_index_label') }} </h2>
    </div>
    <!--/col-->
    <div class="col-4">
        <a href="mailto:contact@podmytube.com" class="btn btn-primary"> {{ __('messages.button_need_assistance_label') }} </a>
    </div>
    <!--/col-->
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
                <a href="{{ $channel->podcastUrl() }}" target="_blank" class="btn btn-light btn-lg">{{ __('messages.podcast_link_label') }}</a>
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