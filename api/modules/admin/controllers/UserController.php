<?php


namespace api\modules\admin\controllers;

use api\modules\admin\forms\MemberAuthForm;
use api\modules\admin\forms\MiniWxUserForm;
use common\models\api\admin\MiniWxUser;
use Yii;
use common\helpers\ResultHelper;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use common\enums\StatusEnum;
use common\traits\Curd;

class UserController extends BaseController
{
    public $authMethod = [];

    /**
     * @author 陈一华
     * @note:列表数据
     */
    public function actionMiniList()
    {
        $MiniWxUserForm = new MiniWxUserForm();
        // 设置场景
        $MiniWxUserForm->setScenario($MiniWxUserForm::SCENARIO_LIST);
        // 加载数据
        $MiniWxUserForm->load(\Yii::$app->request->get(),'');
        if (!$res = $MiniWxUserForm->formList()){
            return  ResultHelper::json('422',"获取列表失败",$MiniWxUserForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * @return array|mixed
     * @note:列表数据
     */
    public function actionList()
    {
        $MemberAuthForm = new MemberAuthForm();
        // 设置场景
        $MemberAuthForm->setScenario($MemberAuthForm::SCENARIO_LIST);
        // 加载数据
        $MemberAuthForm->load(\Yii::$app->request->get(),'');
        if (!$res = $MemberAuthForm->list()){
            return  ResultHelper::json('422',"获取列表失败",$MemberAuthForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

}