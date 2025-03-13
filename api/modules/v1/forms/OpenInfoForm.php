<?php

namespace api\modules\v1\forms;

use common\models\common\OpenInfo;
use common\models\common\OpenInfoCollect;
use common\models\common\OpenInfoLike;
use yii\base\Model;

class OpenInfoForm extends Model
{
    /**
     * 公开信息列表
     */
    const SCENARIO_OPEN_INFO_LIST = 'open_info_list';

    /**
     * 公开信息详情
     */
    const SCENARIO_OPEN_INFO_DETAIL = 'open_info_detail';

    public $page = 1;

    public $page_size = 15;

    /**
     * @var string 标题
     */
    public $title;

    /**
     * @var int id
     */
    public $id;

    public function rules()
    {
        return [
            //公共参数
            [['page', 'page_size'], 'integer'],

            //公开信息列表
            [['title'],'string','on'=>[static::SCENARIO_OPEN_INFO_LIST]],

            //公开信息详情
            [['id'],'required','on'=>[static::SCENARIO_OPEN_INFO_DETAIL]],
        ];
    }

    public function list() {
        if (!$this->validate()) {
            return false;
        }

        $query = OpenInfo::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like','title',$this->title]);
        $offset = ($this->page - 1) * $this->page_size;
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;
        $res['list'] = [];
        $res['total'] = $query->count();
        $res['list'] = $query->orderBy(['sort' => SORT_DESC, 'created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($this->page_size)
            ->all();

        return $res;
    }

    public function detail() {
        OpenInfo::updateAllCounters(['views' => 1], ['id' => $this->id]);
        $item = OpenInfo::find()
            ->where(['status' => 1, 'id' => $this->id])
            ->one();

        $res = $item ? $item->toArray() : [];
        $userId = \Yii::$app->user->id;

        $like = OpenInfoLike::find()->where(['open_info_id' => $this->id, 'user_id' => $userId, 'is_like'=> 1])->one();
        $res['is_like'] = $like ? 1 : 0;
        $collect = OpenInfoCollect::find()->where(['open_info_id' => $this->id, 'user_id'=> $userId, 'is_collect'=> 1])->one();
        $res['is_collect'] = $collect ? 1 : 0;


        return $res;
    }
}
