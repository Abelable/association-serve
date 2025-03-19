<?php

namespace api\modules\v1\forms;

use common\models\common\ClassRoomCategory;
use yii\base\Model;

class ClassRoomCategoryForm extends Model
{
    /**
     * 分类列表
     */
    const SCENARIO_CLASS_ROOM_CATEGORY_LIST = 'class_room_category_list';

    public $page = 1;

    public $page_size = 15;

    /**
     * @var string 名称
     */
    public $name;

    /**
     * @var int id
     */
    public $id;

    public function rules()
    {
        return [
            // 分类列表
            [['name'], 'string', 'on'=>[static::SCENARIO_CLASS_ROOM_CATEGORY_LIST]],
        ];
    }

    public function list() {
        if (!$this->validate()) {
            return false;
        }

        return ClassRoomCategory::find()
            ->where(['status' => 1])
            ->orderBy(['sort' => SORT_DESC, 'created_at' => SORT_DESC])
            ->all();
    }
}
