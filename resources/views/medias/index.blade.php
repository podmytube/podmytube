@extends('layouts.app')

@section('pageTitle', __('medias.page_title_index') )

@section ('content')

@if ($medias->count()>0)
<div class="container mx-auto text-white">
    <h2 class="text-3xl md:text-5xl text-white font-semibold">Podcast episodes</h2>
    
    <table class="table-auto text-white">
        <thead">
            <tr>
                <th class="px-4 py-2 border-white">{{__('medias.index_table_col_media_title')}}</th>
                <th class="px-4 py-2">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($medias as $media)
            <tr>
                <th class="px-4 py-2">{{ $media->title }}</th>
                <td class="px-4 py-2">
                    @if (isset($media->grabbed_at))
                        {{ $media->grabbed_at->format(__('config.dateFormat')) }}
                    @else
                        <span class="badge badge-pill badge-danger">
                            {{__('medias.index_episode_not_included_badge')}}
                        </span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mx-auto" style="width: 300px;">
        {{ $medias->links() }}
    </div>
</div>
@else
<div class="alert alert-dark" role="alert">
    {!! __('medias.index_channel_has_no_known_episode') !!}
</div>
@endif
@endsection