<?php

namespace api\modules\v1\controllers;

use api\controllers\OnAuthController;
use api\modules\v1\forms\EnterpriseCategoryForm;
use common\helpers\ResultHelper;

class EnterpriseCategoryController extends OnAuthController
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

    /**
     * 分类列表
     * @return array|mixed
     */
    public function actionList() {
        $form = new EnterpriseCategoryForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_ENTERPRISE_CATEGORY_LIST);
        $data = \Yii::$app->request->get();
        $form->load($data,'');
        $res = $form->list();
        return  ResultHelper::json('200',"success",$res);
    }
}
