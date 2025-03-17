<?php

namespace api\modules\v1\forms;

use common\models\common\EnterpriseCategory;
use yii\base\Model;

class EnterpriseCategoryForm extends Model
{
    /**
     * 分类列表
     */
    const SCENARIO_ENTERPRISE_CATEGORY_LIST = 'enterprise_category_list';

    public $page = 1;

    public $page_size = 15;

    /**
     * @var string 名称
     */
    public $name;

    /**
     * @var int id
     */
    public $id;

    public function rules()
    {
        return [
            // 分类列表
            [['name'], 'string', 'on'=>[static::SCENARIO_ENTERPRISE_CATEGORY_LIST]],
        ];
    }

    public function list() {
        if (!$this->validate()) {
            return false;
        }

        return EnterpriseCategory::find()
            ->where(['status' => 1])
            ->orderBy(['sort' => SORT_DESC, 'created_at' => SORT_DESC])
            ->all();
    }
}
