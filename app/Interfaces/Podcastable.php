<?php

namespace App\Interfaces;

use App\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

interface Podcastable
{
    public function relativeFeedPath():string;

    public function remoteFilePath():string;

    public function podcastTitle():string;

    public function podcastLink():?string;

    public function podcastDescription():?string;

    public function podcastAuthors():?string;

    public function podcastEmail():?string;

    public function podcastCopyright():?string;

    public function podcastLanguage():?string;

    public function podcastCategory():?Category;

    public function podcastExplicit():?string;

    public function mediasToPublish():Collection;

    public function podcastItems():SupportCollection;

    public function podcastCoverUrl():string;

    public function podcastUrl():string;

    public function podcastHeader():array;

    public function toPodcast():array;
}
