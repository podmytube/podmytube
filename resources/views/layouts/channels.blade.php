<div class="row align-items-center">
    <div class="col-6">
        <h2> {{ __('messages.title_channel_index_label') }} </h2>
    </div> <!--/col-->
    <div class="col-3 text-center">
        <a href="{{ route('channel.create') }}">
            <button type="button" class="btn btn-primary">
                {{ __('messages.button_create_channel_label') }}
            </button>
        </a>
    </div> <!--/col-->
    <div class="col-3">
        <a href="mailto:contact@podmytube.com">
            <button type="button" class="btn btn-primary">
                {{ __('messages.button_need_assistance_label') }}
            </button>
        </a>
    </div> <!--/col-->
</div> <!--/subhead row-->

<div class="container"> <!--channel container-->

    @if ( !empty($channels) )

    <div class="row">
        @if ($channels->count() > 2)
            <div class="card-deck">
        @endif
        
        @foreach ($channels as $channel)
            @if ($channels->count() <=2)
                <div class="col-4">
            @elseif ($loop->index % 3 == 0)
                </div><div class="card-deck">
            @endif
            
            <!-- single channel card layout -->
            @include ('layouts.partials.singleChannelCard')

            @if ($channels->count() <=2)
                </div> <!-- ending col-4 -->
            @endif
        @endforeach

        @if ($channels->count() > 2)
            </div> <!-- ending card-deck -->
        @endif        
    </div> <!-- ending row -->
    
    @else
    {{__('messages.no_channels_at_this_moment')}}
    @endif
</div> <!--/channel container-->