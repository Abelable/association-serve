<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_open_info".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $cover 封面
 * @property string $content 内容
 * @property int $virtual_views 虚拟观看数
 * @property int $views 观看数
 * @property int $virtual_likes 虚拟点赞数
 * @property int $likes 点赞数
 * @property int $collects 收藏数
 * @property int $sort 权重
 * @property int $status 状态：-1-删除，0-禁用；1-启用
 * @property int $created_at
 * @property int $updated_at
 */
class OpenInfo extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_open_info';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['virtual_views', 'views', 'virtual_likes', 'likes', 'collects', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['title', 'cover'], 'string', 'max' => 255],
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
            'cover' => 'Cover',
            'content' => 'Content',
            'virtual_views' => 'Virtual Views',
            'views' => 'Views',
            'virtual_likes' => 'Virtual Likes',
            'likes' => 'Likes',
            'collects' => 'Collects',
            'sort' => 'Sort',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
