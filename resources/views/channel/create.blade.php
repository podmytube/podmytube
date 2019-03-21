@extends('layouts.app')

@section('pageTitle', __('messages.page_title_channel_create'))

@section ('content')

{{ Breadcrumbs::render('channel.create') }}

<div class="container"><!--section container-->

	<h2> 
    
    	{{ __('messages.page_title_channel_create') }}

	</h2>

	<hr> 

    <div class="container w-60 mt-2 mb-2"> <!--form container-->

    	@include ('layouts.errors')

        <form method="POST" action="/channel">

            {{ csrf_field() }}

            <div class="form-group">
                <label for="channel_url">{{ __('messages.youtube_channel_url_label') }}</label>
                <a href="#" title="{{ __('messages.create_youtube_channel_url_help') }}">
                    <img src="/images/glyphicons-195-question-sign.png" class="float-right">
                </a>
                <input id="channel_url" type="text" class="form-control" name="channel_url" required>
            </div>

            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="owner" value="1" required>
                <div class="alert alert-warning">
                    {{ __('messages.channel_owner_warning_checkbox_label') }}
                </div>              
            </div>
        
            <div class="container">
                <div class="row pricing">
                    <div class="col border rounded m-1 p-3 "> <!-- free forever -->
                        <h3>{{ __('plans.pricing_li_free_plan') }}</h3>
                        <div class="alert alert-secondary text-center" role="alert">
                            {!! __('plans.pricing_li_free_plan_monthly_price') !!}
                        </div>
                        <ul>
                            <li>{!! __('plans.pricing_li_your_podcast_begin',['number'=>2]) !!}</li>
                            <li>{!! __('plans.pricing_li_N_new_epidodes_per_month',['number'=>2]) !!}</li>
                            <li>{!! __('plans.pricing_li_I_may_filter_videos_to_be_added_to_my_podcast') !!}</li>
                            <li>{!! __('plans.pricing_li_setup_once_and_forget') !!}</li>
                        </ul>
                        <div class="form-check text-center">
                            <input class="form-check-input" type="radio" name="chosenPlan" id="chosenPlan1" value="1" checked>
                            <label class="form-check-label" for="chosenPlan1">
                                {{ __('plans.pricing_li_I_choose_this_plan') }}
                            </label>
                        </div>
                    </div> <!-- /free forever -->
                    <div class="col border rounded m-1 p-3"> <!-- weekly -->
                        <h3>{{ __('plans.pricing_li_weekly_youtuber') }}</h3>
                        <div class="alert alert-success text-center" role="alert">
                            {!! __('plans.pricing_li_monthly_price',['pricePerMonth'=>9]) !!}
                        </div>
                        <ul>
                            <li>{!! __('plans.pricing_li_your_podcast_begin',['number'=>10]) !!}</li>
                            <li>{!! __('plans.pricing_li_N_new_epidodes_per_month',['number'=>10]) !!}</li>
                            <li>{!! __('plans.pricing_li_I_may_filter_videos_to_be_added_to_my_podcast') !!}</li>
                            <li>{!! __('plans.pricing_li_I_can_convert_N_of_my_playlist_into_a_podcast', ['playlistNumber'=>1]) !!}</li>
                            <li>{!! __('plans.pricing_li_setup_once_and_forget') !!}</li>
                        </ul>
                        <div class="form-check text-center">
                            <input class="form-check-input" type="radio" name="chosenPlan" id="chosenPlan2" value="2">
                            <label class="form-check-label" for="chosenPlan2">
                                {{ __('plans.pricing_li_I_deserve_this_plan') }}
                            </label>
                        </div>
                    </div> <!-- /weekly -->
                    <div class="col border rounded m-1 p-3"> <!-- daily -->
                        <h3>{{ __('plans.pricing_li_daily_youtuber') }}</h3>
                        <div class="alert alert-success text-center" role="alert">
                            {!! __('plans.pricing_li_monthly_price',['pricePerMonth'=>29]) !!}
                        </div>
                        
                        <ul>
                            <li>{!! __('plans.pricing_li_your_podcast_begin',['number'=>33]) !!}</li>
                            <li>{!! __('plans.pricing_li_N_new_epidodes_per_month',['number'=>33]) !!}</li>
                            <li>{!! __('plans.pricing_li_I_may_filter_videos_to_be_added_to_my_podcast') !!}</li>
                            <li>{!! __('plans.pricing_li_I_can_convert_N_of_my_playlist_into_a_podcast', ['playlistNumber'=>3]) !!}</li>
                            <li>{!! __('plans.pricing_li_setup_once_and_forget') !!}</li>
                        </ul>
                        <div class="form-check text-center">
                            <input class="form-check-input" type="radio" name="chosenPlan" id="chosenPlan3" value="3">
                            <label class="form-check-label" for="chosenPlan3">
                                {{ __('plans.pricing_li_I_prefer_this_one') }}
                            </label>
                        </div> <!-- /daily -->
                    </div>
                </div>
            </div> <!-- plans container -->

            <div class="form-group">
                <button type="submit" class="btn btn-primary">{{ __('messages.button_submit_label') }}</button>
            </div>
        </form>

    </div> <!--/form container-->

</div><!--/section container-->

@endsection
