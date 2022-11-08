<?php

namespace app\components\zalo;

use app\components\zalo\services\ZaloNotificationService;
use app\components\zalo\services\ZaloOaService;

/**
 * @property string $accessToken
 * @property ZaloNotificationService $zns
 * @property ZaloOaService $oa
 */
class ZaloComponent extends ZaloBaseComponent
{
    public function init()
    {
        parent::init();
    }

    public function configServices()
    {
        return array_merge([
            "zns" => ZaloNotificationService::className(),
            "oa" => ZaloOaService::className()
        ], parent::configServices());
    }
}