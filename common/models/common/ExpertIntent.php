<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "tbb_expert_intent".
 *
 * @property int $id
 * @property string $title 意向
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at
 * @property int $updated_at
 */
class ExpertIntent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_expert_intent';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['title'], 'string', 'max' => 255],
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
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
