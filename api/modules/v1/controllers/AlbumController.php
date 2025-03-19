<?php

namespace api\modules\v1\controllers;

use api\controllers\OnAuthController;
use api\modules\v1\forms\AlbumForm;
use common\helpers\ResultHelper;

class AlbumController extends OnAuthController
{
    public $modelClass = '';

    /**
     * 不用进行登录验证的方法
     *
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['list','detail'];

    public function actionList() {
        $form = new AlbumForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_ALBUM_LIST);
        $data = \Yii::$app->request->get();
        $form->load($data,'');
        $res = $form->list();
        return  ResultHelper::json('200',"success",$res);
    }

    public function actionDetail() {
        $form = new AlbumForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_ALBUM_DETAIL);
        $data = \Yii::$app->request->get();
        $form->load($data,'');
        if (!$res = $form->detail()){
            return  ResultHelper::json('422',"查询失败", $form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }
}
