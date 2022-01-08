<?php

declare(strict_types=1);

namespace App\Exceptions;

class GoogleApiAuthFileIsMissingException extends PodmytubeException
{
    protected $message = <<<'EOT'
If you want to use Google spreadsheet with YAMM (as I do), you are requiring :
- a google service account file with appropriate permission to be placed in storage/keys folder
- to fill in the value in the .env file with following line
GOOGLE_SPREADSHEET_AUTH_FILE=spreadsheet-xxx-xxxxxx.json
EOT;
}
