<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_personal_apply".
 *
 * @property int $id
 * @property int $member_id 用户id
 * @property string $open_id 小程序openid
 * @property string $name 姓名
 * @property string $mobile 手机号
 * @property int $sex 性别；1-男 2-女 0-未知
 * @property string $employer 工作单位
 * @property string $department 工作部门或所
 * @property int $talent_classification 人才分类：1-市场监管 2-非市场监管
 * @property string $introduction 人才介绍
 * @property string $expert_intent_id 专家意向id,多个id用英文逗号隔开
 * @property int $score 评分
 * @property string $apply_content_json 申请内容
 * @property int $is_deal 0未处理1已处理 2 驳回
 * @property int $status 状态：-1-删除；0-禁用；1-启用
 * @property int $created_at
 * @property int $updated_at
 */
class PersonalApply extends \common\models\base\BaseModel
{
    /**
     * 人才分类
     */
    const MARKET_SUPERVISION = 1;  //市场监管
    const NOT_MARKET_SUPERVISION = 1;  //非市场监管

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_personal_apply';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'sex', 'talent_classification', 'score', 'is_deal', 'status', 'created_at', 'updated_at'], 'integer'],
            [['introduction', 'apply_content_json'], 'string'],
            [['open_id', 'name', 'mobile', 'employer', 'department', 'expert_intent_id'], 'string', 'max' => 255],
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
            'open_id' => 'Open ID',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'sex' => 'Sex',
            'employer' => 'Employer',
            'department' => 'Department',
            'talent_classification' => 'Talent Classification',
            'introduction' => 'Introduction',
            'expert_intent_id' => 'Expert Intent ID',
            'score' => 'Score',
            'apply_content_json' => 'Apply Content Json',
            'is_deal' => 'Is Deal',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
