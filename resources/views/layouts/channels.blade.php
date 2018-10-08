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
                
        <ul>

        @foreach ($channels as $channel)

            <li>
            
                <a href="/channel/<?= $channel->channel_id?>"><?= $channel->channel_name ?></a> 
                (
                    @if ($channel->channel_premium == 0)
                    free
                    @elseif ($channel->channel_premium == 1)
                    early
                    @elseif ($channel->channel_premium == 2)
                    premium
                    @elseif ($channel->channel_premium == 3)
                    vip
                    @endif
                )
                
            </li>

        @endforeach

        </ul>

    @else

        {{__('messages.no_channels_at_this_moment')}}

    @endif
</div>  <!--/channel container-->
