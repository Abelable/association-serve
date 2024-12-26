<?php

namespace common\sdk\alibabaCloud;


use AlibabaCloud\SDK\Dysmsapi\V20170525\Dysmsapi;

use Darabonba\OpenApi\Models\Config;
use AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsRequest;


class Sms
{

    /**
     * 使用AK&SK初始化账号Client
     * @param string $accessKeyId
     * @param string $accessKeySecret
     * @return Dysmsapi Client
     */
    public static function createClient($accessKeyId, $accessKeySecret){
        $config = new Config([
            // 您的AccessKey ID
            "accessKeyId" => $accessKeyId,
            // 您的AccessKey Secret
            "accessKeySecret" => $accessKeySecret
        ]);
        // 访问的域名
        $config->endpoint = "dysmsapi.aliyuncs.com";
        return new Dysmsapi($config);
    }

    /**
     * @param string[] $args
     * @return \AlibabaCloud\SDK\Dysmsapi\V20170525\Models\SendSmsResponse
     */
    public static function sendSms( array $args){
        $accessKeyId = isset($args['accessKeyId']) ? $args['accessKeyId'] : \Yii::$app->params['sms']['AccessKey'];
        $accessKeySecret = isset($args['accessKeySecret']) ? $args['AccessKeySecret'] : \Yii::$app->params['sms']['AccessKeySecret'];
        $client = self::createClient($accessKeyId, $accessKeySecret);
        $sendSmsRequest = new SendSmsRequest([
            "phoneNumbers" => $args['phoneNumbers'],
            "signName" => $args['signName'],
            "templateCode" => $args['templateCode'],
            "templateParam" => $args['templateParam'],
        ]);
        // 复制代码运行请自行打印 API 的返回值
        return $client->sendSms($sendSmsRequest);
    }


}