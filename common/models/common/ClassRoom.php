<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_class_room".
 *
 * @property int $id
 * @property string $title 标题
 * @property int $author_id 作者id
 * @property string $cover_img 封面
 * @property string $media_url 视频地址
 * @property int $duration 视频时长；单位：秒
 * @property int $is_try 是否试看；0-否；1-是
 * @property int $try_time 试看时间：单位：分
 * @property string $password 观看密码
 * @property string $introduction 简介说明
 * @property int $sort 权重
 * @property int $status 状态：-1-删除，0-禁用；1-启用
 * @property int $created_at
 * @property int $updated_at
 *
 * @property Author $author
 * @property ClassRoomTagMap $tagMap
 */
class ClassRoom extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_class_room';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['author_id', 'duration', 'is_try', 'try_time', 'sort','views', 'status', 'created_at', 'updated_at'], 'integer'],
            [['introduction'], 'string'],
            [['title', 'cover_img', 'media_url', 'password'], 'string', 'max' => 255],
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
            'author_id' => 'Author ID',
            'cover_img' => 'Cover Img',
            'media_url' => 'Media Url',
            'duration' => 'Duration',
            'is_try' => 'Is Try',
            'try_time' => 'Try Time',
            'password' => 'Password',
            'introduction' => 'Introduction',
            'sort' => 'Sort',
            'views' => 'Views',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function getAuthor() {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }

    public function getTagMap() {
        return $this->hasMany(ClassRoomTagMap::class, ['class_room_id' => 'id']);
    }
}
