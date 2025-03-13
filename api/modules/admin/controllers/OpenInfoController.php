<?php

namespace api\modules\admin\controllers;

use api\modules\admin\forms\OpenInfoForm;
use common\helpers\ResultHelper;

class OpenInfoController extends BaseController
{
    public $authMethod = [];

    /**
     * 保存公开信息
     * @return array|mixed
     */
    public function actionSave() {
        $form = new OpenInfoForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_OPEN_INFO_SAVE);
        // 加载数据
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->openInfoSave()){
            return  ResultHelper::json('422',$form->errors);
        }
        return  ResultHelper::json('200',"success", []);
    }

    public function actionList() {
        $form = new OpenInfoForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_OPEN_INFO_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->get(),'');
        if (!$res = $form->openInfoList()){
            return  ResultHelper::json('422',"保存失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }
}