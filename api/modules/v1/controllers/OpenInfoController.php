<?php

namespace api\modules\v1\controllers;

use api\controllers\OnAuthController;
use api\models\UserIdentityInter;
use api\modules\v1\forms\OpenInfoForm;
use common\helpers\ResultHelper;
use common\models\common\OpenInfo;
use common\models\common\OpenInfoCollect;
use common\models\common\OpenInfoLike;

class OpenInfoController extends OnAuthController
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
        $form = new OpenInfoForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_OPEN_INFO_LIST);
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

    public function actionDetail() {
        $form = new OpenInfoForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_OPEN_INFO_DETAIL);
        $data = \Yii::$app->request->get();
        /**
         * @var UserIdentityInter $user
         */
        /*$user = \Yii::$app->user->identity;
        $data['open_id'] = $user->open_id;*/
        $form->load($data,'');
        if (!$res = $form->detail()){
            return  ResultHelper::json('422',"查询失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    public function actionLike(){
        $data = \Yii::$app->request->post();
        $userId = \Yii::$app->user->identity->id;

        $model = OpenInfo::findOne(['id' => $data['id']]);
        if(!$model) {
            return  ResultHelper::json('422',"failed","公开信息不存在");
        }

        $like = OpenInfoLike::findOne(['open_info_id' => $data['id'], 'user_id' => $userId]);
        if(!$like) {
            $like = new OpenInfoLike();
            $status = 0;
            $likeNum = 1;
        }else{
            $status = $like->is_like;
            $likeNum = $like->is_like== 1 ? -1 : 1;
        }
        if($data['action'] == $status) {
            return  ResultHelper::json('422',"failed","异常操作");
        }

        $like->open_info_id = $data['id'];
        $like->user_id = $userId;
        $like->is_like = $data['action'];
        $tran = \Yii::$app->db->beginTransaction();
        try {
            if(!$like->save()) {
                throw new \Exception('更新点赞记录失败');
            }
            //更新点赞数
            $res = OpenInfo::updateAllCounters(['likes' => $likeNum], ['id' => $data['id']]);
            if(!$res) {
                throw new \Exception('更新公开信息点赞数失败');
            }
            $tran->commit();
        }catch (\Exception $e) {
            $tran->rollBack();
            return  ResultHelper::json('422',"failed",$e->getMessage());
        }
        return  ResultHelper::json('200',"success");
    }

    public function actionCollect(){
        $data = \Yii::$app->request->post();
        $userId = \Yii::$app->user->identity->id;

        $model = OpenInfo::findOne(['id' => $data['id']]);
        if(!$model) {
            return  ResultHelper::json('422',"failed","公开信息不存在");
        }

        $collect = OpenInfoCollect::findOne(['open_info_id' => $data['id'],'user_id' => $userId]);
        if(!$collect) {
            $collect = new OpenInfoCollect();
            $status = 0;
            $collectNum = 1;
        }else{
            $status = $collect->is_collect;
            $collectNum = $collect->is_collect== 1 ? -1 : 1;
        }
        if($data['action'] == $status) {
            return  ResultHelper::json('422',"failed","异常操作");
        }

        $collect->open_info_id = $data['id'];
        $collect->user_id = $userId;
        $collect->is_collect = $data['action'];
        $tran = \Yii::$app->db->beginTransaction();
        try {
            if(!$collect->save()) {
                throw new \Exception('更新收藏记录失败');
            }
            //更新收藏数
            $res = OpenInfo::updateAllCounters(['collects' => $collectNum], ['id' => $data['id']]);
            if(!$res) {
                throw new \Exception('更新收藏数失败');
            }
            $tran->commit();
        }catch (\Exception $e) {
            $tran->rollBack();
            return  ResultHelper::json('422',"failed",$e->getMessage());
        }
        return  ResultHelper::json('200',"success");
    }

    public function actionCollectList() {
        $data = \Yii::$app->request->get();
        $userId = \Yii::$app->user->identity->id;
        $pageSize = $data['page_size'] ? $data['page_size'] : 10;
        $page = $data['page'] ? $data['page'] : 1;

        $query = OpenInfoCollect::find()
            ->where(['is_collect' => 1])
            ->andFilterWhere(['user_id' => $userId]);

        $total = $query->count();
        $data = [
            'total' => 0,
            'list' => []
        ];
        if ($total == 0) {
        return  ResultHelper::json('200',"success", $data);
        }
        $data['total'] = $total;
        $offset = ($page - 1) * $pageSize;
        $list = $query->orderBy(['created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($pageSize)
            ->asArray()
            ->all();

        foreach ($list as $item) {
            $data['list'][] = OpenInfo::findOne(['id' => $item['open_info_id']]);
        }

        return  ResultHelper::json('200',"success", $data);
    }
}
