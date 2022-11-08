<?php

namespace app\controllers;

use Yii;
use yii\authclient\AuthAction;
use yii\base\InvalidConfigException;
use yii\httpclient\Exception;
use yii\rest\Controller;
use app\components\authclient\clients\ZaloService;
use app\components\zalo\services\ZaloNotificationService;
use app\components\zalo\services\ZaloOaService;
use app\components\zalo\ZaloComponent;

class ZaloController extends Controller
{
    public function actions()
    {
        return [
            'request-permission' => [
                'class' => AuthAction::className(),
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function onAuthSuccess(ZaloService $client)
    {
        $client->revokeAccessTokenDb();
        $client->pushAccessTokenDb($client->getAccessToken());
    }

    /**
     * @throws InvalidConfigException
     */
    public function actionSendTemplate()
    {
        /**
         * @var ZaloComponent $zaloComponent
         * @var ZaloNotificationService $znsService
         *
         */
        $zaloComponent = Yii::$app->zalo;
        $znsService = $zaloComponent->getService("zns");
        return $znsService->getTemplateType();
    }

    /**
     * @throws Exception
     * @throws InvalidConfigException
     */
    public function actionWebhook()
    {
        $request = Yii::$app->request->post();
        if (!empty($request["message"])) {
            /*  @var ZaloComponent $zaloComponent*/
            /* @var ZaloOaService $oaService*/
            $zaloComponent = Yii::$app->zalo;
            $oaService = $zaloComponent->getService("oa");
            $oaService->setParams([
                "messageText" => $request["message"]["text"],
                "recipientId" => $request["sender"]["id"]
            ]);
            return $oaService->sendMessage();
        }
        return false;
    }
}
