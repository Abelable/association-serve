<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_album".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $date 时间
 * @property string $city_id 地区
 * @property string $photo_list 照片
 * @property int $status 状态：-1-删除，0-禁用；1-启用
 * @property int $created_at
 * @property int $updated_at
 */
class Album extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_album';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['city_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['photo_list'], 'string'],
            [['title', 'date'], 'string', 'max' => 255],
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
            'date' => 'Date',
            'city_id' => 'City ID',
            'photo_list' => 'Photo List',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
