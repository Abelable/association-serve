<?php


namespace api\modules\admin\controllers;


use api\modules\admin\forms\MemberServiceForm as LegalForm;
use common\helpers\ResultHelper;
use common\models\common\LegalCategory;

class MemberServiceController extends BaseController
{
    public $authMethod = [];


    /**
     * 保存法律汇编分类
     * @return array|mixed
     */
    public function actionCategorySave() {
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_CATEGORY_SAVE);
        // 加载数据
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->categorySave()){
            return  ResultHelper::json('422','保存失败',$form->errors);
        }
        return  ResultHelper::json('200',"success", []);
    }

    /**
     * 分类列表
     * @return array|mixed
     */
    public function actionCategoryList() {
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_CATEGORY_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->get(),'');
        if (!$res = $form->categoryList()){
            return  ResultHelper::json('422',$form->errors);
        }
        return  ResultHelper::json('200',"success", $res);
    }

    /**
     * 法律汇编保存
     * @return array|mixed
     */
    public function actionLegalSave() {
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_SAVE);
        // 加载数据
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->legalSave()){
            return  ResultHelper::json('422','保存失败',$form->errors);
        }
        return  ResultHelper::json('200',"success", []);
    }

    /**
     * 法律汇编列表
     * @return array|mixed
     */
    public function actionLegalList() {
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->get(),'');
        $res = $form->legalList();
        if ($res == false){
            return  ResultHelper::json('422','查询失败',$form->errors);
        }
        return  ResultHelper::json('200',"success", $res);
    }
}