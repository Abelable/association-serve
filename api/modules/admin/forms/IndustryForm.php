<?php

namespace api\modules\admin\forms;

use common\models\common\Industry;
use yii\base\Model;

class IndustryForm extends Model
{
    /**
     * 产业带新增、编辑、删除
     */
    const SCENARIO_INDUSTRY_SAVE = 'industry_save';

    /**
     * 产业带列表
     */
    const SCENARIO_INDUSTRY_LIST = 'industry_list';

    /**
     * @var string 地区
     */
    public $city_name;

    /**
     * @var string 核心产业
     */
    public $main;

    /**
     * @var string 头部产业
     */
    public $top;

    /**
     * @var int id
     */
    public $id;

    /**
     * @var int 状态
     */
    public $status = 1;

    public $page = 0;

    public $page_size = 10;

    public function rules()
    {
        return [
            // 公共参数
            [['page','page_size'],'integer'],

            // 产业添加、编辑、删除
            [['city_name', 'main', 'top'], 'required', 'on' => [self::SCENARIO_INDUSTRY_SAVE]],
            [['id','status'], 'integer', 'on' => [self::SCENARIO_INDUSTRY_SAVE]],
            ['status', 'in', 'range' => [-1,0,1], 'on' => [self::SCENARIO_INDUSTRY_SAVE]],

            // 产业列表
            [['city_name'], 'string', 'on' => [self::SCENARIO_INDUSTRY_LIST]],
        ];
    }


    /**
     * 产业管理，添加、编辑、新增
     * @return bool
     */
    public function save() {
        if(!$this->validate()) {
            return false;
        }
        $info = Industry::findOne(['id' => $this->id]);
        if(!$info) {
            $info = new Industry();
        }
        $info->city_name = $this->city_name;
        $info->main = $this->main;
        $info->top = $this->top;
        $info->status = $this->status;
        if(!$info->save()) {
            $this->addError('industry_save', '保存产业带信息异常');
            return false;
        }
        return true;
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
        $query = Industry::find()->where(['status' => 1]);
        if ($this->city_name) {
            $query->andFilterWhere(['like', 'city_name', $this->city_name]);
        }
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