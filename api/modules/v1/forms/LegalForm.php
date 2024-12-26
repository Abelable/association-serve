<?php


namespace api\modules\v1\forms;


use common\helpers\ArrayHelper;
use common\models\common\Legal;
use common\models\common\Legal1;
use common\models\common\LegalCategory;
use common\models\common\LegalCategory1;
use common\models\common\LegalCollect;
use common\models\common\LegalLike;
use yii\base\Model;

class LegalForm extends Model
{
    /**
     * 法律汇编列表
     */
    const SCENARIO_LEGAL_LIST = 'legal_list';

    /**
     * 法律汇编列表
     */
    const SCENARIO_LEGAL_DETAIL = 'legal_detail';

    /**
     * 法律汇编列表
     */
    const SCENARIO_LEGAL_LIKE = 'legal_like';

    /**
     * 法律汇编分类列表
     */
    const SCENARIO_LEGAL_CATEGORY_LIST = 'legal_category_list';

    public $page = 1;

    public $page_size = 15;

    /**
     * @var string 标题
     */
    public $title;

    /**
     * @var int 分类id
     */
    public $category_id;
    public $sub_category_id;

    /**
     * @var int id
     */
    public $id;

    /**
     * @var int 用户id
     */
    public $user_id;

    /**
     * @var int 操作：1-点赞 0-取消点赞
     */
    public $action = 1;

    public function rules()
    {
        return [
            //公共参数
            [['page', 'page_size'], 'integer', 'on' => [static::SCENARIO_LEGAL_LIST, static::SCENARIO_LEGAL_CATEGORY_LIST]],

            //列表
            [['title','category_id', 'sub_category_id'],'string','on'=>[static::SCENARIO_LEGAL_LIST]],

            //详情
            [['id', 'user_id'],'required','on'=>[static::SCENARIO_LEGAL_DETAIL]],

            //点赞
            [['id', 'user_id', 'action'],'required','on'=>[static::SCENARIO_LEGAL_LIKE]],
        ];
    }

