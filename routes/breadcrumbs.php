<?php

// Welcome
/* Breadcrumbs::register('home', function ($breadcrumbs) {
    $breadcrumbs->push(__('messages.page_title_home_breadcrumb'), route('home'));
}); */

// Podcasts
Breadcrumbs::register('home', function ($breadcrumbs) {
    $breadcrumbs->push(
        __('messages.page_title_podcasts_breadcrumb'),
        route('home')
    );
});

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//                                 Podcasts
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// Home > Channels >
Breadcrumbs::register('channel.index', function ($breadcrumbs) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(
        __('messages.page_title_channel_index'),
        route('channel.index')
    );
});

// Home > Channel > Create
Breadcrumbs::register('channel.create', function ($breadcrumbs) {
    $breadcrumbs->parent('channel.index');
    $breadcrumbs->push(
        __('messages.page_title_channel_create'),
        route('channel.create')
    );
});

// Home > Channel > XXXXXXX
Breadcrumbs::register('channel.show', function ($breadcrumbs, $channel) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push($channel->channel_name, route('channel.show', $channel));
});

// Home > Channel > [Channel name] > edit
Breadcrumbs::register('channel.edit', function ($breadcrumbs, $channel) {
    $breadcrumbs->parent('channel.show', $channel);
    $breadcrumbs->push(
        __('messages.page_title_channel_edit'),
        route('channel.edit', $channel)
    );
});

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//                                 MEDIAS
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// Home > Medias
Breadcrumbs::register('medias.index', function ($breadcrumbs, $channel) {
    $breadcrumbs->parent('channel.show', $channel);
    $breadcrumbs->push('medias', route('channel.medias.index', $channel));
});

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//                                 USER
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// Home > User
Breadcrumbs::register('user.show', function ($breadcrumbs, $user) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(
        __('messages.page_title_user_show'),
        route('user.show', $user)
    );
});

// Home > User >
Breadcrumbs::register('user.edit', function ($breadcrumbs, $user) {
    $breadcrumbs->parent('home');
    $breadcrumbs->push(
        __('messages.page_title_user_edit'),
        route('user.edit', $user)
    );
});

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
//                                 THUMBS
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

// Home > channel > XXXXXXX > thumbs
Breadcrumbs::register('thumbs.index', function ($breadcrumbs, $channel) {
    $breadcrumbs->parent('channel.show', $channel);
    $breadcrumbs->push(
        __('messages.page_title_channel_thumbs_index'),
        route('channel.thumbs.index', $channel->channel_id)
    );
});

// Home > channel > XXXXXXX > thumbs > edit
Breadcrumbs::register('thumbs.edit', function ($breadcrumbs, $channel) {
    $breadcrumbs->parent('channel.show', $channel);
    $breadcrumbs->push(
        __('messages.page_title_channel_thumbs_edit'),
        route('channel.thumbs.edit', $channel->channel_id)
    );
});
