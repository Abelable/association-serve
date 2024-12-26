<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_banner".
 *
 * @property int $id
 * @property string $title 标题
 * @property string $img 图片url
 * @property int $sort 排序
 * @property int $s_time 开始时间
 * @property int $e_time 结束时间
 * @property int $link_type 跳转类型；[0-不跳转 1-新闻 2-h5]
 * @property int $article_id 新闻id
 * @property string $redirect_url 跳转url
 * @property int $is_show 0不展示1展示
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at
 * @property int $updated_at
 */
class Banner extends \common\models\base\BaseModel
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
        return 'gm_banner';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sort', 's_time', 'e_time', 'link_type', 'article_id', 'is_show', 'status', 'created_at', 'updated_at'], 'integer'],
            [['title', 'img', 'redirect_url'], 'string', 'max' => 255],
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
            'img' => 'Img',
            'sort' => 'Sort',
            's_time' => 'S Time',
            'e_time' => 'E Time',
            'link_type' => 'Link Type',
            'article_id' => 'Article ID',
            'redirect_url' => 'Redirect Url',
            'is_show' => 'Is Show',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
