<?php

namespace api\behaviors;

use Yii;
use yii\base\Behavior;

/**
 * Class BeforeSend
 * @package api\behaviors
 * @author jianyan74 <751393839@qq.com>
 */
class BeforeSend extends Behavior
{
    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            'beforeSend' => 'beforeSend',
        ];
    }

    /**
     * 格式化返回
     *
     * @param $event
     * @throws \yii\base\InvalidConfigException
     */
    public function beforeSend($event)
    {


        /**
         * @var yii\web\Response $response
         */
        $response = $event->sender;

        $response->format = yii\web\Response::FORMAT_JSON;

        if (Yii::$app->request->isOptions){
            return;
        }

        if (YII_DEBUG && isset(Yii::$app->controller->module->id) && Yii::$app->controller->module->id === "debug") {
            return;
        }


        if (isset($response->content)){
            return $response->content;
        }

        // 不进行格式化出处理
        if (Yii::$app->params['triggerBeforeSend'] == false) {
            $response = $event->sender;
            $response->format = yii\web\Response::FORMAT_JSON;
            $response->statusCode = 200;
            return;
        }

        $response->data = [
            'code' => $response->data['code'],
            'message' => $response->data['message'],
            'data' => $response->data['data'],
            'timestamp' => time(),
        ];

        // 记录日志
        $errData = Yii::$app->services->log->record($response, true);

        // 格式化报错输入格式
        if ($response->statusCode >= 500) {
//            $response->data['data'] = YII_DEBUG ? $errData : '服务器打瞌睡了~';
            $response->data['data'] = $errData;
        }

        // 提取系统 300-499 的报错信息
        if ($response->statusCode >= 300 && $response->statusCode <= 499) {
            if (isset($response->data['data']['message']) && isset($response->data['data']['status'])) {
                $response->data['message'] = $response->data['data']['message'];
            }

            if (isset($errData['errorMessage'])) {
                $response->data['message'] = $errData['errorMessage'];
                $response->data['message'] == $response->data['data'] && $response->data['data'] = [];
            }
        }

        // 加入ip黑名单
        $response->statusCode == 429 && Yii::$app->services->ipBlacklist->create(Yii::$app->request->userIP, '请求频率过高');


        // 考虑到了某些前端必须返回成功操作，所以这里可以设置为都返回200的状态码
//        $response->statusCode = 200;
    }
}