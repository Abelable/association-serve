<?php

namespace common\models\api\admin;

use Yii;

/**
 * This is the model class for table "{{%article_class}}".
 *
 * @property int $id
 * @property string $title 分类名称
 * @property int $sort 排序
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at
 * @property int $updated_at
 */
class ArticleClass extends \common\models\base\BaseModel
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
        return '{{%article_class}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort', 'status'], 'integer'],
            [['title'], 'string', 'max' => 255],
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
            'sort' => 'Sort',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
