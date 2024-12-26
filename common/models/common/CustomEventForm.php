<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_custom_event_form".
 *
 * @property int $id
 * @property string $title 标题
 * @property int $enter_num 报名人数限制
 * @property int $registered_num 已报名人数
 * @property string $enter_from_json 报名表单的信息数据 使用json格式
 * @property int $start_time 开始时间
 * @property int $end_time 结束时间
 * @property int $status 状态：-1-删除；1-启用；0-禁用
 * @property string $remark 备注
 * @property int $created_at
 * @property int $updated_at
 */
class CustomEventForm extends \common\models\base\BaseModel
{
    //进行中
    const ACTIVE = 1;
    //未开始
    const INACTIVE = 0;
    //已结束
    const END = 2;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_custom_event_form';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enter_num', 'registered_num', 'start_time', 'end_time', 'status', 'created_at', 'updated_at'], 'integer'],
            [['enter_from_json'], 'required'],
            [['enter_from_json'], 'string'],
            [['title', 'remark'], 'string', 'max' => 1255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'enter_num' => 'Enter Num',
            'registered_num' => 'Registered Num',
            'enter_from_json' => 'Enter From Json',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'status' => 'Status',
            'remark' => 'Remark',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
