<?php


namespace api\modules\v1\controllers;

use api\controllers\OnAuthController;
use api\models\UserIdentityInter;
use api\modules\v1\forms\EnterApplyForm;
use common\helpers\ResultHelper;
use yii\base\BaseObject;

class EnterApplyController extends OnAuthController
{
    public $modelClass = '';

    /**
     * 不用进行登录验证的方法
     * 例如： ['index', 'update', 'create', 'view', 'delete']
     * 默认全部需要验证
     *
     * @var array
     */
    protected $authOptional = ['list', 'detail', 'apply', 'custom-event-detail', 'custom-event-apply'];

    /**
     * @param $id
     * @return array|mixed
     * @note:申请操作
     */
    public function actionApply()
    {
        $EnterApplyForm = new EnterApplyForm();


        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_APPLY);
        // 数据加载放到场景设置的后面 保证设置了safe 的属性能正常使用
        $data = \Yii::$app->request->post();

        /**
         * @var UserIdentityInter $user
         */
        $user = \Yii::$app->user->identity;
        $data['open_id'] = $user->open_id;
        $data['apply_content_json'] = json_decode($data['apply_content_json'],true);
        $EnterApplyForm->load($data,'');
        $fromRes = $EnterApplyForm->apply();

        if (!$fromRes){
            return  ResultHelper::json('422',"申请失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }

    /**
     * @return array|mixed 更新表单信息
     */
    public function actionUpdateApply(){
        $EnterApplyForm = new EnterApplyForm();

        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_APPLY_UPDATE);
        $data = \Yii::$app->request->post();
        /**
         * @var UserIdentityInter $user
         */
        $user = \Yii::$app->user->identity;
        $data['open_id'] = $user->open_id;

        $data['apply_content_json'] = json_decode($data['apply_content_json'],true);
        // 数据加载放到场景设置的后面 保证设置了safe 的属性能正常使用
        $EnterApplyForm->load($data,'');
        if (!$EnterApplyForm->updateApply()){
            return  ResultHelper::json('422',"更新失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }

    /**
     * 查询列表数据
     * @return array|mixed
     */
    public function actionListApply(){
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_APPLY_LIST);
        $data = \Yii::$app->request->get();
        /**
         * @var UserIdentityInter $user
         */
        $user = \Yii::$app->user->identity;
        $data['open_id'] = $user->open_id;


        // 数据加载放到场景设置的后面 保证设置了safe 的属性能正常使用
        $EnterApplyForm->load($data,'');
        if (!$res = $EnterApplyForm->listApply()){
            return  ResultHelper::json('422',"查询失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 企业列表
     * @return array|mixed
     */
    public function actionList() {
        $form = new EnterApplyForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_LIST);
        $data = \Yii::$app->request->get();
        $form->load($data,'');
        $res = $form->list();
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 企业详情
     * @return array|mixed
     */
    public function actionDetail() {
        $form = new EnterApplyForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_DETAIL);
        $data = \Yii::$app->request->get();
        $form->load($data,'');
        $res = $form->detail();
        return  ResultHelper::json('200',"success",$res);
    }

    public function actionInfoApply(){
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_APPLY_INFO);
        $data = \Yii::$app->request->get();
        /**
         * @var UserIdentityInter $user
         */
        $user = \Yii::$app->user->identity;
        $data['open_id'] = $user->open_id;
        $EnterApplyForm->load($data,'');
        if (!$res = $EnterApplyForm->infoApply()){
            return  ResultHelper::json('422',"查询失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);

    }

    /**
     * 自定义活动提交
     * @return array|mixed
     */
    public function actionCustomEventApply() {
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_CUSTOM_EVENT_APPLY);
        $data = \Yii::$app->request->post();
        /**
         * @var UserIdentityInter $user
         */
        $user = \Yii::$app->user->identity;
        $data['member_id'] = $user->id ?? 0;
        $data['apply_content_json'] = json_decode($data['apply_content_json'],true);
        $EnterApplyForm->load($data,'');
        if (!$res = $EnterApplyForm->customEventApply()){
            return  ResultHelper::json('422',"报名失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 自定义活动详情
     * @return array|mixed
     */
    public function actionCustomEventDetail(){
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_CUSTOM_EVENT_DETAIL);
        $data = \Yii::$app->request->get();
        $EnterApplyForm->load($data,'');
        if (!$res = $EnterApplyForm->customEventDetail()){
            return  ResultHelper::json('422',"查询失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);

    }
}