<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_wisdom_library".
 *
 * @property int $id
 * @property string $name 人物姓名
 * @property string $field 领域
 * @property string $title 称号
 * @property string $head_img 头像
 * @property string $honor 荣誉
 * @property string $content 内容
 * @property int $sort 排序
 * @property int $status 状态：-1-删除；0-禁用；1-启用
 * @property int $created_at
 * @property int $updated_at
 */
class WisdomLibrary extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_wisdom_library';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'field', 'title', 'head_img', 'honor'], 'string', 'max' => 255],
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
            'field' => 'Field',
            'title' => 'Title',
            'head_img' => 'Head Img',
            'honor' => 'Honor',
            'content' => 'Content',
            'sort' => 'Sort',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
