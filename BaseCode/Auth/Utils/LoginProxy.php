<?php

namespace BaseCode\Auth\Utils;

// Adapted from:
// http://esbenp.github.io/2017/03/19/modern-rest-api-laravel-part-4/

use BaseCode\Auth\Repositories\Users;
use Illuminate\Foundation\Application;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Client;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;

class LoginProxy
{
    const REFRESH_TOKEN = 'refresh_token';

    private $auth;

    private $cookie;

    private $request;

    private $userRepository;

    public function __construct(Application $app, Users $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->auth = $app->make('auth');
        $this->cookie = $app->make('cookie');
        $this->request = $app->make('request');
    }

    /**
     * Attempt to create an access token using user credentials
     *
     * @param string $username
     * @param string $password
     */
    public function attemptLogin($username, $password)
    {
        $user = $this->userRepository->getUser($username);
        
        if (!is_null($user)) {
            return $this->proxy('password', [
                'username' => $username,
                'password' => $password
            ]);
        }
        throw new \Exception("Invalid Login");
    }

    /**
     * Attempt to refresh the access token used a refresh token that
     * has been saved in a cookie
     */
    public function attemptRefresh()
    {
        $refreshToken = $this->request->cookie(self::REFRESH_TOKEN);
        if ($refreshToken === null) {
            throw new AuthenticationException('Token expired');
        }
        return $this->proxy('refresh_token', [
            'refresh_token' => $refreshToken
        ]);
    }

    /**
     * Proxy a request to the OAuth server.
     *
     * @param string $grantType what type of grant type should be proxied
     * @param array $data the data to send to the server
     */
    public function proxy($grantType, array $data = [])
    {
        $data = array_merge($data, [
            'client_id' => env('PASSWORD_CLIENT_ID'),
            'client_secret' => env('PASSWORD_CLIENT_SECRET'),
            'grant_type' => $grantType,
            'scope' => '*',
        ]);

        $client = new Client;

        try {
            $response = $client->post(url('oauth/token'), [
                'form_params' => $data
            ]);
        } catch (TransferException $e) {
            throw new AuthorizationException(json_decode($e->getResponse()->getBody())->message);
        }

        $data = json_decode($response->getBody());

        // Create a refresh token cookie
        $this->cookie->queue(
            self::REFRESH_TOKEN,
            $data->refresh_token,
            14400,  // 10 days
            null,   // path
            null,   // domain
            false,  // secure
            true // HttpOnly
        );

        return [
            'access_token' => $data->access_token,
            'expires_in' => $data->expires_in
        ];
    }

    /**
     * Logs out the user. We revoke access token and refresh token.
     * Also instruct the client to forget the refresh cookie.
     */
    public function logout()
    {
        $this->auth->user()->tokens()->delete();

        // $accessToken = $this->auth->user()->token();

        // $refreshToken = $this->db
        //     ->table('oauth_refresh_tokens')
        //     ->where('access_token_id', $accessToken->id)
        //     ->update([
        //         'revoked' => true
        //     ]);

        // $accessToken->revoke();

        $this->cookie->queue($this->cookie->forget(self::REFRESH_TOKEN));
        $this->cookie->queue($this->cookie->forget('Bearer'));
    }
}
