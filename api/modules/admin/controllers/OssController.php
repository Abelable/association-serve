<?php
/**
 * User: sun
 * Date: 2021/8/12
 * Time: 5:03 下午
 */

namespace api\modules\admin\controllers;


use common\helpers\ResultHelper;

class OssController extends BaseController
{


    public $authMethod = ['ali'];

    /**
     * @return mixed 获取阿里云的上传地址
     */
    public function actionAli(){
        $AccessKey = \Yii::$app->params['alyoss']['AccessKey'];
        $AccessKeySecret = \Yii::$app->params['alyoss']['AccessKeySecret'];
        $EndPoint = '//'.\Yii::$app->params['alyoss']['cdnDomain'];
        $CallbackUrl = \Yii::$app->params['alyoss']['CallbackUrl'];
        $Dir = \Yii::$app->params['alyoss']['Dir'].date('Ymd')."/";
        $Expire = \Yii::$app->params['alyoss']['Expire'];

        $callback_param = [
            'callbackUrl'=>$CallbackUrl,
            'callbackBody'=>'',
            'callbackBodyType'=>'application/json;charset=UTF-8'
        ];
        $callback_string = json_encode($callback_param);
        $base64_callback_body = base64_encode($callback_string);

        // 失效时间
        $end = time() + $Expire;
        $expiration = $this->gmt_iso8601($end);
        // 文件大小限制
        $conditions[]   = ['content-length-range', 0, 1048576000];
        $conditions[] = ['starts-with', '$key', 2=>$Dir];

        $arr = ['expiration'=>$expiration,'conditions'=>$conditions];
        $policy = json_encode($arr);
        $base64_policy = base64_encode($policy);
        $string_to_sign = $base64_policy;
        $signature = base64_encode(hash_hmac('sha1', $string_to_sign, $AccessKeySecret, true));

        $response = [];
        $response['OSSAccessKeyId'] = $AccessKey;
        $response['host'] = $EndPoint;
        $response['policy'] = $base64_policy;
        $response['signature'] = $signature;
        $response['expire'] = $end;
        $response['callback'] = $base64_callback_body;
        $response['dir'] = $Dir;  // 这个参数是设置用户上传文件时指定的前缀。

        return ResultHelper::json(200, 'success',$response);
    }

    public function gmt_iso8601($time) {
        $dtStr = date("c", $time);
        $mydatetime = new \DateTime($dtStr);
        $expiration = $mydatetime->format(\DateTime::ISO8601);
        $pos = strpos($expiration, '+');
        $expiration = substr($expiration, 0, $pos);
        return $expiration."Z";
    }

}