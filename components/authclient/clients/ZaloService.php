<?php

namespace app\components\authclient\clients;

use Yii;
use yii\authclient\OAuth2;
use yii\authclient\OAuthToken;
use yii\db\Exception;
use yii\web\HttpException;

class ZaloService extends OAuth2
{
    public $returnUrl;
    public $tokenUrl = 'https://oauth.zaloapp.com/v4/oa/access_token';
    public $apiBaseUrl = 'https://oauth.zaloapp.com/v4/oa';
    public $authUrl = 'https://oauth.zaloapp.com/v4/oa/permission';
    public $tableAccessToken;
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

    /**
     * @throws HttpException
     */
    public function fetchAccessToken($authCode, array $params = [])
    {
        return parent::fetchAccessToken($authCode, [
            "app_id" => $this->appId,
            "code_verifier" => $this->getState("code_verifier")
        ]);
    }

    public function refreshAccessToken(OAuthToken $token)
    {
        return parent::refreshAccessToken($token);
    }

    public function pushAccessTokenDb(OAuthToken $response)
    {
        $sql = "INSERT INTO `$this->tableAccessToken` ([[auth_client]], [[access_token]],[[refresh_token]], [[token_expires_in]],[[refresh_token_expires_in]]) VALUES (:auth_client, :access_token, :refresh_token,:token_expires_in,:refresh_token_expires_in)";
        $command = Yii::$app->db->createCommand($sql);
        $excute = $command->bindValues([
            ':auth_client' => $this->getId(),
            ':access_token' => $response->getToken(),
            ':refresh_token' => $response->getParam("refresh_token"),
            ':token_expires_in' => time() + $response->getParam("expires_in"),
            ':refresh_token_expires_in' => strtotime("+3 months")])->execute();
    }

    /**
     * @throws Exception
     */
    public function revokeAccessTokenDb()
    {
        Yii::$app
            ->db
            ->createCommand()
            ->delete($this->tableAccessToken, ['auth_client' => $this->getId()])
            ->execute();
    }

    protected function applyClientCredentialsToRequest($request)
    {
        $request->addHeaders([
            'secret_key' => $this->secretKey,
        ]);
    }

    public function buildOathToken($params)
    {
        $oathToken = new OAuthToken();
        $oathToken->setParams($params);
        return $oathToken;
    }

    public function buildAuthUrl(array $params = [])
    {
        $this->setReturnUrl($this->returnUrl);
        $defaultParams = [
            'app_id' => $this->appId,
            'redirect_uri' => $this->getReturnUrl(),
        ];
        $codeVerifier = bin2hex(Yii::$app->security->generateRandomKey(64));
        $this->setState('code_verifier', $codeVerifier);
        $defaultParams['code_challenge'] = trim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
        $defaultParams['code_challenge_method'] = 'S256';
        return $this->composeUrl($this->authUrl, array_merge($defaultParams, $params));
    }
}