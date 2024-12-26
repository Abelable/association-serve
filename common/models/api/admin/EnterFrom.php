<?php

namespace common\models\api\admin;

use Yii;

/**
 * This is the model class for table "{{%enter_from}}".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $enter_from_json 报名表单的信息数据 使用json格式 为了导出定位数据的组件  每一个组件必须配备一个唯一的识别码  _id  例如 [{“_id”:”1111”}]
 * @property int $enter_num 报名人数
 * @property string $mark 报名备注
 * @property int $created_at
 * @property int $updated_at
 * @property int $status 当前状态  0表示结束  1表示使用中
 */
class EnterFrom extends \common\models\base\BaseModel
{

    /**
     * 有效的状态
     */
    const STATUS_ACTIVE = 1;

    /**
     * 禁用状态
     */
    const STATUS_INACTIVE = 0;



    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%enter_from}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'enter_from_json', 'enter_num', 'mark', 'status'], 'required'],
            [['enter_from_json', 'mark'], 'string'],
            [['enter_num', 'created_at', 'updated_at', 'status'], 'integer'],
            [['title'], 'string', 'max' => 255],
            ['status', 'in','range' =>[static::STATUS_ACTIVE,static::STATUS_INACTIVE]],
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
            'enter_from_json' => 'Enter From Json',
            'enter_num' => 'Enter Num',
            'mark' => 'Mark',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'status' => 'Status',
        ];
    }
}
