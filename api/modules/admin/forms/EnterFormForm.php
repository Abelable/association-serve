<?php
namespace api\modules\admin\forms;

use common\helpers\ArrayHelper;
use common\helpers\ExcelHelper;
use common\models\api\admin\EnterFrom;
use common\models\common\CustomEventApply;
use common\models\common\CustomEventForm;
use yii\base\Model;

/**
 * User: sun
 * Date: 2021/8/12
 * Time: 12:37 下午
 */

class EnterFormForm extends Model
{


    /**
     * 列表数据场景
     */
    const SCENARIO_LIST = 'scenario_list';


    /**
     * 更场景
     */
    const SCENARIO_CREATE = 'scenario_create';

    /**
     * 更新场景
     */
    const SCENARIO_UPDATE = 'scenario_update';


    /**
     * 状态更新场景
     */
    const SCENARIO_ACTIVE = 'scenario_active';

    /**
     * 获取表单详情信息
     */
    const SCENARIO_INFO = 'scenario_info';

    /**
     * 自定义活动保存
     */
    const SCENARIO_CUSTOM_EVENT_SAVE = 'scenario_custom_event_save';

    /**
     * 自定义活动列表
     */
    const SCENARIO_CUSTOM_EVENT_LIST = 'scenario_custom_event_list';

    /**
     * 自定义活动操作，开始和结束活动
     */
    const SCENARIO_CUSTOM_EVENT_OPERATE = 'scenario_custom_event_operate';

    /**
     * 自定义活动报名列表
     */
    const SCENARIO_REGISTERED_LIST = 'scenario_registered_list';

    /**
     * 自定义活动报名保存
     */
    const SCENARIO_REGISTERED_SAVE = 'scenario_registered_save';

    /**
     * 自定义活动报名导出
     */
    const SCENARIO_REGISTERED_EXPORT = 'scenario_registered_export';

    /**
     * @var string 报名表单标题
     */
    public $title = '';

    /**
     * @var string 报名备注
     */
    public $mark = '';

    /**
     * @var int 报名表单的id
     */
    public $id = 0;

    /**
     * @var int 查询时候的页数
     */
    public $page = 1;


    /**
     * @var int 一页的数据
     */
    public $page_size = 20;

    /**
     * @var array 报名表单的信息数据
     */
    public $enter_from_json = [];

    /**
     * @var int 活动状态
     */
    public $status = 0;

    /**
     * @var int 开始时间
     */
    public $start_time;

    /**
     * @var int 结束时间
     */
    public $end_time;

    /**
     * @var string 备注
     */
    public $remark;

    /**
     * @var int 报名人数限制
     */
    public $enter_num;

    /**
     * @var int 1-开始 2-结束
     */
    public $action;

    /**
     * @var string 姓名
     */
    public $name;

    /**
     * @var string 手机号
     */
    public $mobile;

    /**
     * @var int 活动id
     */
    public $custom_event_id;

    /**
     * @var int 报名数据
     */
    public $apply_content_json;

    /**
     * @var string id集合，逗号分割，如（1,2,3,4）
     */
    public $ids = '';


