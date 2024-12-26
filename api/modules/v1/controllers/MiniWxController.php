<?php
/**
 * User: sun
 * Date: 2021/8/17
 * Time: 9:04 上午
 */

namespace api\modules\v1\controllers;


use api\controllers\OnAuthController;
use api\modules\v1\forms\MiniWxForm;
use common\helpers\ResultHelper;
use common\models\member\Auth;

class MiniWxController extends OnAuthController
{

    public $modelClass = '';

    protected $authOptional = ['bind','info','openid'];


    /**
     * 绑定微信信息
     */
    public function actionBind(){
        $MiniWxForm = new MiniWxForm();
        $MiniWxForm->setScenario($MiniWxForm::SCENARIO_BIND);
        $data = \Yii::$app->request->post();
        $data['open_id'] = \Yii::$app->request->headers->get('open-id','');
        $MiniWxForm->load($data,'');
        if (!$MiniWxForm->MiniWxBind()){
            return  ResultHelper::json('422',"绑定数据失败",$MiniWxForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }


    /**
     * 用户用户信息
     */
    public function actionInfo(){
        $open_id = \Yii::$app->request->headers->get('open-id','');
        $Auth = Auth::findOne(['open_id'=>$open_id]);
        $res['open_id'] = $open_id;
        $res['nickName'] = '';
        $res['gender'] = '';
        $res['avatarUrl'] = '';
        $res['city'] = '';
        $res['country'] = '';
        $res['province'] = '';
        $res['is_bind'] = 0;

        if ($Auth){
            $res['is_bind'] = 1;
            $res['nickName'] = $Auth->nickname;
            $res['gender'] = $Auth->gender;
            $res['avatarUrl'] = $Auth->avatar_url;
            $res['city'] = $Auth->city;
            $res['country'] = $Auth->country;
            $res['province'] = $Auth->province;
        }
        return  ResultHelper::json('200',"success",$res);
    }


    /**
     * 通过code 换取用户的openid
     */
    public function actionOpenid(){
        $MiniWxForm = new MiniWxForm();
        $MiniWxForm->setScenario($MiniWxForm::SCENARIO_CODE);
        $MiniWxForm->load(\Yii::$app->request->post(),'');

        if (!$res = $MiniWxForm->MiniWxOpenid()){
            return  ResultHelper::json('422',"查询用户失败",$MiniWxForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }
}