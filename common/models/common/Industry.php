<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_industry".
 *
 * @property int $id
 * @property string $city_id 地区id
 * @property string $main 核心产业
 * @property string $top 头部产业
 * @property int $status 状态：-1-删除，0-禁用；1-启用
 * @property int $created_at
 * @property int $updated_at
 */
class Industry extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_industry';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['main', 'top'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'city_id' => 'City Id',
            'main' => 'Main',
            'top' => 'Top',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
