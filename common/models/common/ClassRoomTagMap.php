<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_class_room_tag_map".
 *
 * @property int $id
 * @property int $class_room_id 课堂id
 * @property int $tag_id 标签id
 * @property int $status 状态：-1-删除，0-禁用；1-启用
 * @property int $created_at
 * @property int $updated_at
 *
 * @property ClassRoomTag $tag
 */
class ClassRoomTagMap extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_class_room_tag_map';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['class_room_id', 'tag_id', 'status', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'class_room_id' => 'Class Room ID',
            'tag_id' => 'Tag ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 获得标签
     * @return \yii\db\ActiveQuery
     */
    public function getTag() {
        return $this->hasOne(ClassRoomTag::class, ['id' => 'tag_id']);
    }
}
