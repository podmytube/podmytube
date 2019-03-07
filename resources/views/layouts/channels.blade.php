<div class="row align-items-center"> <!--subhead row-->
		
    <div class="col">

        <h2> {{ __('messages.title_channel_index_label') }} </h2>

    </div> <!--/col-->

    <div class="col text-center">

        <a href="{{ route('channel.create') }}">
            <button type="button" class="btn btn-primary">
                {{ __('messages.button_create_channel_label') }}
            </button>
        </a>

    </div> <!--/col-->
    
</div> <!--/subhead row-->

<div class="container"> <!--channel container-->

    @if ( count($channels) > 0 )
                
        <div class="row"> 

        @foreach ($channels as $channel)

            <div class="col-4 card">

                <img class="card-img-top" 
                    src="{{ $channel->vigUrl }}" 
                    alt="{{ __('messages.channel_vignette_alt') }}"
                    >
                <div class="card-body">
                    <h5 class="card-title">
                        @if ( $channel->podcast_title ) 
                        {{ $channel->podcast_title }}
                        @else
                        {{ $channel->channel_name }}
                        @endif
                    </h5>

                    @if ($channel->nbEpisodesGrabbedThisMonth > $channel->subscription->plan->nb_episodes_per_month)
                    <div class="alert alert-danger" role="alert">
                        {{  __('messages.danger_podcast_is_no_more_updated') }}
                    </div>
                    @endif
                    
                    <a href="{{ route('channel.show', $channel) }}">
                        <button type="button" class="btn btn-primary">
                            {{ __('messages.button_show_channel_label') }}
                        </button>
                    </a>
                    <a href="{{ route('channel.edit', $channel) }}">
                        <button type="button" class="btn btn-primary">
                            {{ __('messages.button_edit_channel_label') }}
                        </button>
                    </a>
                    <!--
                    <a href="{{ route('plans.index', $channel) }}">
                        <button type="button" class="btn btn-success">
                            {{ __('messages.button_upgrade_my_plan') }}
                        </button>
                    </a>
                    -->
                </div> <!-- /card body -->
            </div> <!-- /col card -->

        @endforeach

        </div> <!-- /row -->

    @else

        {{__('messages.no_channels_at_this_moment')}}

    @endif
</div>  <!--/channel container-->
