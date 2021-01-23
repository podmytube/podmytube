<?php

namespace App\Interfaces;

use App\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface Podcastable
{
    public function title():string;

    public function link():?string;

    public function description():?string;

    public function authors():?string;

    public function email():?string;

    public function copyright():?string;

    public function languageCode():?string;

    public function category():?Category;

    public function explicit():?bool;

    public function mediasToPublish():Collection;

    public function podcastItems():SupportCollection;

    public function podcastCoverUrl():string;

    public function podcastHeader():array;

    public function toPodcast():array;
}
