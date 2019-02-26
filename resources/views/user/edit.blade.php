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
        <div class="form-row">
            <div class="form-group col-md-4">                
                <label for="inputName">{{ __('messages.name_label') }}</label>
                <input type="text" class="form-control" id="inputName" name="name" value="{{ $user->name }}" required>
            </div>
            <div class="form-group col-md-5">
                <label for="inputEmail">{{ __('messages.email_label') }}</label>
                <input type="text" class="form-control" id="inputEmail" name="email" value="{{ $user->email }}" required>
            </div>
            <div class="form-group col-md-3">
                <label for="selectLang">{{ __('messages.language_label') }}</label>
                <select name="language" id="selectLang" class="form-control">
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