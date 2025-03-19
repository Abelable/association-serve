<?php

namespace api\modules\v1\forms;

use common\models\common\Industry;
use yii\base\Model;

class IndustryForm extends Model
{
    /**
     * 产业带列表
     */
    const SCENARIO_INDUSTRY_LIST = 'industry_list';

    public function rules()
    {
        return [
            // 产业带列表
            [['city_name'],'string','on'=>[static::SCENARIO_INDUSTRY_LIST]],
        ];
    }

    public function list() {
        if (!$this->validate()) {
            return false;
        }
        return Industry::find()->where(['status' => 1])->orderBy(['created_at' => SORT_DESC])->all();
    }
}
