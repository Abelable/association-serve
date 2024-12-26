<?php

namespace common\models\common;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "gm_article_like".
 *
 * @property int $id
 * @property int $member_id 用户id
 * @property int $article_id 文章id
 * @property int $is_like 是否点赞；1-否；1-是
 * @property int $created_at
 * @property int $updated_at
 */
class ArticleLike extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_article_like';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['article_id','member_id', 'is_like', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'article_id' => 'Article ID',
            'member_id' => 'Member ID',
            'is_like' => 'Is Like',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
