<?php

namespace app\components\authclient\clients;

use Yii;
use yii\authclient\OAuth2;
use yii\authclient\OAuthToken;

class ZaloService extends OAuth2
{
    public $returnUrl;
    public $tokenUrl = 'https://oauth.zaloapp.com/v4/oa/access_token';
    public $apiBaseUrl = 'https://oauth.zaloapp.com/v4/oa';
    public $authUrl = 'https://oauth.zaloapp.com/v4/oa/permission';
    public $tableName;
    public $secretKey;
    public $appId;

    /**
     * @var mixed
     */

    protected function initUserAttributes()
    {
        // TODO: Implement initUserAttributes() method.
    }

    public function fetchAccessToken($authCode, array $params = [])
    {
        $accessToken = parent::fetchAccessToken($authCode, [
            "app_id" => $this->appId,
            "code_verifier" => $this->getState("code_verifier")
        ]);
        $this->pushAccessTokenDb($accessToken);
        return $accessToken;
    }

    public function pushAccessTokenDb(OAuthToken $accessToken)
    {
        $sql = "INSERT INTO `service_oauth` ([[auth_client]], [[access_token]],[[refresh_token]], [[expires_in]]) VALUES (:auth_client, :access_token, :refresh_token,:expires_in)";
        $command = Yii::$app->db->createCommand($sql);
        $excute = $command->bindValues([
            ':auth_client' => $this->getName(),
            ':access_token' => $accessToken->getToken(),
            ':refresh_token' => $accessToken->getParam("refresh_token"),
            ':expires_in' => $accessToken->getParam("expires_in")
        ])->execute();
    }

    protected function applyClientCredentialsToRequest($request)
    {
        $request->addHeaders([
            'secret_key' => $this->secretKey,
        ]);
    }

    public function buildAuthUrl(array $params = [])
    {
        $this->setReturnUrl($this->returnUrl);
        $defaultParams = [
            'app_id' => $this->appId,
            'redirect_uri' => $this->getReturnUrl(),
        ];
        if ($this->enablePkce) {
            $codeVerifier = bin2hex(Yii::$app->security->generateRandomKey(64));
            $this->setState('code_verifier', $codeVerifier);
            $defaultParams['code_challenge'] = trim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
            $defaultParams['code_challenge_method'] = 'S256';
        }

        return $this->composeUrl($this->authUrl, array_merge($defaultParams, $params));
    }
}