    public function rules()
    {
        return [
            // 创建表单场景
            [['title','mark','enter_from_json'],'required','on'=>[static::SCENARIO_CREATE]],
            [['title','mark'],'string','on'=>[static::SCENARIO_CREATE]],
            [['title','mark'], 'filter', 'filter' => 'trim','on'=>[static::SCENARIO_CREATE]],
            [['enter_from_json'],'validatorEnterFrom','on'=>[static::SCENARIO_CREATE]],

            // 更新表单场景
            [['title','mark','enter_from_json','id'],'required','on'=>[static::SCENARIO_UPDATE]],
            [['title','mark'],'string','on'=>[static::SCENARIO_UPDATE]],
            [['title','mark'], 'filter', 'filter' => 'trim','on'=>[static::SCENARIO_UPDATE]],
            [['enter_from_json'],'validatorEnterFrom','on'=>[static::SCENARIO_UPDATE]],

            // 更新活动状态
            [['status','id'],'required','on'=>[static::SCENARIO_ACTIVE]],
            [['status','id'],'integer','on'=>[static::SCENARIO_ACTIVE]],
            [['status'],'in', 'range' => [EnterFrom::STATUS_INACTIVE,EnterFrom::STATUS_ACTIVE],'on'=>[static::SCENARIO_ACTIVE]],

            // 列表数据
            [['page','page_size'],'integer','on'=>[static::SCENARIO_LIST]],

            // 获取某一个表单详情
            [['id'],'integer','on'=>[static::SCENARIO_INFO]],
            [['id'],'required','on'=>[static::SCENARIO_INFO]],

            // 创建自定义活动
            [['title','enter_num','enter_from_json','remark','start_time','end_time'],'required','on'=>[static::SCENARIO_CUSTOM_EVENT_SAVE]],
            [['id','enter_num','status'],'integer','on'=>[static::SCENARIO_CUSTOM_EVENT_SAVE]],
            [['title','remark','start_time','end_time'],'string','on'=>[static::SCENARIO_CUSTOM_EVENT_SAVE]],
            [['title','remark'], 'filter', 'filter' => 'trim','on'=>[static::SCENARIO_CUSTOM_EVENT_SAVE]],
            //['start_time', 'compare', 'compareAttribute' => 'end_time', 'operator' => '<','on'=>[static::SCENARIO_CUSTOM_EVENT_SAVE]],

            //自定义活动列表
            [['page','page_size'],'integer','on'=>[static::SCENARIO_CUSTOM_EVENT_LIST]],
            [['title'],'string','on'=>[static::SCENARIO_CUSTOM_EVENT_LIST]],

            //开始和结束活动
            [['action', 'id'], 'required', 'on' => [static::SCENARIO_CUSTOM_EVENT_OPERATE]],
            [['action', 'id'], 'integer', 'on' => [static::SCENARIO_CUSTOM_EVENT_OPERATE]],

            //自定义活动报名列表
            [['page','page_size', 'custom_event_id'],'integer','on'=>[static::SCENARIO_REGISTERED_LIST]],
            [['name', 'mobile','start_time', 'end_time'],'string','on'=>[static::SCENARIO_REGISTERED_LIST]],

            //自定义活动报名保存
            [['id'],'required', 'on'=>[static::SCENARIO_REGISTERED_SAVE]],
            [['apply_content_json'],'string','on'=>[static::SCENARIO_REGISTERED_SAVE]],
            [['status'],'integer','on'=>[static::SCENARIO_REGISTERED_SAVE]],
            ['status','in','range' => [0,1,-1],'on'=>[static::SCENARIO_REGISTERED_SAVE]],

            //自定义活动报名导出
            [['ids','start_time', 'end_time', 'custom_event_id'],'integer','on'=>[static::SCENARIO_REGISTERED_EXPORT]],
            [['name', 'mobile'],'string','on'=>[static::SCENARIO_REGISTERED_EXPORT]],
        ];
    }


    /**
     * 验证enter_from_json 数据格式
     * @return false
     */
    public function validatorEnterFrom(){
        if (!is_array($this->enter_from_json)){
            $this->addError('validatorEnterFrom','必须是一个数组');
            return false;
        }
        $newJson = [];
        foreach ($this->enter_from_json as $item){
            if (empty($item['_id'])){
                $this->addError('validatorEnterFrom','存在没有设置ID的组件');
                return  false;
            }
            if (isset($newJson[$item['_id']])){
                $this->addError('validatorEnterFrom',"id:{$item['_id']} 重复了");
                return  false;
            }
            $newJson[$item['_id']] = $item ;
        }
    }

    /**
     * 创建表单
     */
    public function formCreate(){
        // 验证数据
        if (!$this->validate()){
            return false;
        }

        $EnterFromForm = new EnterFrom();
        $EnterFromForm->title = $this->title;
        $EnterFromForm->status = EnterFrom::STATUS_INACTIVE;
        $EnterFromForm->enter_from_json = json_encode($this->enter_from_json,JSON_UNESCAPED_UNICODE);
        $EnterFromForm->enter_num = 0;
        $EnterFromForm->mark = $this->mark;
        if (!$EnterFromForm->save()){
            \Yii::error("创建表单失败 res:".json_encode($EnterFromForm->errors,JSON_UNESCAPED_UNICODE));
            $this->addError("EnterFrom",'数据添加失败');
            return false;
        }
        return true;
    }


    /**
     * 更新场景
     */
    public function formUpdate(){
        // 验证数据
        if (!$this->validate()){
            return false;
        }
        if (!$EnterFrom = EnterFrom::findOne(['id'=>$this->id])){
            $this->addError("EnterFrom",'数据不存在');
            return false;
        }
        $EnterFrom->title = $this->title;
        $EnterFrom->status = EnterFrom::STATUS_INACTIVE;
        $EnterFrom->enter_from_json = json_encode($this->enter_from_json,JSON_UNESCAPED_UNICODE);
        $EnterFrom->mark = $this->mark;
        if (!$EnterFrom->save()){
            \Yii::error("更新表单失败 res:".json_encode($EnterFrom->errors,JSON_UNESCAPED_UNICODE));
            $this->addError("EnterFrom",'数据更新失败');
            return false;
        }
        return true;
    }

