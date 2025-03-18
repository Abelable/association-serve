<?php

namespace api\modules\admin\controllers;

use api\modules\admin\forms\ActivityCategoryForm;
use common\helpers\ResultHelper;

class ActivityCategoryController extends BaseController
{
    public $authMethod = [];

    /**
     * 保存分类
     * @return array|mixed
     */
    public function actionSave() {
        $form = new ActivityCategoryForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_ACTIVITY_CATEGORY_SAVE);
        // 加载数据
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->categorySave()){
            return  ResultHelper::json('422',$form->errors);
        }
        return  ResultHelper::json('200',"success", []);
    }

    public function actionList() {
        $form = new ActivityCategoryForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_ACTIVITY_CATEGORY_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->get(),'');
        if (!$res = $form->categoryList()){
            return  ResultHelper::json('422',"保存失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    public function actionOptions() {
        $form = new ActivityCategoryForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_ACTIVITY_CATEGORY_OPTIONS);
        // 加载数据
        $form->load(\Yii::$app->request->get(),'');
        if (!$res = $form->categoryOptions()){
            return  ResultHelper::json('422',"保存失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }
}