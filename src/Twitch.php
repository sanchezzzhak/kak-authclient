<?php namespace kak\authclient;

use yii\httpclient\Request;
use yii\authclient\OAuth2;

/**
 * Class Twitch
 * @package kak\authclient
 */
class Twitch extends OAuth2
{
    public $scope = 'user_read';

    public $authUrl = 'https://api.instagram.com/kraken/oauth2/authorize';

    public $tokenUrl = 'https://api.instagram.com/kraken/oauth2/token';

    public $apiBaseUrl = 'https://api.instagram.com';

    /**
     * @return array
     */
    protected function initUserAttributes()
    {
        $response = $this->api('kraken/user', 'GET');
        return $response['data'];
    }


    /**
     * @param Request $request
     * @param $accessToken
     */
    public function applyAccessTokenToRequest($request, $accessToken)
    {
        $data = $request->getData();
        $request->addHeaders([
            'Client-ID' => $this->clientId,
            'Accept' => 'application/vnd.twitchtv.v5+json'
        ]);
        $data['oauth_token'] = $accessToken->getToken();
        $request->setData($data);
    }

}