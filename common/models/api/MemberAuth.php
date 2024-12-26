<?php

namespace common\models\api;

use Yii;

/**
 * This is the model class for table "gm_member_auth".
 *
 * @property int $id 主键
 * @property int $merchant_id 商户id
 * @property int $member_id 用户id
 * @property string $open_id openid
 * @property string $unionid 唯一ID
 * @property string $oauth_client 授权组别
 * @property string $oauth_client_user_id 授权id
 * @property int $gender 性别[0:未知;1:男;2:女]
 * @property string $nickname 昵称
 * @property string $head_portrait 头像
 * @property string $birthday 生日
 * @property string $country 国家
 * @property string $province 省
 * @property string $city 市
 * @property int $status 状态(-1:已删除,0:禁用,1:正常)
 * @property int $created_at 创建时间
 * @property int $updated_at 修改时间
 */
class MemberAuth extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_member_auth';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['merchant_id', 'member_id', 'gender', 'status', 'created_at', 'updated_at'], 'integer'],
            [['birthday'], 'safe'],
            [['open_id'], 'string', 'max' => 255],
            [['unionid'], 'string', 'max' => 64],
            [['oauth_client'], 'string', 'max' => 20],
            [['oauth_client_user_id', 'nickname', 'country', 'province', 'city'], 'string', 'max' => 100],
            [['head_portrait'], 'string', 'max' => 150],
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
            'member_id' => 'Member ID',
            'open_id' => 'Open ID',
            'unionid' => 'Unionid',
            'oauth_client' => 'Oauth Client',
            'oauth_client_user_id' => 'Oauth Client User ID',
            'gender' => 'Gender',
            'nickname' => 'Nickname',
            'head_portrait' => 'Head Portrait',
            'birthday' => 'Birthday',
            'country' => 'Country',
            'province' => 'Province',
            'city' => 'City',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
