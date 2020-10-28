<?php

namespace App\Http\Controllers;

use App\Channel;
use Illuminate\Support\Facades\Session;

class MediasController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel)
    {
        try {
            $medias = $channel
                ->medias()
                ->orderBy('published_at', 'desc')
                ->simplePaginate(10);
        } catch (\Exception $exception) {
            Session::flash('message', $exception->getMessage());
            Session::flash('alert-class', 'alert-danger');
        }
        return view('medias.index', compact('channel', 'medias'));
    }
}
