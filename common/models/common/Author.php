<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_author".
 *
 * @property int $id
 * @property string $author_name 作者名称
 * @property string $head_img 作者头像
 * @property int $status 状态：-1-删除；0-禁用；1-启用
 * @property int $created_at
 * @property int $updated_at
 */
class Author extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_author';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['author_name', 'head_img'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'author_name' => 'Author Name',
            'head_img' => 'Head Img',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