    /**
     * 修改活动状态
     */
    public function formActiveUpdate(){
        // 验证数据
        if (!$this->validate()){
            return false;
        }
        if (!$EnterFrom = EnterFrom::findOne(['id'=>$this->id])){
            $this->addError("EnterFrom",'数据不存在');
            return false;
        }

        // 更新的状态和当前状态相同 不要处理
        if ($EnterFrom->status == $this->status){
            return true;
        }

        $transaction = \Yii::$app->db->beginTransaction();
        if ($this->status == $EnterFrom::STATUS_ACTIVE){
            // 修改为有效的活动状态 则要关闭现有的有效的活动
            EnterFrom::updateAll(['status'=>$EnterFrom::STATUS_INACTIVE],['status'=>$EnterFrom::STATUS_ACTIVE]);
        }
        $EnterFrom->status = $this->status;
        if (!$EnterFrom->save()){
            $transaction->rollBack();
            \Yii::error("更新状态失败 res:".json_encode($EnterFrom->errors,JSON_UNESCAPED_UNICODE));
            $this->addError("EnterFrom",'数据状态失败');
            return false;
        }
        $transaction->commit();
        return true;
    }


    /**
     * 获取活动列表数
     */
    public function formList(){
        // 验证数据
        if (!$this->validate()){
            return false;
        }

        $EnterFromModel = EnterFrom::find();


       $res['total'] = 0;
       $res['list'] = [];
       $res['page'] = $this->page;
       $res['page_size'] = $this->page_size;

       $res['total'] = $EnterFromModel->count();
       $offset = ($this->page-1)*$this->page_size;
       $list = $EnterFromModel->limit($this->page_size)
                ->offset($offset)
                ->select(['id','title','enter_num','created_at','updated_at','status'])
                ->orderBy(['id'=>SORT_DESC])
                ->asArray()
                ->all();

        $res['list'] = $list;
        return $res;
    }

    /**
     * 获取某一个表单的详情数据
     */
    public function formInfo(){
        // 验证数据
        if (!$this->validate()){
            return false;
        }


        if (!$EnterFromModel = EnterFrom::findOne(['id'=>$this->id])){
            $this->addError('id','数据不存在');
            return false;
        }

        $res =  $EnterFromModel->toArray();;

        $res['enter_from_json'] = json_decode($res['enter_from_json'],true);
        return $res;
    }

    /**
     * 自定义活动保存
     * @return bool
     */
    public function customEventSave(){
        // 验证数据
        if (!$this->validate()){
            return false;
        }

        $model = CustomEventForm::findOne(['id' => $this->id]);
        if(empty($model)) {
            $model = new CustomEventForm();
            $model->status = 1;
        }else{
            $model->status = $this->status;
        }

        $model->title = $this->title;
        $model->enter_from_json = json_encode($this->enter_from_json,JSON_UNESCAPED_UNICODE);
        $model->enter_num = $this->enter_num;
        $model->start_time = $this->start_time;
        $model->end_time = $this->end_time;
        $model->remark = $this->remark;
        if (!$model->save()){
            \Yii::error("创建表单失败 res:".json_encode($model->errors,JSON_UNESCAPED_UNICODE));
            $this->addError("EnterFrom",'数据添加失败');
            return false;
        }
        return true;
    }

    /**
     * 自定义活动列表
     * @return false
     */
    public function customEventList() {
        // 验证数据
        if (!$this->validate()){
            return false;
        }

        $query = CustomEventForm::find()
            ->where(['status' => 1]);

        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;
        $res['total'] = $query->count();
        $offset = ($this->page-1)*$this->page_size;
        $list = $query->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['id'=>SORT_DESC])
            ->asArray()
            ->all();
        foreach ($list as &$val) {
            if($val['start_time'] > time()) {
                $status = CustomEventForm::INACTIVE;
            }elseif ($val['start_time'] <= time() && $val['end_time'] >= time()) {
                $status = CustomEventForm::ACTIVE;
            }else{
                $status = CustomEventForm::END;
            }
            $val['activity_status'] = $status;
            $val['enter_from_json'] = json_decode($val['enter_from_json'], true);
        }

