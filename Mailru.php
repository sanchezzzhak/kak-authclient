<?php namespace kak\authclient;

use yii\authclient\OAuth2;

/**
 * Mailru allows authentication via Mail.ru OAuth.
 *
 * In order to use Mail.ru OAuth you must register your application
 * at <http://api.mail.ru/sites/my/add>.
 *
 * Example configuration:
 *
 * ~~~
 * 'components' => [
 *     'authClientCollection' => [
 *         'class' => 'yii\authclient\Collection',
 *         'clients' => [
 *             'mailru' => [
 *                  'class' => kak\authclient\Mailru::class,
 *                  'clientId' => 'mailru_app_id',
 *                  'clientSecret' => 'mailru_app_secret_key',
 *             ],
 *         ],
 *     ]
 *     ...
 * ]
 * ~~~
 *
 * @see    http://api.mail.ru/sites/my/add
 * @see    http://api.mail.ru/docs/guides/oauth/sites/
 * @see    http://api.mail.ru/docs/reference/js/users.getInfo/
 *
 */
class Mailru extends OAuth2
{
    /**
     * @inheritdoc
     */
    public $authUrl = 'https://connect.mail.ru/oauth/authorize';
    /**
     * @inheritdoc
     */
    public $tokenUrl = 'https://connect.mail.ru/oauth/token';
    /**
     * @inheritdoc
     */
    public $apiBaseUrl = 'http://www.appsmail.ru/platform/api';

    /**
     * @inheritdoc
     */
    protected function initUserAttributes()
    {
        return $this->api('users.getInfo', 'GET');
    }

    /**
     * @param string $apiSubUrl
     * @param string $method
     * @param array $data
     * @param array $headers
     * @return array
     */
    public function api($apiSubUrl, $method = 'GET', $data = [], $headers = [])
    {
        $data['method'] = $apiSubUrl;
        return parent::api($this->apiBaseUrl, $method, $data, $headers);
    }


    /**
     * @param Request $request
     * @param $accessToken
     */
    public function applyAccessTokenToRequest($request, $accessToken)
    {
        $data = $request->getData();

        $data['format'] = 'json';
        $data['secure'] = 1;
        $data['app_id'] = $this->clientId;
        $data['session_key'] =  $accessToken->getToken();

        //sign up params - http://api.mail.ru/docs/guides/restapi/#server
        ksort($data);
        $str = '';
        foreach ($data as $key => $value) {
            $str .= "$key=$value";
        }
        $data['sig'] = md5($str . $this->clientSecret);

        $request->setData($data);
    }

    /**
     * @inheritdoc
     */
    protected function defaultName()
    {
        return 'mailru';
    }

    /**
     * @inheritdoc
     */
    protected function defaultTitle()
    {
        return 'Mail.ru';
    }

    /**
     * @inheritdoc
     */
    protected function defaultNormalizeUserAttributeMap()
    {
        return [
            'id' => [
                0,
                'uid'
            ],
        ];
    }
}