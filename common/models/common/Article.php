<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_article".
 *
 * @property int $id
 * @property int $article_class_id 文章分类id
 * @property string $title 文章标题
 * @property string $img 图片url
 * @property int $sort 排序
 * @property int $virtual_like 虚拟点赞数
 * @property int $actual_like 实际点赞数
 * @property int $virtual_look 虚拟观看数
 * @property int $actual_look 实际观看数
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at
 * @property int $updated_at
 */
class Article extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_article';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['article_class_id', 'sort', 'virtual_like', 'actual_like', 'virtual_look', 'actual_look', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'img'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'article_class_id' => 'Article Class ID',
            'title' => 'Title',
            'img' => 'Img',
            'sort' => 'Sort',
            'virtual_like' => 'Virtual Like',
            'actual_like' => 'Actual Like',
            'virtual_look' => 'Virtual Look',
            'actual_look' => 'Actual Look',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getArticleClass(){
        return $this->hasOne(ArticleClass::class,['id' => 'article_class_id']);
    }
}
