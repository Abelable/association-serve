<?php
/**
 * User: sun
 * Date: 2021/8/17
 * Time: 9:04 上午
 */

namespace api\modules\v1\forms;


use common\models\member\Auth;
use yii\base\Model;
use yii\httpclient\Client;

class MiniWxForm  extends Model
{

    /**
     * 绑定微信信息
     */
    const SCENARIO_BIND = 'scenario_bind';


    /**
     * 通过code 换取小程序的 openid
     */
    const SCENARIO_CODE = 'scenario_code';

    /**
     * @var string 用户的open_id
     */
    public $open_id = '';

    /**
     * @var string 用户昵称
     */
    public $nickName = '';

    /**
     * @var string 性别
     */
    public $gender = 0;

    /**
     * @var string 市
     */
    public $city = '';


    /**
     * @var string 省
     */
    public $province = '';

    /**
     * @var string 国家
     */
    public $country = '';

    /**
     * @var string 头像地址
     */
    public $avatarUrl = '';

    /**
     * @var string 登录时获取的 code
     */
    public $code = '';


    /**
     * @var string 小程序的appid
     */
    public $appid = '';

    /**
     * @var string 小程序的 secret
     */
    public $secret = '';


    public function init()
    {
        parent::init();

        // Retrieve appid and secret from configuration
        $this->appid = \Yii::$app->params['wechat']['appid'];
        $this->secret = \Yii::$app->params['wechat']['secret'];
    }

    public function rules()
    {
        return [
            // 绑定账号数据信息
            [['open_id','nickName','gender','avatarUrl'],'required','on'=>[static::SCENARIO_BIND]],
            [['avatarUrl'],'url','on'=>[static::SCENARIO_BIND]],
            [['open_id','nickName','city','province','country'],'string','on'=>[static::SCENARIO_BIND]],

            // 通过小程序的code 获取 openid
            [['code'],'required','on'=>[static::SCENARIO_CODE]],
        ];
    }

    /**
     * 绑定微信用户
     */
    public function MiniWxBind(){
        if (!$this->validate()){
            return false;
        }

        if (!$Auth = Auth::findOne(['open_id'=>$this->open_id])){
            $Auth = new Auth();
            $Auth->open_id = $this->open_id;
        }
        $Auth->nickname = $this->nickName;
        $Auth->gender = $this->gender;
        $Auth->avatar_url = $this->avatarUrl;
        $Auth->city = $this->city;
        $Auth->country = $this->country;
        $Auth->province = $this->province;
        if (!$Auth->save()){
            \Yii::error("保存微信用户信息失败 res:".json_encode($Auth->errors,JSON_UNESCAPED_UNICODE));
            $this->addError("id",'保存用户信息失败');
            return false;
        }
        return true;
    }


    /**
     * 通过code 获取openid
     */
    public function MiniWxOpenid(){

        if (!$this->validate()){
            return false;
        }


        $appid = $this->appid;
        $secret = $this->secret;

        $Client = new Client([
            'baseUrl'=>"https://api.weixin.qq.com",
            'responseConfig' => [
                'format' => Client::FORMAT_JSON
            ]
        ]);
        $resClient = $Client->get(
            '/sns/jscode2session',
            ['appid'=>$appid,'secret'=>$secret,'js_code'=>$this->code,'grant_type'=>'authorization_code']
        )->send();


        $resArr = $resClient->getData();
        if (!is_array($resArr)){
            \Yii::error("调用微信信息失败 res:".$resClient->content);
            $this->addError('id','调用微信信息失败');
            return false;
        }
        if (empty($resArr['openid'])){
            \Yii::error("微信接口返回信息异常 res:".$resClient->content);
            $this->addError('id','微信接口返回信息异常:'.$resClient->content);
            return false;
        }


        $Auth = Auth::findOne(['open_id'=>$resArr['openid']]);
        $res['open_id'] = $resArr['openid'];
        $res['nickName'] = '';
        $res['gender'] = '';
        $res['avatarUrl'] = '';
        $res['city'] = '';
        $res['country'] = '';
        $res['province'] = '';
        $res['is_bind'] = 0;
        $res['session_key'] = $resArr['session_key'];

        if ($Auth){
            $res['is_bind'] = 1;
            $res['nickName'] = $Auth->nickname;
            $res['gender'] = $Auth->gender;
            $res['avatarUrl'] = $Auth->avatar_url;
            $res['city'] = $Auth->city;
            $res['country'] = $Auth->country;
            $res['province'] = $Auth->province;
        }

        return $res;

    }

}