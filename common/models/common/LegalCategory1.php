<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_legal_category".
 *
 * @property int $id
 * @property string $name 分类名称
 * @property string $image 配图
 * @property string $description 描述
 * @property int $sort 排序
 * @property int $status 状态：-1-删除；0-禁用；1-启用
 * @property int $created_at
 * @property int $updated_at
 */
class LegalCategory1 extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_legal_category1';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort', 'status', 'created_at', 'updated_at', 'pid'], 'integer'],
            [['name', 'image', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'image' => 'Image',
            'description' => 'Description',
            'sort' => 'Sort',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'pid' => 'Pid',
        ];
    }
}
