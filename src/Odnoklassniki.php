<?php namespace kak\authclient;

use yii\authclient\OAuth2;
use yii\httpclient\Request;

/**
 * Class Odnoklassniki
 * @package kak\authclient
 *
 * Example configuration:
 * ```php
 * 'components' => [
 *     'authClientCollection' => [
 *         'class' => 'yii\authclient\Collection',
 *         'clients' => [
 *             'odnoklassniki' => [
 *                 'class' => kak\authclient\Odnoklassniki::class,
 *                 'clientId' => 'app_client_id',
 *                 'clientSecret' => 'application_client_secret',
 *                 'application_public_key' => 'application_public_key',
 *                 'scope' => 'VALUABLE_ACCESS'
 *             ],
 *         ],
 *     ]
 * ]
 * ```
 */
class Odnoklassniki extends OAuth2
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'http://www.odnoklassniki.ru/oauth/authorize';
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'http://api.odnoklassniki.ru/oauth/token.do';
    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'http://api.odnoklassniki.ru/fb.do';

    public $application_public_key;

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return $this->api('', 'GET', [
            'method' => 'users.getCurrentUser',
            'format' => 'JSON',
            'client_id' => $this->clientId,
        ]);
    }


    /**
     * @param Request $request
     * @param $accessToken
     */
    public function applyAccessTokenToRequest($request, $accessToken)
    {
        $data = $request->getData();
        $data['application_key'] = $this->application_public_key;
        $data['sig'] = $this->generateSignature($data);
        $data['access_token'] = $accessToken->getToken();
        $request->setData($data);
    }

    protected function generateSignature(array $params)
    {
        ksort($params);
        $query = '';
        foreach ($params as $key => $param) {
            $query .= sprintf('%s=%s', $key, $param);
        }
        return md5($query . md5($this->accessToken->getToken() . $this->clientSecret));
    }


    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'odnoklassniki';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'Odnoklassniki';
    }

    /**
     * @inheritdoc
     */
    protected function defaultNormalizeUserAttributeMap()
    {
        return [
            'id' => 'uid'
        ];
    }

} 