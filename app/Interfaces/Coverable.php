<?php

namespace App\Interfaces;

interface Coverable
{
    public function id();

    public function title(): string;

    public function youtubeId(): string;

    public function channelId(): string;
}
