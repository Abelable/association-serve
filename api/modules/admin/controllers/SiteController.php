<?php
namespace api\modules\admin\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use common\helpers\ResultHelper;
use common\helpers\ArrayHelper;
use api\controllers\OnAuthController;

class SiteController extends OnAuthController
{
    public $modelClass = '';

    /**
     * 不用进行登录验证的方法
     *
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['admin-login'];

    /**
     * 后台登录
     *
     * @return string|\yii\web\Response
     * @throws \yii\base\InvalidConfigException
     */
    public function actionAdminLogin()
    {
        /*
        if (!Yii::$app->user->isGuest) {
            // 记录行为日志
            Yii::$app->services->actionLog->create('login', '自动登录', false);

            return $this->goHome();
        }
        */

        $model = new \backend\forms\LoginForm();
        $model->loginCaptchaRequired();
        $model->attributes = Yii::$app->request->post();
        if ($model->login()) {
            $user = $model->getUser();
            $access_token = Yii::$app->security->generateRandomString() . '_' . time();
            /*
            $redis = Yii::$app->redis;
            $redis->set($access_token, $user->id);
            $redis->expire($access_token,7 * 24 * 3600);
            */
            Yii::$app->cache->set($access_token, $user, 7 * 24 * 3600);
            // 记录行为日志
//            Yii::$app->services->actionLog->create('login', '账号登录', false);
            return ResultHelper::json(200,'成功', ['token' => $access_token]);
        }
        return ResultHelper::json(422, '登陆失败',[]);
    }
}