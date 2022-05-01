<?php

/**
 * Determine that the given talenta request header is valid.
 *
 * @param  \Illuminate\Http\Client\Request  $request
 * @return bool
 */
function phpunit_validate_request_header(Illuminate\Http\Client\Request $request): bool
{
    return
        $request->hasHeader('Authorization') &&
        $request->hasHeader('Date') &&
        Illuminate\Support\Carbon::hasFormat($request->header('Date')[0], Carbon\CarbonInterface::RFC7231_FORMAT);
}

/**
 * Create a new response instance for use during stubbing from the given json responses path.
 *
 * @param  string  $jsonPath
 * @param  int  $status
 * @param  array  $headers
 * @return \GuzzleHttp\Promise\PromiseInterface
 */
function phpunit_create_http_fake_response(string $jsonPath, $status = 200, $headers = [])
{
    $body = json_decode(file_get_contents(__DIR__.'../../responses/'.$jsonPath), true);

    return \Ianriizky\BeoneSAPServiceLayer\Support\Facades\Http::response($body, $status, $headers);
}

/**
 * Assert that the generated JSON encoded object and the content of the given json responses path are equal.
 *
 * @param  string  $jsonPath
 * @param  Illuminate\Http\Client\Response  $response
 * @return void
 */
function phpunit_assert_same_json_response(string $jsonPath, Illuminate\Http\Client\Response $response)
{
    Illuminate\Testing\Assert::assertJsonStringEqualsJsonString(
        file_get_contents(__DIR__.'../../responses/'.$jsonPath),
        $response->body()
    );
}
