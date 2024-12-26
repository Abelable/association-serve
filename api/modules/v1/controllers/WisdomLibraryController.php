<?php


namespace api\modules\v1\controllers;


use api\controllers\OnAuthController;
use api\models\UserIdentityInter;
use api\modules\v1\forms\WisdomLibraryForm;
use common\helpers\ResultHelper;
use common\models\common\WisdomLibrary;
use common\models\common\WisdomLibraryCollect;
use common\models\common\WisdomLibraryLike;

class WisdomLibraryController extends OnAuthController
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
        $form = new WisdomLibraryForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_WISDOM_LIBRARY_LIST);
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
        $form = new WisdomLibraryForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_WISDOM_LIBRARY_DETAIL);
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

        $model = WisdomLibrary::findOne(['id' => $data['id']]);
        if(!$model) {
            return  ResultHelper::json('422',"failed","智库不存在");
        }

        $like = WisdomLibraryLike::findOne(['wisdom_library_id' => $data['id'], 'user_id' => $userId]);
        if(!$like) {
            $like = new WisdomLibraryLike();
            $status = 0;
            $likeNum = 1;
        }else{
            $status = $like->is_like;
            $likeNum = $like->is_like== 1 ? -1 : 1;
        }
        if($data['action'] == $status) {
            return  ResultHelper::json('422',"failed","异常操作");
        }

        $like->wisdom_library_id = $data['id'];
        $like->user_id = $userId;
        $like->is_like = $data['action'];
        $tran = \Yii::$app->db->beginTransaction();
        try {
            if(!$like->save()) {
                throw new \Exception('更新点赞记录失败');
            }
            //更新收藏数
            $res = WisdomLibrary::updateAllCounters(['likes' => $likeNum], ['id' => $data['id']]);
            if(!$res) {
                throw new \Exception('更新智库点赞失败');
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

        $model = WisdomLibrary::findOne(['id' => $data['id']]);
        if(!$model) {
            return  ResultHelper::json('422',"failed","课堂不存在");
        }

        $collect = WisdomLibraryCollect::findOne(['wisdom_library_id' => $data['id'],'user_id' => $userId]);
        if(!$collect) {
            $collect = new WisdomLibraryCollect();
            $status = 0;
            $collectNum = 1;
        }else{
            $status = $collect->is_collect;
            $collectNum = $collect->is_collect== 1 ? -1 : 1;
        }
        if($data['action'] == $status) {
            return  ResultHelper::json('422',"failed","异常操作");
        }

        $collect->wisdom_library_id = $data['id'];
        $collect->user_id = $userId;
        $collect->is_collect = $data['action'];
        $tran = \Yii::$app->db->beginTransaction();
        try {
            if(!$collect->save()) {
                throw new \Exception('更新收藏记录失败');
            }
            //更新收藏数
            $res = WisdomLibrary::updateAllCounters(['collects' => $collectNum], ['id' => $data['id']]);
            if(!$res) {
                throw new \Exception('更新收藏点赞失败');
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

        $query = WisdomLibraryCollect::find()
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
            $data['list'][] = WisdomLibrary::findOne(['id' => $item['wisdom_library_id']]);
        }

        return  ResultHelper::json('200',"success", $data);
    }
}
