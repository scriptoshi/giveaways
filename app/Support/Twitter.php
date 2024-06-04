<?php

namespace App\Support;

use Ixudra\Curl\Facades\Curl;
use App\Models\Connection;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Client;

class Twitter
{


    private $baseUrl = "https://api.twitter.com/2/";
    public function __construct(public Connection $connection)
    {
    }



    function me()
    {

        $response = Curl::to($this->baseUrl . 'users/me')
            ->withBearer($this->getToken())
            ->withData([
                'user.fields' => 'public_metrics,description,verified'
            ])
            ->asJson()
            ->get();
        if (isset($response->errors)) throw new \Exception($response->detail);
        $user = $response->data;
        $connection = $this->connection;
        $connection->description = $user->description;
        $connection->verified = $user->verified;
        //$connection->name = $user->name;
        $connection->followers = $user->public_metrics->followers_count;
        $connection->following = $user->public_metrics->following_count;
        $connection->tweets = $user->public_metrics->tweet_count;
        $connection->save();
    }

    function followed($projectUsername)
    {
        $response = Curl::to($this->baseUrl . 'users/by/username/' . $projectUsername)
            ->withBearer($this->getToken())
            ->withData([
                'user.fields' => 'connection_status'
            ])
            ->asJson()
            ->get();
        return $response->data[0]?->connection_status?->following ?? false;
    }


    public function getToken(): string
    {
        $url = "https://api.twitter.com/2/oauth2/token";
        $con = $this->connection;
        if (now()->lt($con->expires_at)) return $con->token;
        $clientId = config('services.twitter.client_id');
        $clientSecrect = config('services.twitter.client_secret');
        $auth = base64_encode("$clientId:$clientSecrect");
        $response = Curl::to($url)
            ->withHeader("Authorization: Basic $auth")
            ->asJsonResponse()
            ->withData([
                //'client_id' => config('services.twitter.client_id'),
                'refresh_token' => $con->refreshToken,
                'grant_type' => 'refresh_token',
            ])->withContentType('application/x-www-form-urlencoded')
            ->post();
        $con->token = $response->access_token;
        $con->expiresIn = (int) $response->expires_in;
        $con->expires_at = now()->addSeconds($response->expires_in);
        $con->refreshToken = $response->refresh_token;
        $con->save();
        return $con->token;
    }
}
