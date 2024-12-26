<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_custom_event_apply".
 *
 * @property int $id
 * @property int $member_id 用户id
 * @property int $custom_event_id 活动id
 * @property string $name 姓名
 * @property string $mobile 手机号
 * @property string $email 邮箱
 * @property string $apply_content_json 申请内容
 * @property string $is_deal 0未处理1已处理 2 驳回
 * @property string $reject_mark 驳回原因
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at
 * @property int $updated_at
 *
 * @property CustomEventForm $customEvent
 */
class CustomEventApply extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_custom_event_apply';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'custom_event_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['apply_content_json'], 'required'],
            [['apply_content_json'], 'string'],
            [['name', 'mobile', 'email', 'is_deal', 'reject_mark'], 'string', 'max' => 255],
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
            'custom_event_id' => 'Custom Event ID',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'apply_content_json' => 'Apply Content Json',
            'is_deal' => 'Is Deal',
            'reject_mark' => 'Reject Mark',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * 获取自定义活动
     * @return \yii\db\ActiveQuery
     */
    public function getCustomEvent(){
        return $this->hasOne(CustomEventForm::class, ['id' => 'custom_event_id']);
    }
}
