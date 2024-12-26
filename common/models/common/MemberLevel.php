<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_member_level".
 *
 * @property int $id 主键
 * @property int $merchant_id 商户id
 * @property int $level 等级（数字越大等级越高）
 * @property string $name 等级名称
 * @property string $money 消费额度满足则升级
 * @property int $check_money 选中消费额度
 * @property int $integral 消费积分满足则升级
 * @property int $check_integral 选中消费积分
 * @property int $middle 条件（0或 1且）
 * @property string $discount 折扣
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property string $detail 会员介绍
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class MemberLevel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_member_level';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'level', 'check_money', 'integral', 'check_integral', 'middle', 'status', 'created_at', 'updated_at'], 'integer'],
            [['money', 'discount'], 'number'],
            [['name', 'detail'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'merchant_id' => 'Merchant ID',
            'level' => 'Level',
            'name' => 'Name',
            'money' => 'Money',
            'check_money' => 'Check Money',
            'integral' => 'Integral',
            'check_integral' => 'Check Integral',
            'middle' => 'Middle',
            'discount' => 'Discount',
            'status' => 'Status',
            'detail' => 'Detail',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
