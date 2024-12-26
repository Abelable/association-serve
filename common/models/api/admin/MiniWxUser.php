<?php

namespace common\models\api\admin;

use Yii;

/**
 * This is the model class for table "gm_mini_wx_user".
 *
 * @property int $id
 * @property string $avatar 头像
 * @property string $nick_name 微信昵称
 * @property string $mobile 手机号
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at
 * @property int $updated_at
 */
class MiniWxUser extends \common\models\base\BaseModel
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
        return 'gm_mini_wx_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['avatar', 'nick_name', 'mobile'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'avatar' => 'Avatar',
            'nick_name' => 'Nick Name',
            'mobile' => 'Mobile',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
