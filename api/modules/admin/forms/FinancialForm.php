<?php


namespace api\modules\admin\forms;

use common\models\common\Financial;
use common\models\common\FinancialOut;
use yii\base\Model;

class FinancialForm extends Model
{
    /**
     * 保存
     */
    const SCENARIO_FINANCIAL_SAVE = 'financial_save';

    /**
     * 列表
     */
    const SCENARIO_FINANCIAL_LIST = 'financial_list';

    const SCENARIO_FINANCIAL_OUT_SAVE='financial_out_save';

    const SCENARIO_FINANCIAL_OUT_LIST='financial_out_list';
    /**
     * @var int id
     */
    public $id=0;


    /**
     * @var int $select_year
     * 选择的年份
     */
    public $select_year=0;

    /**
     * @var string 内容(json字符串)
     */
    public $apply_content_json = '';

    public function rules()
    {
        return [
            //保存
            [['apply_content_json'], 'required', 'on' => [self::SCENARIO_FINANCIAL_SAVE]],
            [['id'], 'integer', 'on' => [self::SCENARIO_FINANCIAL_SAVE]],
            //保存
            [['apply_content_json'], 'required', 'on' => [self::SCENARIO_FINANCIAL_OUT_SAVE]],
            [['id'], 'integer', 'on' => [self::SCENARIO_FINANCIAL_OUT_SAVE]],

            //列表
            [['select_year'], 'required', 'on' => [self::SCENARIO_FINANCIAL_LIST]],
            [['select_year'], 'required', 'on' => [self::SCENARIO_FINANCIAL_OUT_LIST]],

        ];
    }


    /**
     * 法律汇编分类，添加、编辑、新增
     * @return bool
     */
    public function financialSave() {
        if(!$this->validate()) {
            return false;
        }

        $applyContentJson = json_decode($this->apply_content_json, true);
        foreach ($applyContentJson as $v){

            $model=Financial::find()->where(['year'=>$v['year'],'month'=>$v['month']])->one();
            if(!$model){
                $model=new Financial();
            }

            $model->year=$v['year'];
            $model->month=$v['month'];
            $model->member_income=$v['member_income'];
            $model->project_income=$v['project_income'];
            $model->service_income=$v['service_income'];
            $model->other_income=$v['other_income'];
            $model->total_income=$v['total_income'];

            if(!$model->save()) {
                $this->addError('financial_save', '保存异常');
                return false;
            }
        }

        return true;
    }

    public function financialList(){
        if(!$this->validate()) {
            return false;
        }

        $query = Financial::find()
            ->where(['year' => $this->select_year]);

        $res['list'] = $query
            ->orderBy(['month' => SORT_ASC])
            ->asArray()
            ->all();

        return $res;
    }


    public function financialOutSave() {
        if(!$this->validate()) {
            return false;
        }

        $applyContentJson = json_decode($this->apply_content_json, true);
        foreach ($applyContentJson as $v){

            $model=FinancialOut::find()->where(['year'=>$v['year'],'month'=>$v['month']])->one();
            if(!$model){
                $model=new FinancialOut();
            }

            $model->year=$v['year'];
            $model->month=$v['month'];
            $model->member_expend=$v['member_expend'];
            $model->technology_expend=$v['technology_expend'];
            $model->entertain_expend=$v['entertain_expend'];
            $model->meeting_expend=$v['meeting_expend'];
            $model->travel_expend=$v['travel_expend'];
            $model->other_expend=$v['other_expend'];
            $model->total_expend=$v['total_expend'];

            if(!$model->save()) {
                $this->addError('financial_out_save', '保存异常');
                return false;
            }
        }

        return true;
    }

    public function financialOutList(){
        if(!$this->validate()) {
            return false;
        }

        $query = FinancialOut::find()
            ->where(['year' => $this->select_year]);

        $res['list'] = $query
            ->orderBy(['month' => SORT_ASC])
            ->asArray()
            ->all();

        return $res;
    }
}