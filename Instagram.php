<?php namespace kak\authclient;

use yii\authclient\OAuth2;
use yii\httpclient\Request;

/**
 * Class Instagram
 * @package kak\authclient
 *
 *
 */
class Instagram extends OAuth2
{

    public $authUrl = 'https://api.instagram.com/oauth/authorize';

    public $tokenUrl = 'https://api.instagram.com/oauth/access_token';


    public $apiBaseUrl = 'https://api.instagram.com/v1';

    /**
     * @return array
     */
    protected function initUserAttributes()
    {
        $response = $this->api('users/self', 'GET');
        return $response['data'];
    }



    /**
     * @param Request $request
     * @param $accessToken
     */
    public function applyAccessTokenToRequest($request, $accessToken)
    {
        $data = $request->getData();

        $data['access_token'] =  $accessToken->getToken();

        $request->setData($data);
    }

    /**
     * @return string
     */
    protected function defaultName()
    {
        return 'instagram';
    }

    /**
     * @return string
     */
    protected function defaultTitle()
    {
        return 'Instagram';
    }
}