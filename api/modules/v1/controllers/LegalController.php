<?php


namespace api\modules\v1\controllers;


use api\controllers\OnAuthController;
use api\models\UserIdentityInter;
use api\modules\v1\forms\LegalForm;
use common\helpers\ResultHelper;
use common\models\common\LegalCategory;

class LegalController extends OnAuthController
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
    protected $authOptional = ['category-list', 'list', 'detail', 'list1', 'category-list1', 'detail1'];


    /**
     * 法律汇编列表
     * @return array|mixed
     */
    public function actionList() {
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_LIST);
        $data = \Yii::$app->request->get();
        /**
         * @var UserIdentityInter $user
         */
        /*$user = \Yii::$app->user->identity;
        $data['open_id'] = $user->open_id;*/
        $form->load($data,'');
        $res = $form->list();
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 法律汇编分类列表
     * @return array|mixed
     */
    public function actionCategoryList() {
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_CATEGORY_LIST);
        $data = \Yii::$app->request->get();
        /**
         * @var UserIdentityInter $user
         */
        /*$user = \Yii::$app->user->identity;
        $data['open_id'] = $user->open_id;*/
        $form->load($data,'');
        $res = $form->categoryList();
        return  ResultHelper::json('200',"success",$res);

    }

    /**
     * 法律汇编详情
     * @return array|mixed
     */
    public function actionDetail(){
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_DETAIL);
        $data = \Yii::$app->request->get();
        /**
         * @var UserIdentityInter $user
         */
        $user = \Yii::$app->user->identity;
        $data['user_id'] = $user->id ?? 0;
        $form->load($data,'');
        if (!$res = $form->detail()){
            return  ResultHelper::json('422',"查询失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 点赞、取消点赞
     * @return array|mixed
     */
    public function actionLike() {
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_LIKE);
        $data = \Yii::$app->request->post();
        /**
         * @var UserIdentityInter $user
         */
        $user = \Yii::$app->user->identity;
        $data['user_id'] = $user->id;
        $form->load($data,'');
        if (!$res = $form->like()){
            return  ResultHelper::json('422',"查询失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 收藏、取消收藏
     * @return array|mixed
     */
    public function actionCollect() {
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_LIKE);
        $data = \Yii::$app->request->post();
        /**
         * @var UserIdentityInter $user
         */
        $user = \Yii::$app->user->identity;
        $data['user_id'] = $user->id;
        $form->load($data,'');
        if (!$res = $form->collect()){
            return  ResultHelper::json('422',"查询失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 收藏列表
     * @return array|mixed
     */
    public function actionCollectList() {
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_LIKE);
        $data = \Yii::$app->request->get();
        /**
         * @var UserIdentityInter $user
         * */
        $user = \Yii::$app->user->identity;
        $data['user_id'] = $user->id;
        $form->load($data,'');
        $res = $form->collectList();
        return  ResultHelper::json('200',"success",$res);
    }

    //=============================================
    /**
     * 法律汇编列表
     * @return array|mixed
     */
    public function actionList1() {
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_LIST);
        $data = \Yii::$app->request->get();
        /**
         * @var UserIdentityInter $user
         */
        /*$user = \Yii::$app->user->identity;
        $data['open_id'] = $user->open_id;*/
        $form->load($data,'');
        $res = $form->list1();
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 法律汇编详情
     * @return array|mixed
     */
    public function actionDetail1(){
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_DETAIL);
        $data = \Yii::$app->request->get();
        /**
         * @var UserIdentityInter $user
         */
        $user = \Yii::$app->user->identity;
        $data['user_id'] = $user->id ?? 0;
        $form->load($data,'');
        if (!$res = $form->detail1()){
            return  ResultHelper::json('422',"查询失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 点赞、取消点赞
     * @return array|mixed
     */
    public function actionLike1() {
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_LIKE);
        $data = \Yii::$app->request->post();
        /**
         * @var UserIdentityInter $user
         */
        $user = \Yii::$app->user->identity;
        $data['user_id'] = $user->id;
        $form->load($data,'');
        if (!$res = $form->like1()){
            return  ResultHelper::json('422',"查询失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 收藏、取消收藏
     * @return array|mixed
     */
    public function actionCollect1() {
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_LIKE);
        $data = \Yii::$app->request->post();
        /**
         * @var UserIdentityInter $user
         */
        $user = \Yii::$app->user->identity;
        $data['user_id'] = $user->id;
        $form->load($data,'');
        if (!$res = $form->collect1()){
            return  ResultHelper::json('422',"查询失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 收藏列表
     * @return array|mixed
     */
    public function actionCollectList1() {
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_LIKE);
        $data = \Yii::$app->request->get();
        /**
         * @var UserIdentityInter $user
         * */
        $user = \Yii::$app->user->identity;
        $data['user_id'] = $user->id;
        $form->load($data,'');
        $res = $form->collectList1();
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 法律汇编分类列表
     * @return array|mixed
     */
    public function actionCategoryList1() {
        $form = new LegalForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LEGAL_CATEGORY_LIST);
        $data = \Yii::$app->request->get();
        /**
         * @var UserIdentityInter $user
         */
        /*$user = \Yii::$app->user->identity;
        $data['open_id'] = $user->open_id;*/
        $form->load($data,'');
        $res = $form->categoryList1();
        return  ResultHelper::json('200',"success",$res);

    }

}
