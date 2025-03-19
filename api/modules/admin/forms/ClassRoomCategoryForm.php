<?php

namespace api\modules\admin\forms;

use common\models\common\ClassRoomCategory;
use yii\base\Model;

class ClassRoomCategoryForm extends Model
{
    /**
     * 分类新增、编辑、删除
     */
    const SCENARIO_CLASS_ROOM_CATEGORY_SAVE = 'class_room_category_save';

    /**
     * 分类列表
     */
    const SCENARIO_CLASS_ROOM_CATEGORY_LIST = 'class_room_category_list';

    /**
     * 分类列表
     */
    const SCENARIO_CLASS_ROOM_CATEGORY_OPTIONS = 'class_room_category_options';

    /**
     * @var string 名称
     */
    public $name;

    /**
     * @var int 排序
     */
    public $sort = 0;

    /**
     * @var int id
     */
    public $id;

    /**
     * @var int 状态
     */
    public $status = 1;

    public $page = 0;

    public $page_size = 15;

    public function rules()
    {
        return [
            // 公共参数
            [['page','page_size'],'integer'],

            // 分类添加、编辑、删除
            [['name', 'sort'], 'required', 'on' => [self::SCENARIO_CLASS_ROOM_CATEGORY_SAVE]],
            [['id','status','sort'], 'integer', 'on' => [self::SCENARIO_CLASS_ROOM_CATEGORY_SAVE]],
            ['status', 'in', 'range' => [-1,0,1], 'on' => [self::SCENARIO_CLASS_ROOM_CATEGORY_SAVE]],

            // 分类列表
            [['name'], 'string', 'on' => [self::SCENARIO_CLASS_ROOM_CATEGORY_LIST]],

            // 分类选项
            [['name'], 'string', 'on' => [self::SCENARIO_CLASS_ROOM_CATEGORY_OPTIONS]],
        ];
    }


    /**
     * 分类管理，添加、编辑、新增
     * @return bool
     */
    public function categorySave() {
        if(!$this->validate()) {
            return false;
        }
        $info = ClassRoomCategory::findOne(['id' => $this->id]);
        if(!$info) {
            $info = new ClassRoomCategory();
        }
        $info->name = $this->name;
        $info->sort = $this->sort;
        $info->status = $this->status;
        if(!$info->save()) {
            $this->addError('class_room_category_save', '保存分类异常');
            return false;
        }

        return true;
    }

    public function categoryList() {
        if (!$this->validate()){
            return false;
        }

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $offset = ($this->page - 1) * $this->page_size;
        $query = ClassRoomCategory::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like', 'name', $this->name]);
        $list = $query->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['sort' => SORT_DESC,'id' => SORT_DESC])
            ->asArray()
            ->all();
        $res['total'] = $query->count();

        $res['list'] = $list;
        return $res;
    }

    public function categoryOptions() {
        if (!$this->validate()){
            return false;
        }
        return ClassRoomCategory::find()
            ->where(['status' => 1])
            ->orderBy(['sort' => SORT_DESC,'id' => SORT_DESC])
            ->all();
    }
}