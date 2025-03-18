<?php

namespace common\models\api;

use Yii;
use common\models\base\BaseModel;

/**
 * This is the model class for table "gm_form_apply".
 *
 * @property int $id
 * @property int $member_id 会员id
 * @property int $member_level 会员等级
 * @property string $open_id 小程序openid
 * @property string $title 标题
 * @property string $name 姓名
 * @property string $mobile 手机号
 * @property string $email 邮箱
 * @property string $apply_content_json 申请内容
 * @property string $logo 申请内容
 * @property int $is_deal 0未处理1已处理 2:驳回
 * @property int $enter_from_id 报名表单id
 * @property int $status 状态[-1:删除;0:禁用;1启用]
 * @property int $created_at
 * @property int $updated_at
 * @property string $company_name    公司名称
 * @property string $reject_mark    驳回原因
 * @property string $number    编号
 * @property string $url    证书url
 * @property int $certificate_status 证书状态[-1:删除;0:禁用;1启用]
 * @property int $category_id 分类id
 * @property string $banner 介绍头图
 * @property string $address_detail 地址详情
 * @property string $main_business 主营业务
 * @property int $mp_app_id 小程序id
 */
class FormApply extends BaseModel
{
    /** @var int 驳回 */
    const IS_REJECT = 2;

    /** @var int 已处理 */
    const IS_DEAL = 1;

    /** @var int 未处理 */

    const NOT_DEAL = 0;

    /**
     * 启用状态
     */
    const STATUS_ACTIVE = 1;

    /**
     * 删除状态
     */
    const STATUS_DEL = -1;

    /**
     * 禁用状态
     */
    const STATUS_FORBIDDEN = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_form_apply';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['member_id', 'member_level', 'is_deal', 'enter_from_id', 'status', 'created_at', 'updated_at','certificate_status','number'], 'integer'],
            [['email'], 'required'],
            [['apply_content_json','logo','url', 'banner', 'address_detail', 'main_business'], 'string'],
            [['open_id', 'title', 'name', 'email','company_name','reject_mark', 'mp_app_id'], 'string', 'max' => 255],
            [['mobile'], 'string', 'max' => 30],
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
            'member_level' => 'Member LEVEL',
            'open_id' => 'Open ID',
            'title' => 'Title',
            'logo' => 'Logo',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'email' => 'Email',
            'apply_content_json' => 'Apply Content Json',
            'is_deal' => 'Is Deal',
            'enter_from_id' => 'Enter From ID',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'company_name'=>'Company Name',
            'reject_mark'=>'Reject Mark',
            'mp_app_id'=>'Mp App Id',
            'address_detail'=>'Address Detail',
            'banner'=>'Banner',
            'category_id'=>'Category Id',
            'main_business'=>'Main Business',
        ];
    }
}
