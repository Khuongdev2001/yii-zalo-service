<?php

namespace app\controllers;

use yii\authclient\AuthAction;
use yii\authclient\ClientInterface;
use yii\rest\Controller;

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
}
