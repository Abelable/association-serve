<?php


namespace api\modules\admin\controllers;


use api\modules\admin\forms\ClassRoomForm;
use common\helpers\ResultHelper;

class ClassRoomController extends BaseController
{
    public $authMethod = [];


    /**
     * 保存作者信息
     * @return array|mixed
     */
    public function actionAuthorSave()
    {
        $form = new ClassRoomForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_AUTHOR_SAVE);
        // 加载数据
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->authorSave()){
            return  ResultHelper::json('422','保存失败',$form->errors);
        }
        return  ResultHelper::json('200',"success", []);
    }

    /**
     * 保存课堂
     * @return array|mixed
     */
    public function actionSave() {
        $form = new ClassRoomForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_CLASS_ROOM_SAVE);
        // 加载数据
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->classRoomSave()){
            return  ResultHelper::json('422','保存失败',$form->errors);
        }
        return  ResultHelper::json('200',"success", []);
    }

    /**
     * 列表
     * @return array|mixed
     */
    public function actionList() {
        $form = new ClassRoomForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_CLASS_ROOM_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->get(),'');
        if (!$res = $form->list()){
            return  ResultHelper::json('422','查询失败',$form->errors);
        }
        return  ResultHelper::json('200',"success", $res);
    }

    /**
     * 列表
     * @return array|mixed
     */
    public function actionAuthorList() {
        $form = new ClassRoomForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_AUTHOR_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->get(),'');
        if (!$res = $form->authorList()){
            return  ResultHelper::json('422','获取失败',$form->errors);
        }
        return  ResultHelper::json('200',"success", $res);
    }
}