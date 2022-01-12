<?php

declare(strict_types=1);

namespace App\Exceptions;

class NoPayingChannelException extends PodmytubeException
{
    protected $message = "There is no active paying channel. Hoping it's only a test case.";
}
