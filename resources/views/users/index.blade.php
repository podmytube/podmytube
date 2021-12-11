@extends('layouts.app')

@section('pageTitle', 'Podmytube list of users')

@section('content')
    <div class="max-w-screen-xl mx-auto py-12 px-4">

        <h2 class="text-3xl md:text-5xl text-white font-semibold">Users</h2>

        @if ($users->count() > 0)
            <div class="bg-gray-100 mt-6 px-4 py-4 rounded-lg max-w-screen-lg md:mx-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-900 text-white">
                            <th class="p-3 border-white">User</th>
                            <th class="p-3">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td class="px-2 py-2">
                                    {{ $user->name }}
                                </td>

                                <td class="text-center">
                                    @if ($user->id() != auth()->id())
                                        <a href="{{ route('users.impersonate', $user->id()) }}"
                                            class="btn btn-delete">Impersonate</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4 px-6">
                    {{ $users->appends(['nb' => $nbItemsPerPage])->links() }}
                </div>

            </div>
        @else
            <div class="bg-gray-100 border rounded-lg border-gray-500 text-gray-900 px-4 py-3" role="alert">
                <p class="font-bold">No users yet.</p>
                <p class="text-base">This is absolutely NOT normal !</p>
            </div>
        @endif
    </div>
@endsection
