<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_legal".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $image 图片
 * @property int $category_id 分类id
 * @property string $content 内容
 * @property int $virtual_views 虚拟点赞数
 * @property int $views 观看数
 * @property int $virtual_likes 虚拟点赞数
 * @property int $likes 点赞数
 * @property int $effective_time 生效时间
 * @property int $promulgation_time 颁布时间
 * @property int $sort 排序
 * @property int $status 状态：-1-删除；0-禁用；1-启用
 * @property int $created_at
 * @property int $updated_at
 *
 * @property LegalCategory $category 法律汇编分类
 */
class Dynamic extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_dynamic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'virtual_views', 'views', 'virtual_likes', 'likes', 'effective_time', 'promulgation_time', 'sort', 'status', 'created_at', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['title', 'image'], 'string', 'max' => 255],
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
            'image' => 'Image',
            'category_id' => 'Category ID',
            'content' => 'Content',
            'virtual_views' => 'Virtual Views',
            'views' => 'Views',
            'virtual_likes' => 'Virtual Likes',
            'likes' => 'Likes',
            'effective_time' => 'Effective Time',
            'promulgation_time' => 'Promulgation Time',
            'sort' => 'Sort',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

//    /**
//     * 获取分类
//     * @return \yii\db\ActiveQuery
//     */
//    public function getCategory(){
//        return $this->hasOne(LegalCategory::class, ['id' => 'category_id']);
//    }
}