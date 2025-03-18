<?php

namespace api\modules\v1\controllers;

use api\controllers\OnAuthController;
use api\modules\v1\forms\ActivityForm;
use common\helpers\ResultHelper;

class ActivityController extends OnAuthController
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
    protected $authOptional = ['list'];

    public function actionList() {
        $form = new ActivityForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_ACTIVITY_LIST);
        $data = \Yii::$app->request->get();
        $form->load($data,'');
        $res = $form->list();
        return  ResultHelper::json('200',"success", $res);
    }
}
