<?php

namespace api\modules\v1\forms;

use common\models\common\ClassRoomApply;
use yii\base\Model;

class ClassRoomApplyForm extends Model
{
    /**
     * 课程申请新增、编辑、删除
     */
    const SCENARIO_CLASS_ROOM_APPLY_SAVE = 'class_room_apply_save';

    /**
     * @var string 课程内容
     */
    public $content;

    public function rules()
    {
        return [
            // 课程申请添加、编辑、删除
            [['content'], 'required', 'on' => [self::SCENARIO_CLASS_ROOM_APPLY_SAVE]],
            [['content'], 'string', 'on' => [self::SCENARIO_CLASS_ROOM_APPLY_SAVE]],
        ];
    }

    /**
     * 课程申请管理，添加、编辑、新增
     * @return bool
     */
    public function save() {
        if(!$this->validate()) {
            return false;
        }
        $info = new ClassRoomApply();
        $info->content = $this->content;
        $info->status = 1;
        if(!$info->save()) {
            $this->addError('open_info_save', '保存课程申请信息异常');
            return false;
        }
        return true;
    }
}
