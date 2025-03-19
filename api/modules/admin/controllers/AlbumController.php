<?php

namespace api\modules\admin\controllers;

use api\modules\admin\forms\AlbumForm;
use common\helpers\ResultHelper;

class AlbumController extends BaseController
{
    public $authMethod = [];

    /**
     * 保存公开信息
     * @return array|mixed
     */
    public function actionSave() {
        $form = new AlbumForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_ALBUM_SAVE);
        // 加载数据
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->save()){
            return ResultHelper::json('422',$form->errors);
        }
        return  ResultHelper::json('200',"success", []);
    }

    public function actionList() {
        $form = new AlbumForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_ALBUM_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->get(),'');
        if (!$res = $form->list()){
            return  ResultHelper::json('422',"查询失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }
}