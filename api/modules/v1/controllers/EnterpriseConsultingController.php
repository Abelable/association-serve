<?php

namespace api\modules\v1\controllers;

use api\controllers\OnAuthController;
use api\modules\v1\forms\EnterpriseConsultingForm;
use common\helpers\ResultHelper;

class EnterpriseConsultingController extends OnAuthController
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
    protected $authOptional = ['save'];

    public function actionSave() {
        $form = new EnterpriseConsultingForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_ENTERPRISE_CONSULTING_SAVE);
        // 加载数据
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->save()){
            return  ResultHelper::json('422',$form->errors);
        }
        return  ResultHelper::json('200',"success", []);
    }
}
