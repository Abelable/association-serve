<?php


namespace api\modules\admin\controllers;

use api\modules\admin\forms\BannerForm;
use Yii;
use common\helpers\ResultHelper;
use yii\data\ActiveDataProvider;
use common\enums\StatusEnum;
use common\traits\Curd;

class BannerController extends BaseController
{
    public $authMethod = [];

    /**
     * @return array|mixed
     * @note:列表数据
     */
    public function actionList()
    {
        $BannerForm = new BannerForm();
        // 设置场景
        $BannerForm->setScenario($BannerForm::SCENARIO_LIST);
        // 加载数据
        $BannerForm->load(\Yii::$app->request->get(),'');
        if (!$res = $BannerForm->formList()){
            return  ResultHelper::json('422',"获取列表失败",$BannerForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * @return array|mixed
     * @note:创建/修改报表列表数据
     */
    public function actionSave(){
        $BannerForm = new BannerForm();

        $fromRes = false;
        if (\Yii::$app->request->post('id',0)){
            // 设置场景
            $BannerForm->setScenario($BannerForm::SCENARIO_UPDATE);
            // 数据加载放到场景设置的后面 保证设置了safe 的属性能正常使用
            $BannerForm->load(\Yii::$app->request->post(),'');
            $fromRes = $BannerForm->formUpdate();
        }else{
            // 设置场景
            $BannerForm->setScenario($BannerForm::SCENARIO_CREATE);
            // 数据加载放到场景设置的后面 保证设置了safe 的属性能正常使用
            $BannerForm->load(\Yii::$app->request->post(),'');
            $fromRes = $BannerForm->formCreate();
        }

        if (!$fromRes){
            return  ResultHelper::json('422',"保存失败",$BannerForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }

    /**
     * 删除banner信息
     */
    public function actionDel(){
        $BannerForm = new BannerForm();
        // 设置场景
        $BannerForm->setScenario($BannerForm::SCENARIO_DEL);
        $BannerForm->load(\Yii::$app->request->post(),'');

        if (!$res = $BannerForm->formDel()){
            return  ResultHelper::json('422',"删除失败",$BannerForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }

    /**
     * @return array|mixed
     * @note:详情
     */
    public function actionDetail()
    {
        $BannerForm = new BannerForm();
        // 设置场景
        $BannerForm->setScenario($BannerForm::SCENARIO_DETAIL);
        $BannerForm->load(\Yii::$app->request->get(),'');

        if (!$res = $BannerForm->detail()){
            return  ResultHelper::json('422',"获取失败",$BannerForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }
}