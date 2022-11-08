<?php

namespace app\components\zalo\services;

use yii\base\BaseObject;
use yii\httpclient\Client;
use yii\httpclient\Exception;
use yii\httpclient\RequestEvent;
use yii\httpclient\Request;

class ZaloOaService extends BaseObject
{
    /**
     * @var Client $httpclient ;
     */
    public $httpclient;
    public $accessToken;
    protected $messageText;
    protected $recipientId;
    protected $apiMessage = "https://openapi.zalo.me/v2.0/oa/message";

    /**
     * @throws Exception
     */
    public function sendMessage()
    {
        $request = $this->httpclient->post($this->apiMessage, json_encode([
            "recipient" => [
                "user_id" => $this->recipientId
            ],
            "message" => [
                "text" => $this->messageText
            ]
        ]));
        $request->on(Request::EVENT_BEFORE_SEND, [$this, "beforeRequest"]);
        $response = $request->send();
        return $response->isOk;
    }

    public function setParams($attributes)
    {
        foreach ($attributes as $attribute => $value) {
            if ($this->canSetProperty($attribute)) {
                $this->$attribute = $value;
            }
        }
    }

    public function getParam($attribute)
    {
        return $this->$attribute;
    }

    /**
     * @param RequestEvent $event
     */
    public function beforeRequest(RequestEvent $event)
    {
        $event->request->addHeaders([
            "access_token" => $this->accessToken,
            "Content-Type" => "application/json"
        ]);
    }
}