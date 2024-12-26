<?php


namespace api\modules\admin\controllers;


use api\modules\admin\forms\CompanyEvaluationForm;
use common\helpers\ResultHelper;
use common\models\common\CompanyEvaluation;
use common\models\common\LegalCategory;

class CompanyEvaluationController extends BaseController
{
    public $authMethod = [];


    /**
     * 保存
     * @return array|mixed
     */
    public function actionCompanyEvaluationSave() {
        $form = new CompanyEvaluationForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_COMPANY_EVALUATION_SAVE);
        // 加载数据
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->companyEvaluationSave()){
            return  ResultHelper::json('422','保存失败',$form->errors);
        }
        return  ResultHelper::json('200',"success", []);
    }

    /**
     * 列表
     * @return array|mixed
     */
    public function actionCompanyEvaluationList() {
        $form = new CompanyEvaluationForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_COMPANY_EVALUATION_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->get(),'');
        if (!$res = $form->companyEvaluationList()){
            return  ResultHelper::json('422',$form->errors);
        }
        return  ResultHelper::json('200',"success", $res);
    }


    /**
     * 保存
     * @return array|mixed
     */
    public function actionCompanySentenceSave() {
        $form = new CompanyEvaluationForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_COMPANY_SENTENCE_SAVE);
        // 加载数据
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->companySentenceSave()){
            return  ResultHelper::json('422','保存失败',$form->errors);
        }
        return  ResultHelper::json('200',"success", []);
    }

    /**
     * 列表
     * @return array|mixed
     */
    public function actionCompanySentenceList() {
        $form = new CompanyEvaluationForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_COMPANY_SENTENCE_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->get(),'');
        if (!$res = $form->companySentenceList()){
            return  ResultHelper::json('422',$form->errors);
        }
        return  ResultHelper::json('200',"success", $res);
    }


}