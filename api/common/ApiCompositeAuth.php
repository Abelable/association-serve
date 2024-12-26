<?php
/**
 * User: sun
 * Date: 2021/8/16
 * Time: 11:45 上午
 */

namespace api\common;


use common\helpers\ResultHelper;
use yii\filters\auth\CompositeAuth;
use yii\web\UnauthorizedHttpException;

class ApiCompositeAuth extends CompositeAuth
{

    /**
     * {@inheritdoc}
     */
    public function handleFailure($response)
    {
        \Yii::$app->response->data = ResultHelper::json(401, "需要进行登入");
        \Yii::$app->end();
    }
}