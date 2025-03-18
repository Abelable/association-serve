<?php

namespace common\models\common;

use Yii;

/**
 * This is the model class for table "gm_enterprise_consulting".
 *
 * @property int $id
 * @property int $enterprise_id 企业id
 * @property string $name 姓名
 * @property string $mobile 手机号
 * @property string $company_name 单位名称
 * @property string $content 咨询问题
 * @property int $status 状态：-1-删除，0-禁用；1-启用
 * @property int $created_at
 * @property int $updated_at
 */
class EnterpriseConsulting extends \common\models\base\BaseModel
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gm_enterprise_consulting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['enterprise_id', 'status', 'created_at', 'updated_at'], 'integer'],
            [['name', 'mobile', 'company_name', 'content'], 'string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enterprise_id' => 'Enterprise ID',
            'name' => 'Name',
            'mobile' => 'Mobile',
            'company_name' => 'Company Name',
            'content' => 'Content',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
