<?php

namespace api\modules\admin\controllers;

use api\modules\admin\forms\EnterpriseConsultingForm;
use common\helpers\ResultHelper;

class EnterpriseConsultingController extends BaseController
{
    public $authMethod = [];

    public function actionList() {
        $form = new EnterpriseConsultingForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_ENTERPRISE_CONSULTING_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->get(),'');
        if (!$res = $form->list()){
            return  ResultHelper::json('422',"保存失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }
}