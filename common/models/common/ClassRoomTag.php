<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_class_room_tag".
 *
 * @property int $id
 * @property string $tag_name 标签名称
 * @property int $use_num 标签使用次数
 * @property int $created_at
 * @property int $updated_at
 */
class ClassRoomTag extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_class_room_tag';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['use_num', 'created_at', 'updated_at'], 'integer'],
            [['tag_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'tag_name' => 'Tag Name',
            'use_num' => 'Use Num',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
