<?php

namespace api\modules\admin\forms;

use common\models\common\ClassRoomApply;
use yii\base\Model;

class ClassRoomApplyForm extends Model
{
    /**
     * 课程申请列表
     */
    const SCENARIO_CLASS_ROOM_APPLY_LIST = 'class_room_apply_list';

    /**
     * @var string 课程内容
     */
    public $content;

    public $page = 0;

    public $page_size = 10;

    public function rules()
    {
        return [
            // 公共参数
            [['page','page_size'],'integer'],

            // 课程申请列表
            [['content'], 'integer', 'on' => [self::SCENARIO_CLASS_ROOM_APPLY_LIST]],
        ];
    }

    public function list() {
        if (!$this->validate()){
            return false;
        }

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $offset = ($this->page - 1) * $this->page_size;
        $query = ClassRoomApply::find()->where(['status' => 1]);
        $list = $query->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();
        $res['total'] = $query->count();

        $res['list'] = $list;
        return $res;
    }
}