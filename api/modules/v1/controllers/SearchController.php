<?php


namespace api\modules\v1\controllers;


use api\controllers\OnAuthController;
use api\models\UserIdentityInter;
use api\modules\v1\forms\SearchForm;
use common\helpers\ResultHelper;

class SearchController extends OnAuthController
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
    protected $authOptional = [];

    /**
     * 网商搜索
     * @return array|mixed
     */
    public function actionList() {
        $form = new SearchForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_SEARCH);
        $data = \Yii::$app->request->post();
        /**
         * @var UserIdentityInter $user
         */
        $user = \Yii::$app->user->identity;
        $data['open_id'] = $user->open_id;
        $form->load($data,'');
        if (!$res = $form->search()){
            return  ResultHelper::json('422',"查询失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 网商搜索
     * @return array|mixed
     */
    public function actionList1() {
        $form = new SearchForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_SEARCH);
        $data = \Yii::$app->request->post();
        /**
         * @var UserIdentityInter $user
         */
        $user = \Yii::$app->user->identity;
        $data['open_id'] = $user->open_id;
        $form->load($data,'');
        if (!$res = $form->search1()){
            return  ResultHelper::json('422',"查询失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }
}
