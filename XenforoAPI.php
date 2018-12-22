<?php

namespace common\libs;


use Curl\Curl;

class XenforoAPI
{
    public $errors;
    private $forum_url;
    private $api_key;
    private $api_user_id;

    /**
     * XenforoAPI constructor.
     * @param $forum_url
     * @param $api_key
     * @param $api_user_id
     */
    public function __construct($forum_url, $api_key, $api_user_id)
    {
        $this->forum_url = $forum_url;
        $this->api_key = $api_key;
        $this->api_user_id = $api_user_id;
    }

    public function list_users()
    {
        return $this->executeRequest('GET', 'users');
    }

    private function executeRequest($method = 'GET', $endpoint, $payload = [])
    {
        $curl = new Curl();
        $curl->setHeaders($this->getHeaders());
        switch ($method) {
            case 'GET':
                $curl->get($this->forum_url . '/api/' . $endpoint);
                break;
            case 'POST':
                $curl->post($this->forum_url . '/api/' . $endpoint, $payload);
                break;
        }
        if ($curl->httpStatusCode !== 200) {
            $this->errors = $curl->response;
            return false;
        }
        return $curl->response;
    }

    private function getHeaders()
    {
        return [
            'XF-Api-Key' => $this->api_key,
            'XF-Api-User' => $this->api_user_id,
        ];
    }

    public function add_user($username, $password, $email, $require_confirmation = false)
    {
        return $this->executeRequest('POST', 'users', [
            'username' => $username,
            'password' => $password,
            'email' => $email,
            'user_state' => $require_confirmation ? 'email_confirm' : 'valid'
        ]);
    }

    public function update_user($user_id, $payload)
    {
        return $this->executeRequest('POST', 'users/' . $user_id, $payload);
    }

}