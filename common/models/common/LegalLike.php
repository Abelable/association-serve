<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_legal_like".
 *
 * @property int $id
 * @property int $legal_id 法律汇编id
 * @property int $user_id 点赞人
 * @property int $status 状态：-1-删除；0-取消点赞 1-点赞
 * @property int $created_at
 * @property int $updated_at
 */
class LegalLike extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_legal_like';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['legal_id', 'user_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['user_id'], 'required'],
            [['legal_id', 'user_id'], 'unique', 'targetAttribute' => ['legal_id', 'user_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'legal_id' => 'Legal ID',
            'user_id' => 'User ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
