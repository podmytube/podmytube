@extends('layouts.app')

@section('pageTitle', __('messages.page_title_user_edit') )

@section ('content')

{{ Breadcrumbs::render('user.edit', $user) }}

<div class="container"><!--section container-->

	<h2> {{ __('messages.page_title_user_edit') }} </h2>

	<hr> 

    <form method="POST" action="{{ route('user.patch') }}">

        {{ method_field('PATCH') }}

        {{ csrf_field() }}
        <div class="row">
            <div class="col-3">
                {{ __('messages.name_label') }}
            </div>
            <div class="col">
                <input type="text" class="form-control" name="name" value="{{ $user->name }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                {{ __('messages.email_label') }}
            </div>
            <div class="col">
                <input type="text" class="form-control" name="email" value="{{ $user->email }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                {{ __('messages.language_label') }}
            </div>
            <div class="col">
                <select name="language">
                    <option value="fr" {{ $user->language == 'fr' ? ' selected' : '' }}>FR</option>
					<option value="en" {{ $user->language == 'en' ? ' selected' : '' }}>EN</option>
                </select>
            </div>
        </div>

        <div class="mx-auto" style="width:200px">
            <button type="submit" class="btn btn-primary">{{ __('messages.button_update_label') }}</button>
            <a href="{{ route('user.show') }}" class="btn btn-secondary">
                {{ __('messages.button_cancel_label') }}
            </a>
		</div>

    </form>
</div> <!-- /end container -->

@endsection