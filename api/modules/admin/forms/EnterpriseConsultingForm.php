<?php

namespace api\modules\admin\forms;

use common\models\common\EnterpriseConsulting;
use yii\base\Model;

class EnterpriseConsultingForm extends Model
{
    /**
     * 企业咨询列表
     */
    const SCENARIO_ENTERPRISE_CONSULTING_LIST = 'enterprise_consulting_list';

    /**
     * @var int 企业id
     */
    public $enterprise_id;

    public $page = 0;

    public $page_size = 10;

    public function rules()
    {
        return [
            // 公共参数
            [['page','page_size'],'integer'],

            // 企业咨询列表
            [['enterprise_id'], 'integer', 'on' => [self::SCENARIO_ENTERPRISE_CONSULTING_LIST]],
        ];
    }

    public function list() {
        if (!$this->validate()){
            return false;
        }

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $offset = ($this->page - 1) * $this->page_size;
        $query = EnterpriseConsulting::find()
            ->where(['status' => 1])
            ->where(['enterprise_id' => $this->enterprise_id]);
        $list = $query->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();
        $res['total'] = $query->count();

        $res['list'] = $list;
        return $res;
    }
}