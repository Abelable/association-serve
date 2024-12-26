<?php
namespace api\modules\admin\controllers;

use Yii;
use common\helpers\ResultHelper;
use \yii\base\Controller;

class BaseController extends Controller
{
    public $authMethod = [];

    /**
     * @return array|mixed|void
     */
    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }

    public function beforeAction($action)
    {
       parent::beforeAction($action);
       
        if (!in_array($action->id,$this->authMethod)){
            $headers = Yii::$app->request->headers;
            if (!$headers->has('token')){
                Yii::$app->response->data = ResultHelper::json(420, '不存在请求头token',[]);
                Yii::$app->end();
            }
            if (!Yii::$app->cache->exists($headers['token'])){
                Yii::$app->response->data = ResultHelper::json(420, '登陆已失效',[]);
                Yii::$app->end();
            }
        }

        return $action;
    }
}