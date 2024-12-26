<?php
/**
 * User: sun
 * Date: 2021/8/12
 * Time: 12:34 下午
 */

namespace api\modules\admin\controllers;


use api\modules\admin\forms\EnterFormForm;
use common\helpers\ResultHelper;

class EnterFormController extends BaseController
{

    public $authMethod = ['registered-export'];

    /**
     * 表单报名列表数据
     */
    public function actionList(){
        $EnterFromForm = new EnterFormForm();
        // 设置场景
        $EnterFromForm->setScenario($EnterFromForm::SCENARIO_LIST);
        // 加载数据
        $EnterFromForm->load(\Yii::$app->request->get(),'');
        if (!$res = $EnterFromForm->formList()){
            return  ResultHelper::json('422',"获取列表失败",$EnterFromForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }


    /**
     * 创建/修改报表列表数据
     */
    public function actionSave(){
        $EnterFromForm = new EnterFormForm();

        $fromRes = false;
        if (\Yii::$app->request->post('id',0)){
            // 设置场景
            $EnterFromForm->setScenario($EnterFromForm::SCENARIO_UPDATE);
            // 数据加载放到场景设置的后面 保证设置了safe 的属性能正常使用
            $EnterFromForm->load(\Yii::$app->request->post(),'');
            $fromRes = $EnterFromForm->formUpdate();
        }else{
            // 设置场景
            $EnterFromForm->setScenario($EnterFromForm::SCENARIO_CREATE);
            // 数据加载放到场景设置的后面 保证设置了safe 的属性能正常使用
            $EnterFromForm->load(\Yii::$app->request->post(),'');
            $fromRes = $EnterFromForm->formCreate();
        }

        if (!$fromRes){
            return  ResultHelper::json('422',"保存失败",$EnterFromForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }


    /**
     * 报名表单列表数据的状态更新处理
     */
    public function actionActive(){
        $EnterFromForm = new EnterFormForm();
        $EnterFromForm->setScenario($EnterFromForm::SCENARIO_ACTIVE);
        $EnterFromForm->load(\Yii::$app->request->post(),'');

        if (!$EnterFromForm->formActiveUpdate()){
            return  ResultHelper::json('422',"更新失败",$EnterFromForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }


    /**
     * 获取某一个表单数据的详情信息
     */
    public function actionInfo(){
        $EnterFromForm = new EnterFormForm();
        $EnterFromForm->setScenario($EnterFromForm::SCENARIO_INFO);
        $EnterFromForm->load(\Yii::$app->request->get(),'');



        if (!$res = $EnterFromForm->formInfo()){
            return  ResultHelper::json('422',"获取数据失败",$EnterFromForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);

    }

    /**
     * 自定义活动
     * @return array|mixed
     */
    public function actionCustomEventSave() {
        $form = new EnterFormForm();
        $form->setScenario($form::SCENARIO_CUSTOM_EVENT_SAVE);
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->customEventSave()){
            return  ResultHelper::json('422',"获取数据失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    public function actionCustomEventList() {
        $form = new EnterFormForm();
        $form->setScenario($form::SCENARIO_CUSTOM_EVENT_LIST);
        $form->load(\Yii::$app->request->get(),'');
        if (!$res = $form->customEventList()){
            return  ResultHelper::json('422',"获取数据失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 提前开始和结束活动
     * @return array|mixed
     */
    public function actionCustomEventOperate() {
        $form = new EnterFormForm();
        $form->setScenario($form::SCENARIO_CUSTOM_EVENT_OPERATE);
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->customEventOperate()){
            return  ResultHelper::json('422',"操作失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 活动报名列表
     * @return array|mixed
     */
    public function actionRegisteredList() {
        $form = new EnterFormForm();
        $form->setScenario($form::SCENARIO_REGISTERED_LIST);
        $form->load(\Yii::$app->request->get(),'');
        $res = $form->registeredList();
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 报名数据编辑
     * @return array|mixed
     */
    public function actionRegisteredSave() {
        $form = new EnterFormForm();
        $form->setScenario($form::SCENARIO_REGISTERED_SAVE);
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->registeredSave()){
            return  ResultHelper::json('422',"保存失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 报名活动列表导出
     * @return array|mixed
     */
    public function actionRegisteredExport() {
        $form = new EnterFormForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_REGISTERED_EXPORT);
        $form->load(\Yii::$app->request->get(),'');

        if (!$res = $form->registeredExport()){
            return  ResultHelper::json('422',"导出失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }
}