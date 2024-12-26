<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_certificate".
 *
 * @property int $id
 * @property int $apply_id
 * @property string $logo
 * @property string $title 公司名
 * @property string $number 编号
 * @property string $short_num 缩写
 * @property string $year 年份
 * @property int $start_time 颁发时间
 * @property int $end_time 有效期
 * @property int $status
 * @property int $member_level
 * @property int $created_at
 * @property int $updated_at
 */
class Certificate extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_certificate';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['apply_id', 'start_time', 'end_time', 'status','member_level', 'created_at', 'updated_at'], 'integer'],
            [['logo', 'title', 'number', 'year','short_num'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'apply_id' => 'Apply ID',
            'logo' => 'Logo',
            'title' => 'Title',
            'number' => 'Number',
            'short_num' => 'Short Num',
            'year' => 'Year',
            'member_level'=>'member_level',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
