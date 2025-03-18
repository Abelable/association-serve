<?php

namespace api\modules\v1\forms;

use common\models\common\EnterpriseConsulting;
use yii\base\Model;

class EnterpriseConsultingForm extends Model
{
    /**
     * 企业咨询新增、编辑、删除
     */
    const SCENARIO_ENTERPRISE_CONSULTING_SAVE = 'enterprise_consulting_save';

    /**
     * @var int 企业id
     */
    public $enterprise_id;

    /**
     * @var string 姓名
     */
    public $name;

    /**
     * @var string 手机号
     */
    public $mobile;

    /**
     * @var string 单位名称
     */
    public $company_name;

    /**
     * @var string 咨询问题
     */
    public $content;

    public function rules()
    {
        return [
            // 企业咨询添加、编辑、删除
            [['enterprise_id', 'name', 'mobile', 'company_name', 'content'], 'required', 'on' => [self::SCENARIO_ENTERPRISE_CONSULTING_SAVE]],
            [['enterprise_id'], 'integer', 'on' => [self::SCENARIO_ENTERPRISE_CONSULTING_SAVE]],
            [['name', 'mobile', 'company_name', 'content'], 'string', 'on' => [self::SCENARIO_ENTERPRISE_CONSULTING_SAVE]],
        ];
    }

    /**
     * 企业咨询管理，添加、编辑、新增
     * @return bool
     */
    public function save() {
        if(!$this->validate()) {
            return false;
        }
        $info = new EnterpriseConsulting();
        $info->enterprise_id = $this->enterprise_id;
        $info->name = $this->name;
        $info->mobile = $this->mobile;
        $info->company_name = $this->company_name;
        $info->content = $this->content;
        $info->status = 1;
        if(!$info->save()) {
            $this->addError('open_info_save', '保存企业咨询信息异常');
            return false;
        }
        return true;
    }
}
