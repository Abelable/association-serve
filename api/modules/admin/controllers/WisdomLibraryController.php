<?php


namespace api\modules\admin\controllers;


use api\modules\admin\forms\WisdomLibraryForm;
use common\helpers\ResultHelper;

class WisdomLibraryController extends BaseController
{
    public $authMethod = [];


    /**
     * 保存智库
     * @return array|mixed
     */
    public function actionArticleSave() {
        $form = new WisdomLibraryForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_ARTICLE_SAVE);
        // 加载数据
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->articleSave()){
            return  ResultHelper::json('422',$form->errors);
        }
        return  ResultHelper::json('200',"success", []);
    }

    public function actionArticleList() {
        $form = new WisdomLibraryForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_ARTICLE_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->get(),'');
        if (!$res = $form->articleList()){
            return  ResultHelper::json('422',"保存失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }
}