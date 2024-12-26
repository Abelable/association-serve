<?php


namespace api\modules\admin\controllers;

use common\helpers\ResultHelper;
use common\models\common\LegalCategory;
use common\models\common\Stock;
use common\models\common\Valuation;
use stdClass;

class StockController extends BaseController
{
    public $authMethod = [];

    public function actionSave() {
        $data = \Yii::$app->request->post();

        if ($data['id']) {
            $model = Stock::findOne($data['id']);
        }else{
            $model = new Stock();
        }
        $model->type = $data['type'];
        $model->in_province = $data['in_province'];
        $model->out_province = $data['out_province'];
        $model->international = $data['international'];
        $model->save();

        return  ResultHelper::json('200',"success", []);
    }

    public function actionList() {

        $query = Stock::find();
        $list = $query->asArray()->all();
        return  ResultHelper::json('200',"success", [
            'list' => $list,
        ]);
    }

    public function actionDelete() {
        $data = \Yii::$app->request->post();
        Stock::deleteAll(['in','id',$data['ids']]);
        return  ResultHelper::json('200',"success", []);
    }

    public function actionDetail(){
        $data = \Yii::$app->request->get();
        $model = Stock::findOne($data['id']);
        $model = $model ? $model->toArray() : new stdClass();
        return  ResultHelper::json('200',"success", $model);
    }


}
