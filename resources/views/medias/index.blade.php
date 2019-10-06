@extends('layouts.app')

@section('pageTitle', __('medias.page_title_index') )

@section ('content')

{{ Breadcrumbs::render('medias.index', $channel) }}

@if ($medias->count()>0)
<table class="table ">
    <thead class="thead-dark">
        <tr>
            <th scope="col">{{__('medias.index_table_col_media_title')}}</th>
            <th scope="col">{{__('medias.index_table_col_media_published_at')}}</th>
            <th scope="col">{{__('medias.index_table_col_media_grabbed_at')}}</th>
        </tr>
    </thead>
    <tbody>

        @foreach ($medias as $media)
        <tr>
            <th scope="row">{{ $media->title }}</th>
            <td>{{ $media->published_at->format(__('config.dateFormat')) }}</td>
            <td>
                @if (isset($media->grabbed_at))
                {{ $media->grabbed_at->format(__('config.dateFormat')) }}
                @else
                <span class="badge badge-pill badge-danger">{{__('medias.index_episode_not_included_badge')}}</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="mx-auto" style="width: 300px;">
          {{ $medias->links() }}
</div>
@else
<div class="alert alert-dark" role="alert">
    {!! __('medias.index_channel_has_no_known_episode') !!}
</div>
@endif
@endsection