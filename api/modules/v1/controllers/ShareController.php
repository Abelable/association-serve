<?php


namespace api\modules\v1\controllers;


use api\controllers\OnAuthController;
use common\helpers\ResultHelper;

class ShareController extends OnAuthController
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
    protected $authOptional = [];

    public function actionMiniShare(){
        $memberId = \Yii::$app->user->identity->id ?? 0;
        $params = \Yii::$app->request->post();
        $data = \Yii::$app->services->shareService->getShareInfo($memberId,$params);
        return ResultHelper::json(200,'',$data);
    }
}