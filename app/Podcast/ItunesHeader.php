<?php

namespace App\Podcast;

class ItunesHeader implements
{
    protected $itunesAuthor;

    protected $itunesEmail;
    protected $itunesSummary;
    protected $itunesExplicit;
    protected $itunesImage;

    private function __construct()
    {
        //code 
    }

    public static function create(...$params)
    {
        return new static(...$params);
    }

    public function addOwner(ItunesOwner $itunesOwner)
    {
        $this->itunesOwner = $itunesOwner;
    }
}
