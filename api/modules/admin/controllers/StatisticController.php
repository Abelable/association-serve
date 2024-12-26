<?php


namespace api\modules\admin\controllers;

use common\helpers\ResultHelper;
use common\models\common\Statistic;
use stdClass;

class StatisticController extends BaseController
{
    public $authMethod = [];

    public function actionSave() {
        $data = \Yii::$app->request->post();

        if ($data['id']) {
            $model = Statistic::findOne($data['id']);
        }else{
            $model = new Statistic();
        }
        $model->name= $data['name'];
        $model->num = $data['num'];
        $model->type = $data['type'];
        $model->rate = $data['rate'];
        $model->place = $data['place'];
        $model->save();

        return  ResultHelper::json('200',"success", []);
    }

    public function actionList() {

        $query = Statistic::find();
        $list = $query->asArray()->all();
        return  ResultHelper::json('200',"success", [
            'list' => $list,
        ]);
    }

    public function actionDelete() {
        $data = \Yii::$app->request->post();
        Statistic::deleteAll(['in','id',$data['ids']]);
        return  ResultHelper::json('200',"success", []);
    }

    public function actionDetail(){
        $data = \Yii::$app->request->get();
        $model = Statistic::findOne($data['id']);
        $model = $model ? $model->toArray() : new stdClass();
        return  ResultHelper::json('200',"success", $model);
    }


}
