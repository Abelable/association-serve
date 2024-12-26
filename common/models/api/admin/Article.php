<?php

namespace common\models\api\admin;

use Yii;

/**
 * This is the model class for table "{{%article}}".
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
 * @property int $content   文章内容
 *
 * @property int $showLike  显示的点赞数
 * @property int $showLook  显示的观看数
 *
 * @property ArticleClass|null $articleClass 分类信息
 */
class Article extends \common\models\base\BaseModel
{


    /**
     * 有效的状态
     */
    const STATUS_ACTIVE = 1;

    /**
     * 禁用状态
     */
    const STATUS_INACTIVE = 0;

    /**
     * 删除状态
     */
    const STATUS_DELETE = -1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%article}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['article_class_id', 'sort', 'virtual_like', 'actual_like', 'virtual_look', 'actual_look', 'status'], 'integer'],
            [['title', 'img'], 'string', 'max' => 255],
            [['content'], 'string'],
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
            'content'=>'content',
        ];
    }


    /**
     * 显示的点赞数
     */
    public function getShowLike(){
        return $this->virtual_like + $this->actual_like;
    }

    /**
     * 显示观看数
     */
    public function getShowLook(){
        return $this->virtual_look + $this->actual_look;
    }

    /**
     * 管理分类信息
     * @return \yii\db\ActiveQuery
     */
    public function getArticleClass(){
        return $this->hasOne(ArticleClass::class,['id'=>'article_class_id']);
    }
}
