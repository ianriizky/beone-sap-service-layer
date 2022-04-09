<?php

namespace Ianriizky\BeoneSAPServiceLayer\Services\Concerns;

use GuzzleHttp\Psr7\Uri;
use Ianriizky\BeoneSAPServiceLayer\Exception\MalformedBaseUrlExeption;

trait ParseUrl
{
    /**
     * Parse the given URL into a full URL format.
     *
     * @param  string  $url
     * @return string
     *
     * @throws \Ianriizky\BeoneSAPServiceLayer\Exception\MalformedBaseUrlExeption
     */
    protected static function parseBaseUrl(string $url): string
    {
        $url = new Uri($url);

        if ($url->getScheme() === '') {
            throw new MalformedBaseUrlExeption('The scheme (http:// or https://) of base url must exists.');
        }

        return (string) $url;
    }
}
