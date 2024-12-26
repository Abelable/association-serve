<?php


namespace api\modules\admin\controllers;


use api\modules\admin\forms\LegalForm;
use common\helpers\ResultHelper;
use common\models\common\LiveMonitor;
use stdClass;

class LiveMonitorController extends BaseController
{
    public $authMethod = [];


    // 直播监控列表 临时加的
    public function actionList(){
        $page = \Yii::$app->request->get('page',1);
        $pageSize = \Yii::$app->request->get('pageSize',10);

        $list = LiveMonitor::find()->orderBy('id desc')->limit($pageSize)->offset(($page-1)*$pageSize)->asArray()->all();
        $total = LiveMonitor::find()->count();

        return ResultHelper::json(200,'success', [
            'list' => $list,
            'total' => $total
        ]);
    }

    public function actionDelete(){
        $id = \Yii::$app->request->post('id');
        LiveMonitor::deleteAll(['id'=>$id]);
        return ResultHelper::json(200,'删除成功');
    }

    public function actionSave(){
        $data = \Yii::$app->request->post();
        $model = new LiveMonitor();
        if (isset($data['id']) && $data['id']){
            $model = LiveMonitor::findOne($data['id']);
        }
        $model->cover = $data['cover'];
        $model->title = $data['title'];
        $model->replay_url = $data['replay_url'];
        $model->address = $data['address'];
        $model->company_name = $data['company_name'];
        $model->platform = $data['platform'];
        $model->sort = $data['sort'] ?? 0;
        $model->save();

        return ResultHelper::json(200,'添加成功');
    }

    public function actionDetail(){
        $id = \Yii::$app->request->post('id');
        $model = LiveMonitor::findOne($id);
        $model = $model  ? $model : new stdClass();
        return ResultHelper::json(200,'success', $model);
    }

}
