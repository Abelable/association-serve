<?php

namespace api\modules\v1\forms;

use common\models\common\OpenInfo;
use common\models\common\OpenInfoCollect;
use common\models\common\CustomEventForm;
use yii\base\Model;

class ActivityForm extends Model
{
    /**
     * 公开信息列表
     */
    const SCENARIO_ACTIVITY_LIST = 'activity_list';

    public $page = 1;

    public $page_size = 15;

    /**
     * @var int 分类id
     */
    public $category_id;

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
            [['category_id', 'title'], 'string', 'on'=>[static::SCENARIO_ACTIVITY_LIST]],
        ];
    }

    public function list() {
        if (!$this->validate()) {
            return false;
        }

        $query = CustomEventForm::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like','title',$this->title]);
        if ($this->category_id && $this->category_id != 0) {
            $query->andFilterWhere(['category_id' => $this->category_id]);
        }
        $offset = ($this->page - 1) * $this->page_size;
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;
        $res['list'] = [];
        $res['total'] = $query->count();
        $res['list'] = $query->orderBy(['created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($this->page_size)
            ->all();

        return $res;
    }
}
