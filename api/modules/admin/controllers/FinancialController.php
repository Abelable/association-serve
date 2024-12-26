<?php


namespace api\modules\admin\controllers;


use api\modules\admin\forms\FinancialForm;
use common\helpers\ResultHelper;
use common\models\common\Financial;

class FinancialController extends BaseController
{
    public $authMethod = [];


    /**
     * 保存
     * @return array|mixed
     */
    public function actionFinancialSave()
    {
        $form = new FinancialForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_FINANCIAL_SAVE);
        // 加载数据
        $form->load(\Yii::$app->request->post(), '');
        if (!$res = $form->financialSave()) {
            return ResultHelper::json('422', '保存失败', $form->errors);
        }
        return ResultHelper::json('200', "success", []);
    }

    /**
     * 列表
     * @return array|mixed
     */
    public function actionFinancialList()
    {
        $form = new FinancialForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_FINANCIAL_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->get(), '');
        if (!$res = $form->financialList()) {
            return ResultHelper::json('422', $form->errors);
        }
        return ResultHelper::json('200', "success", $res);
    }


    /**
     * 保存
     * @return array|mixed
     */
    public function actionFinancialOutSave()
    {
        $form = new FinancialForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_FINANCIAL_OUT_SAVE);
        // 加载数据
        $form->load(\Yii::$app->request->post(), '');
        if (!$res = $form->financialOutSave()) {
            return ResultHelper::json('422', '保存失败', $form->errors);
        }
        return ResultHelper::json('200', "success", []);
    }

    /**
     * 列表
     * @return array|mixed
     */
    public function actionFinancialOutList()
    {
        $form = new FinancialForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_FINANCIAL_OUT_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->get(), '');
        if (!$res = $form->financialOutList()) {
            return ResultHelper::json('422', $form->errors);
        }
        return ResultHelper::json('200', "success", $res);
    }

}

