<?php

namespace common\models\common;

use common\models\base\BaseModel;
use Yii;

/**
 * This is the model class for table "gm_article_look".
 *
 * @property int $id
 * @property int $member_id 用户id
 * @property int $article_id 观看文章id
 * @property int $created_at
 * @property int $updated_at
 */
class ArticleLook extends BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_article_look';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'article_id', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'member_id' => 'Member ID',
            'article_id' => 'Article ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
