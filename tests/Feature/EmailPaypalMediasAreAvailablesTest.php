<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * This class is testing if medias used by paypal shop
 * or email signature are availables on /medias.
 * I failed to test file is really accessible from uri
 * because the local domain is unknown in the container.
 */
class EmailPaypalMediasAreAvailablesTest extends TestCase
{
    protected $emailAndPaypalMedias = [
        'entete-paypal.png',
        'logo-gmail.png',
        'logo-paypal.jpg',
        'logo-paypal.png',
    ];

    public function testPodmytubeMediasAreAvailables()
    {
        foreach ($this->emailAndPaypalMedias as $mediaToCheck) {
            $fileToCheck = public_path('/medias/' . $mediaToCheck);
            $this->assertTrue(file_exists($fileToCheck));
        }
    }
}