    /**
     * 法律汇编列表
     * @return array|false|\yii\db\ActiveRecord[]
     */
    public function list() {
        if (!$this->validate()) {
            return false;
        }

        $query = Legal::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like','title',$this->title])
            ->andFilterWhere(['category_id' => $this->category_id]);
        $offset = ($this->page - 1) * $this->page_size;
        $list = $query->orderBy(['sort' => SORT_DESC, 'created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($this->page_size)
            ->all();

        return $list;
    }

    public function detail() {
        if (!$this->validate()) {
            return false;
        }

        $model = Legal::findOne(['id' => $this->id, 'status' => 1]);
        if(!$model) {
            $this->addError('detail', '法律汇编不存在');
            return false;
        }
        //观看数+1
        Legal::updateAllCounters(['views' => 1], ['id' => $this->id]);

        $legal = Legal::findOne(['id' => $this->id]);
        $data = ArrayHelper::toArray($legal);
        $like = LegalLike::findOne(['legal_id' => $this->id, 'user_id' => $this->user_id, 'status' => 1]);
        $data['is_like'] = $like? 1 : 0;
        return $data;
    }

    /**
     * 点赞、取消点赞
     * @return bool
     */
    public function like() {
        if (!$this->validate()) {
            return false;
        }

        $model = Legal::findOne(['id' => $this->id, 'status' => 1]);
        if(!$model) {
            $this->addError('like', '法律汇编不存在');
            return false;
        }

        $like = LegalLike::findOne(['legal_id' => $this->id,'user_id' => $this->user_id]);
        if(!$like) {
            $like = new LegalLike();
            $status = 0;
            $likeNum = 1;
        }else{
            $status = $like->status;
            $likeNum = $like->status == 1 ? -1 : 1;
        }
        if($this->action == $status) {
            $this->addError('like', '异常操作');
            return false;
        }

        $like->legal_id = $this->id;
        $like->user_id = $this->user_id;
        $like->status = $this->action;
        $tran = \Yii::$app->db->beginTransaction();
        try {
            if(!$like->save()) {
                throw new \Exception('更新点赞记录失败');
            }
            //更新点赞数
            $res = Legal::updateAllCounters(['likes' => $likeNum], ['id' => $this->id]);
            if(!$res) {
                throw new \Exception('更新法律汇编点赞数失败');
            }
            $tran->commit();
            return true;
        }catch (\Exception $e) {
            $tran->rollBack();
            $this->addError('like', $e->getMessage());
            return false;
        }

    }

    /**
     * 点赞、取消收藏
     * @return bool
     */
    public function collect() {
        if (!$this->validate()) {
            return false;
        }

        $model = Legal::findOne(['id' => $this->id, 'status' => 1]);
        if(!$model) {
            $this->addError('like', '法律汇编不存在');
            return false;
        }

        $like = LegalCollect::findOne(['legal_id' => $this->id,'user_id' => $this->user_id]);
        if(!$like) {
            $like = new LegalCollect();
            $status = 0;
            $likeNum = 1;
        }else{
            $status = $like->status;
            $likeNum = $like->status == 1 ? -1 : 1;
        }
        if($this->action == $status) {
            $this->addError('like', '异常操作');
            return false;
        }

        $like->legal_id = $this->id;
        $like->user_id = $this->user_id;
        $like->status = $this->action;
        $tran = \Yii::$app->db->beginTransaction();
        try {
            if(!$like->save()) {
                throw new \Exception('更新收藏记录失败');
            }
            //更新收藏数
            $res = Legal::updateAllCounters(['collects' => $likeNum], ['id' => $this->id]);
            if(!$res) {
                throw new \Exception('更新法律汇编收藏数失败');
            }
            $tran->commit();
            return true;
        }catch (\Exception $e) {
            $tran->rollBack();
            $this->addError('collect', $e->getMessage());
            return false;
        }
    }

    /**
     * 收藏法律汇编列表
     * @return array|false|\yii\db\ActiveRecord[]
     */
    public function collectList() {
        $query = LegalCollect::find()
            ->where(['status' => 1])
            ->andFilterWhere(['user_id' => $this->user_id]);

        $total = $query->count();
        $data = [
            'total' => 0,
            'list' => []
        ];
        if ($total == 0) {
            return $data;
        }
        $data['total'] = $total;
        $offset = ($this->page - 1) * $this->page_size;
        $list = $query->orderBy(['sort' => SORT_DESC, 'created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($this->page_size)
            ->asArray()
            ->all();

        foreach ($list as $item) {
            $data['list'][] = Legal::findOne(['id' => $item['legal_id']]);
        }

        return $list;
    }



    /**
     * 法律汇编分类列表
     * @return array|false|\yii\db\ActiveRecord[]
     */
    public function categoryList() {
        if (!$this->validate()) {
            return false;
        }

        $query = LegalCategory::find()
            ->where(['status' => 1]);
        $offset = ($this->page - 1) * $this->page_size;
        $list = $query->orderBy(['sort' => SORT_DESC, 'created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($this->page_size)
            ->all();

        return $list;
    }

    //=====================================================================================
    /**
     * 法律汇编列表
     * @return array|false|\yii\db\ActiveRecord[]
     */
    public function list1() {
        if (!$this->validate()) {
            return false;
        }

        $query = Legal1::find()
            ->where(['status' => 1]);
        if ($this->title) {
            $query = $query->andFilterWhere(['like','title',$this->title]);
        }
        if ($this->category_id) {
            $query = $query->andFilterWhere(['category_id' => $this->category_id]);
        }
        if ($this->sub_category_id) {
            $query = $query->andFilterWhere(['sub_category_id' => $this->sub_category_id]);
        }
        $keywords= $_GET['keywords']??'';
        if ($keywords) {
            $query = $query->andFilterWhere(['like','content',$_GET['keywords']]);
        }
        $offset = ($this->page - 1) * $this->page_size;
        $list = $query->orderBy(['sort' => SORT_DESC, 'created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($this->page_size)
            ->all();

        return $list;
    }

    public function detail1() {
        if (!$this->validate()) {
            return false;
        }

        $model = Legal1::findOne(['id' => $this->id, 'status' => 1]);
        if(!$model) {
            $this->addError('detail', '法律汇编不存在');
            return false;
        }
        //观看数+1
        Legal1::updateAllCounters(['views' => 1], ['id' => $this->id]);

        $legal = Legal1::findOne(['id' => $this->id]);
        $data = ArrayHelper::toArray($legal);
        $like = LegalLike::findOne(['legal_id' => $this->id, 'user_id' => $this->user_id, 'status' => 1]);
        $data['is_like'] = $like? 1 : 0;
        $collect = LegalCollect::findOne(['legal_id' => $this->id, 'user_id' => $this->user_id, 'is_collect' => 1]);
        $data['is_collect'] = $collect? 1 : 0;

        return $data;
    }

    /**
     * 点赞、取消点赞
     * @return bool
     */
    public function like1() {
        if (!$this->validate()) {
            return false;
        }

        $model = Legal1::findOne(['id' => $this->id, 'status' => 1]);
        if(!$model) {
            $this->addError('like', '法律汇编不存在');
            return false;
        }

        $like = LegalLike::findOne(['legal_id' => $this->id,'user_id' => $this->user_id]);
        if(!$like) {
            $like = new LegalLike();
            $status = 0;
            $likeNum = 1;
        }else{
            $status = $like->status;
            $likeNum = $like->status == 1 ? -1 : 1;
        }
        if($this->action == $status) {
            $this->addError('like', '异常操作');
            return false;
        }

        $like->legal_id = $this->id;
        $like->user_id = $this->user_id;
        $like->status = $this->action;
        $tran = \Yii::$app->db->beginTransaction();
        try {
            if(!$like->save()) {
                throw new \Exception('更新点赞记录失败');
            }
            //更新点赞数
            $res = Legal1::updateAllCounters(['likes' => $likeNum], ['id' => $this->id]);
            if(!$res) {
                throw new \Exception('更新法律汇编点赞数失败');
            }
            $tran->commit();
            return true;
        }catch (\Exception $e) {
            $tran->rollBack();
            $this->addError('like', $e->getMessage());
            return false;
        }

    }

    /**
     * 点赞、取消收藏
     * @return bool
     */
    public function collect1() {
        if (!$this->validate()) {
            return false;
        }

        $model = Legal1::findOne(['id' => $this->id, 'status' => 1]);
        if(!$model) {
            $this->addError('like', '法律汇编不存在');
            return false;
        }

        $like = LegalCollect::findOne(['legal_id' => $this->id,'user_id' => $this->user_id]);
        if(!$like) {
            $like = new LegalCollect();
            $status = 0;
            $likeNum = 1;
        }else{
            $status = $like->is_collect;
            $likeNum = $like->is_collect== 1 ? -1 : 1;
        }
        if($this->action == $status) {
            $this->addError('like', '异常操作');
            return false;
        }

        $like->legal_id = $this->id;
        $like->user_id = $this->user_id;
        $like->is_collect = $this->action;
        $tran = \Yii::$app->db->beginTransaction();
        try {
            if(!$like->save()) {
                throw new \Exception('更新收藏记录失败');
            }
            //更新收藏数
            $res = Legal1::updateAllCounters(['collects' => $likeNum], ['id' => $this->id]);
            if(!$res) {
                throw new \Exception('更新法律汇编收藏数失败');
            }
            $tran->commit();
            return true;
        }catch (\Exception $e) {
            $tran->rollBack();
            $this->addError('collect', $e->getMessage());
            return false;
        }
    }

    /**
     * 收藏法律汇编列表
     * @return array|false|\yii\db\ActiveRecord[]
     */
    public function collectList1() {
        $query = LegalCollect::find()
            ->where(['is_collect' => 1])
            ->andFilterWhere(['user_id' => $this->user_id]);
        // 半个月了才被通知有UI  上面的功能没有
        if ($_GET['keywords']) {
            $legals = Legal1::find()->where(['and', ['like', 'content', $_GET['keywords']]])->asArray()->all();
            $ids = [];
            if ($legals) {
                $ids = array_column($legals, 'id');
            }else{
                $ids = [0];
            }
            $query->andFilterWhere(['in', 'legal_id', $ids]);
        }

        $total = $query->count();
        $data = [
            'total' => 0,
            'list' => []
        ];
        if ($total == 0) {
            return $data;
        }
        $data['total'] = $total;
        $offset = ($this->page - 1) * $this->page_size;
        $list = $query->orderBy(['created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($this->page_size)
            ->asArray()
            ->all();

        foreach ($list as $item) {
            $data['list'][] = Legal1::findOne(['id' => $item['legal_id']]);
        }

        return $data;
    }

    /**
     * 法律汇编分类列表
     * @return array|false|\yii\db\ActiveRecord[]
     */
    public function categoryList1() {
        if (!$this->validate()) {
            return false;
        }

        $pid = $_GET['pid'] ? $_GET['pid'] : 0;
        $query = LegalCategory1::find()
            ->where(['status' => 1, 'pid' => $pid]);
        $offset = ($this->page - 1) * $this->page_size;
        $list = $query->orderBy(['sort' => SORT_DESC, 'created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($this->page_size)
            ->all();

        return $list;
    }
}
