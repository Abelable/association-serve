<?php

namespace api\modules\v1\forms;

use common\enums\StatusEnum;
use common\models\member\Auth;
use common\models\member\Member;
use Yii;
use yii\base\Model;

/**
 * Class MiniProgramLoginForm
 * @package api\modules\v1\models
 */
class MiniProgramLoginForm extends Model
{
    public $iv;
    public $rawData;
    public $encryptedData;
    public $signature;
    public $code;

    public $auth;

    /**
     * @var
     */
    protected $openid;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['iv', 'rawData', 'encryptedData', 'signature', 'code'], 'required'],
            [['signature'], 'authVerify'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'iv' => '加密算法的初始向量',
            'rawData' => '不包括敏感信息的原始数据字符串，用于计算签名',
            'encryptedData' => '包括敏感数据在内的完整用户信息的加密数据',
            'signature' => '签名',
            'code' => 'code码',
            'auth' => '授权秘钥',
        ];
    }

    public function scenarios(){
        return [
            'login' => ['iv','encryptedData', 'code','group'],
            'bind' => ['iv', 'rawData', 'encryptedData', 'signature', 'code', 'mobile', 'group'],
        ];
    }

    /**
     * @param $attribute
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function authVerify($attribute)
    {
        $auth = Yii::$app->wechat->miniProgram->auth->session($this->code);
        // 解析是否接口报错
        Yii::$app->debris->getWechatError($auth);

        $sign = sha1(htmlspecialchars_decode($this->rawData . $auth['session_key']));
        if ($sign !== $this->signature) {
            $this->addError($attribute, '签名错误');
            return;
        }

        $this->auth = $auth;
        $this->openid = $auth['openid'];
    }

    /**
     * @return mixed
     */
    public function getOpenid()
    {
        return $this->openid;
    }
    
    /**
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     */
    public function getUser()
    {
        $user = Yii::$app->wechat->miniProgram->encryptor->decryptData($this->auth['session_key'], $this->iv, $this->encryptedData);
        $user['openId'] = $this->openid;

        return $user;
    }

    /**
     * 获取微信手机号
     * @return array
     * @throws \EasyWeChat\Kernel\Exceptions\DecryptException
     * @throws \EasyWeChat\Kernel\Exceptions\HttpException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidArgumentException
     * @throws \EasyWeChat\Kernel\Exceptions\InvalidConfigException
     * @throws \EasyWeChat\Kernel\Exceptions\RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \yii\web\UnprocessableEntityHttpException
     */
    public function getPhoneNumber(){
        $auth = Yii::$app->wechat->miniProgram->auth->session($this->code);
        // 解析是否接口报错
        Yii::$app->debris->getWechatError($auth);
        $this->auth = $auth;
        return Yii::$app->wechat->miniProgram->encryptor->decryptData($this->auth['session_key'], $this->iv, $this->encryptedData);
    }

    public function getMember($unionId)
    {
        $auth = Auth::findOne(['unionid' => $unionId]);
        if ($auth) {
            $member = Member::findOne(['id' => $auth->member_id, 'status' => StatusEnum::ENABLED]);
            if ($member) {
                return $member;
            }
        }

        return false;
    }
}