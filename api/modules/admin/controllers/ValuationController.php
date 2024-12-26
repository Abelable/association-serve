<?php


namespace api\modules\admin\controllers;

use common\helpers\ResultHelper;
use common\models\common\LegalCategory;
use common\models\common\Valuation;
use stdClass;

class ValuationController extends BaseController
{
    public $authMethod = [];

    public function actionSave() {
        $data = \Yii::$app->request->post();

        if (isset($data['id'])) {
            $model = Valuation::findOne($data['id']);
        }else{
            $model = new Valuation();
        }
        $model->year = $data['year']??'';
        $model->name = $data['name'];
        $model->sort = $data['sort']?? $data['name'];
        $model->num = $data['num'];
        $model->save();

        return  ResultHelper::json('200',"success", []);
    }

    public function actionList() {
        $query = Valuation::find();
        $list = $query->orderBy(['year' => SORT_DESC,'id' => SORT_ASC])->asArray()->all();
        return  ResultHelper::json('200',"success", [
            'list' => $list,
        ]);
    }

    public function actionDelete() {
        $data = \Yii::$app->request->post();
        Valuation::deleteAll(['in','id',$data['ids']]);
        return  ResultHelper::json('200',"success", []);
    }

    public function actionDetail(){
        $data = \Yii::$app->request->get();
        $model = Valuation::findOne($data['id']);
        $model = $model ? $model->toArray() : new stdClass();
        return  ResultHelper::json('200',"success", $model);
    }


}
