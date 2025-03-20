<?php

namespace api\modules\admin\controllers;

use api\modules\admin\forms\ClassRoomApplyForm;
use common\helpers\ResultHelper;

class ClassRoomApplyController extends BaseController
{
    public $authMethod = [];

    public function actionList() {
        $form = new ClassRoomApplyForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_CLASS_ROOM_APPLY_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->get(),'');
        if (!$res = $form->list()){
            return  ResultHelper::json('422',"保存失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }
}