        $res['list'] = $list;

        $res = ArrayHelper::itemsNumber($res);

        return $res;
    }

    /**
     * 提前开始和结束活动
     * @return bool
     */
    public function customEventOperate() {
        // 验证数据
        if (!$this->validate()){
            return false;
        }
        $customEvent = CustomEventForm::findOne(['status' => 1, 'id' => $this->id]);
        if(!$customEvent) {
            $this->addError("operate",'活动不存在');
            return false;
        }
        if($this->action == 1) {
            //提前开始
            if($customEvent->start_time<= time()) {
                $this->addError("operate",'活动已经开始');
                return false;
            }
            $customEvent->start_time = time();
        }else{
            if($customEvent->end_time<= time()) {
                $this->addError("operate",'活动已经结束');
                return false;
            }
            $customEvent->end_time = time();
        }

        return $customEvent->save();
    }

    /**
     * 活动报名列表
     * @return false
     */
    public function registeredList() {
        // 验证数据
        if (!$this->validate()){
            return false;
        }
        
        $query = CustomEventApply::find()
            ->with('customEvent')
            ->where(['status' => 1])
            ->andFilterWhere(['custom_event_id' => $this->custom_event_id])
            ->andFilterWhere(['name' => $this->name])
            ->andFilterWhere(['mobile' => $this->mobile]);
        if(!empty($this->start_time) && !empty($this->end_time)) {
            $startTime = strtotime($this->start_time.' 00:00:00');
            $endTime = strtotime($this->end_time.' 23:59:59');

            $query = $query->andWhere(['>=','created_at', $startTime])
            ->andWhere(['<=','created_at', $endTime]);
        }

        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;
        $res['total'] = $query->count();
        $offset = ($this->page-1)*$this->page_size;
        $list = $query->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['id'=>SORT_DESC])
            ->asArray()
            ->all();
        foreach ($list as &$val) {
            $val['apply_content_json'] = json_decode($val['apply_content_json'], true);
            $val['customEvent']['enter_from_json'] = json_decode($val['customEvent']['enter_from_json'], true);
        }
        $res['list'] = $list;
        return $res;
    }

    /**
     * 报名数据编辑
     * @return bool
     */
    public function registeredSave() {
        if (!$this->validate()){
            return false;
        }

        $model = CustomEventApply::findOne(['id' => $this->id]);

        $applyContentJson = json_decode($this->apply_content_json, true);
        if(!empty($applyContentJson)) {
            foreach ($applyContentJson as $v){
                if ($v['name'] == 'name'){
                    $model->name = $v['value'];
                }
                if ($v['name'] == 'mobile'){
                    $model->mobile = $v['value'];
                }
                if ($v['name'] == 'email'){
                    $model->email = $v['value'];
                }
            }
        }

        $model->apply_content_json = $this->apply_content_json;
        $model->status = is_null($this->status) ? $model->status : $this->status;

        if(!$model->save()) {
            return false;
        }

        return true;
    }

    public function registeredExport() {
        if (!$this->validate()){
            return false;
        }

        $ids = $this->ids ? explode(',',$this->ids) : null;

        $query = CustomEventApply::find()
            ->with('customEvent')
            ->where(['status' => 1])
            ->andFilterWhere(['custom_event_id' => $this->custom_event_id])
            ->andFilterWhere(['name' => $this->name])
            ->andFilterWhere(['mobile' => $this->mobile])
            ->andFilterWhere(['>=','created_at', $this->start_time])
            ->andFilterWhere(['<=','created_at', $this->end_time]);

        if ($ids){
            $query = CustomEventApply::find()->andFilterWhere(['id' => $ids]);
        }
        if (!$list = $query->asArray()->all()){
            $this->addError("EnterApplyForm",'数据不存在');
            return false;
        }

        $exportArr = [];
        $header = [];

        /** 组装header */
        $arr = json_decode($list[0]['apply_content_json'],true);
        foreach ($arr as $v){
            $header[] = [$v['title'],$v['title']];
        }
        /** end */

        /** 组装list */
        $tmp = [];
        foreach ($list as $v){
            $arr = json_decode($v['apply_content_json'],true);
            foreach ($arr as $vv){
                $tmp[$vv['title']] = $vv['value'];
            }
            array_push($exportArr,$tmp);
        }
        /** end */
        return ExcelHelper::exportData($exportArr,$header,'自定义活动报名数据_'.time());
    }
}
