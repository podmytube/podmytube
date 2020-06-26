@extends('layouts.app')

@section('pageTitle', __('messages.page_title_user_edit') )

@section ('content')

{{ Breadcrumbs::render('user.edit', $user) }}

<div class="container">
    <!--section container-->

    <h2> {{ __('messages.page_title_user_edit') }} </h2>

    <hr>

    <form method="POST" action="{{ route('user.update', $user) }}">

        {{ method_field('PATCH') }}

        {{ csrf_field() }}
        <div class="form-group">
            <label for="inputName">{{ __('account.name') }}</label>
            <input type="text" class="form-control" id="inputName" name="name" value="{{ $user->name }}" required>
        </div>
        <div class="form-group">
            <label for="inputEmail">{{ __('account.email') }}</label>
            <input type="text" class="form-control" id="inputEmail" name="email" value="{{ $user->email }}" required>
        </div>
        <div class="form-group">
            <label for="selectLang">{{ __('account.language') }}</label>
            <select name="language" id="selectLang" class="form-control">
                <option value="fr" {{ $user->language == 'fr' ? ' selected' : '' }}>FR</option>
                <option value="en" {{ $user->language == 'en' ? ' selected' : '' }}>EN</option>
            </select>
        </div>
        <div class="form-group form-check">
            <input type="checkbox" class="form-check-input" name="newsletter" id="newsletter" value="1" @if($user->newsletter) checked @endif>
            <label class="form-check-label" for="newsletter">Newsletter</label>
        </div>

        <div class="mx-auto" style="width:200px">
            <button type="submit" class="btn btn-success">{{ __('messages.button_update_label') }}</button>
            <a href="{{ route('user.show', $user) }}" class="btn btn-secondary">
                {{ __('messages.button_cancel_label') }}
            </a>
        </div>

    </form>
</div> <!-- /end container -->

@endsection