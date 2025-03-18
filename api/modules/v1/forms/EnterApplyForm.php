<?php


namespace api\modules\v1\forms;

use common\helpers\ArrayHelper;
use common\models\api\FormApply;
use common\models\api\MemberAuth;
use common\models\common\CustomEventApply;
use common\models\common\CustomEventForm;
use common\sdk\alibabaCloud\Sms;
use Yii;
use yii\base\Model;

class EnterApplyForm extends Model
{
    /**
     * 申请场景
     */
    const SCENARIO_APPLY = 'scenario_apply';

    /**
     * 更新场景
     */
    const SCENARIO_APPLY_UPDATE = 'scenario_apply_update';

    /**
     * 查询记录信息
     */
    const SCENARIO_APPLY_LIST = 'scenario_apply_list';

    /**
     * 企业列表
     */
    const SCENARIO_LIST = 'scenario_list';

    /**
     * 企业详情
     */
    const SCENARIO_DETAIL = 'scenario_detail';

    /**
     * 详情记录
     */
    const SCENARIO_APPLY_INFO = 'scenario_apply_info';

    /**
     * 自定义活动提交
     */
    const SCENARIO_CUSTOM_EVENT_APPLY = 'scenario_custom_event_apply';

    /**
     * 自定义活动详情
     */
    const SCENARIO_CUSTOM_EVENT_DETAIL = 'scenario_custom_event_detail';

    /**
     * @var int 查询时候的页数
     */
    public $page = 1;


    /**
     * @var int 一页的数据
     */
    public $page_size = 20;

    /**
     * @var int 分类id
     */
    public $category_id;

    /**
     * @var int 报名表单id
     */
    public $id = 0;

    /**
     * @var string 标题
     */
    public $title = '';

    /**
     * @var string 姓名
     */
    public $name = '';

    /**
     * @var string 手机号
     */
    public $mobile = '';

    /**
     * @var int 邮箱
     */
    public $email = '';

    /**
     * @var string 申请内容(json字符串)
     */
    public $apply_content_json = '';

    /**
     * @var string 创建时间
     */
    public $created_at = 0;

    /**
     * @var string 更新时间
     */
    public $updated_at = 0;

    /**
     * @var int 状态[-1:删除;0:禁用;1启用]
     */
    public $status = 0;

    /**
     * @var int 会员id
     */
    public $member_id = 0;

    /**
     * @var string 小程序openid
     */
    public $open_id = '';

    /**
     * @var int 0未处理1已处理
     */
    public $is_deal = 0;

    /**
     * @var int 表单id
     */
    public $enter_from_id = 0;

    /**
     * @var string 公司名称
     */
    public $company_name = '';

    /**
     * @var null|FormApply
     */
    public $_FormApply = null;

    /**
     * @var int 活动id
     */
    public $custom_event_id;

    public $logo;

    public function rules()
    {
        return [
            //申请
            [['id','status','is_deal','created_at','updated_at'],'integer','on'=>[static::SCENARIO_APPLY]],
            [['name','mobile','email','title','open_id'],'string','on'=>[static::SCENARIO_APPLY]],
            [['apply_content_json'],'filter','filter'=>function($v){
                return $v;
            },'on'=>[static::SCENARIO_APPLY]],
            [['apply_content_json'],'validatorApplyContentJson','on'=>[static::SCENARIO_APPLY]],

            //更新场景
            [['id','open_id','title'],'required','on'=>[static::SCENARIO_APPLY_UPDATE]],
            [['id'],'integer','on'=>[static::SCENARIO_APPLY_UPDATE]],
            [['name','mobile','open_id','email','title'],'string','on'=>[static::SCENARIO_APPLY_UPDATE]],
            [['apply_content_json'],'filter','filter'=>function($v){
                return $v;
            },'on'=>[static::SCENARIO_APPLY_UPDATE]],
            [['apply_content_json'],'validatorApplyContentJson','on'=>[static::SCENARIO_APPLY_UPDATE]],

            // 查询记录场景
            [['open_id'],'required','on'=>[static::SCENARIO_APPLY_LIST]],
            [['page','page_size'],'integer','on'=>[static::SCENARIO_APPLY_LIST]],

            // 企业列表
            [['category_id', 'company_name'],'string','on'=>[static::SCENARIO_LIST]],
            [['page','page_size'],'integer','on'=>[static::SCENARIO_LIST]],

            // 企业详情
            [['id'],'required','on'=>[static::SCENARIO_DETAIL]],
            [['id'],'integer','on'=>[static::SCENARIO_DETAIL]],

            // 查询详情信息场景
            [['open_id','id'],'required','on'=>[static::SCENARIO_APPLY_INFO]],
            [['id'],'integer','on'=>[static::SCENARIO_APPLY_INFO]],

            //自定义活动报名
            [['id','status','is_deal','member_id','custom_event_id'],'integer','on'=>[static::SCENARIO_CUSTOM_EVENT_APPLY]],
            [['name','mobile','email'],'string','on'=>[static::SCENARIO_CUSTOM_EVENT_APPLY]],
            [['apply_content_json'],'filter','filter'=>function($v){
                return $v;
            },'on'=>[static::SCENARIO_CUSTOM_EVENT_APPLY]],

            // 自定义活动详情
            [['custom_event_id'],'required','on'=>[static::SCENARIO_CUSTOM_EVENT_DETAIL]],
            [['custom_event_id'],'integer','on'=>[static::SCENARIO_CUSTOM_EVENT_DETAIL]],
        ];
    }


    public function validatorApplyContentJson(){
        // 获取 company_name

        foreach ($this->apply_content_json as $item){
            if ($item['name'] == 'company_name'){
                $this->company_name = $item['value'];
            }
        }

        if (empty($this->company_name)){
            $this->addError('company_name','公司名称不能为空');
        }else{
            // 校验公司名称
            $this->validatorCompanyName();
        }
        return ;
    }

    /**
     * 校验公司名称
     */
    public function validatorCompanyName(){
        /**
         * @var FormApply|null $FormApply
         */
        $FormApply = FormApply::find()
            ->where(['<>','status',FormApply::STATUS_DEL])
            ->andWhere(['company_name'=>$this->company_name])->one();
        if (empty($FormApply)){
            return;
        }
        
        if ($FormApply->open_id != $this->open_id){
            $this->addError('company_name','公司名称已经被人提交');
        }

        // 当前场景为提交场景 该用户已经提交过名称一样的企业  做更新动作
        if ($this->getScenario() == static::SCENARIO_APPLY){
            $this->_FormApply = $FormApply;
        }

        // 当前场景为更新场景 并且更新该企业名称不是本条要更新的数据
        if ($this->getScenario() == static::SCENARIO_APPLY_UPDATE && $FormApply->id  != $this->id){
            $this->addError('company_name','您已经提交过该企业的入会申请');
        }




        return;
    }

    /**
     * @return bool
     * @note:申请操作
     */
    public function apply()
    {
        if (!$this->validate())
        {
            return false;
        }

        $openid = \Yii::$app->request->headers->get('open-id');
        $memberAuth = MemberAuth::findOne(['open_id' => $openid]);
        if (empty($memberAuth)){
//            \Yii::error("先授权 res:".json_encode($memberAuth->errors,JSON_UNESCAPED_UNICODE));
            $this->addError("EnterFrom",'请先授权');
            return false;
        }

        if ($this->_FormApply){
            $FormApply = $this->_FormApply;
        }else{
            $FormApply = new FormApply();
        }
        foreach ($this->apply_content_json as $v){
            if ($v['name'] == '_name'){
                $FormApply->name = $v['value']??'1';
            }
            if ($v['name'] == '_mobile'){
                $FormApply->mobile = $v['value']??'1';
            }
            if ($v['name'] == '_email'){
                $FormApply->email = $v['value']??'1';
            }
            if ($v['name'] == 'logo'){
                $FormApply->logo = $v['value']??'1';
            }
        }
        $FormApply->title = $this->title;
        $FormApply->member_id = isset($memberAuth->member_id) ? $memberAuth->member_id : 0;
        $FormApply->open_id = $openid;
        $FormApply->company_name = $this->company_name;
        $FormApply->apply_content_json = json_encode($this->apply_content_json,JSON_UNESCAPED_UNICODE);
        if (!$FormApply->save()){
            \Yii::error("创建表单失败 res:".json_encode($FormApply->errors,JSON_UNESCAPED_UNICODE));
            $this->addError("EnterFrom",'数据添加失败');
            return false;
        }

        //发送短信通知
        $res = Sms::sendSms([
            'phoneNumbers' => $FormApply->mobile,
            'signName' => Yii::$app->params['sms']['template']['apply']['sign'],
            'templateCode' => Yii::$app->params['sms']['template']['apply']['code'],
            'templateParam' => json_encode(['day' => 3], JSON_UNESCAPED_UNICODE)
        ]);
        if(!$res) {
            Yii::error("{$FormApply->mobile}申请入会报名成功通知异常");
        }
        return true;
    }


    /**
     * @return bool 更新表单数据
     */
    public function updateApply(){
        if (!$this->validate())
        {
            return false;
        }
        // 查询数据信息
        $FormApply = FormApply::findOne(['id'=>$this->id,'open_id'=>$this->open_id]);
        if (!$FormApply){
            $this->addError('id','提交的内容不存在');
            return false;
        }
        if ($FormApply->status == $FormApply::STATUS_DEL){
            $this->addError('id','数据已经被删除');
            return false;
        }
        if ($FormApply->is_deal == $FormApply::IS_DEAL){
            $this->addError('id','数据已经被审核通过不允许修改');
            return false;
        }


        foreach ($this->apply_content_json as $v){
            if ($v['name'] == '_name'){
                $FormApply->name = $v['value'];
            }
            if ($v['name'] == '_mobile'){
                $FormApply->mobile = $v['value'];
            }
            if ($v['name'] == '_email'){
                $FormApply->email = $v['value'];
            }
        }

        $FormApply->apply_content_json = json_encode($this->apply_content_json,JSON_UNESCAPED_UNICODE);
        $FormApply->title = $this->title;
        $FormApply->company_name = $this->company_name;
        $FormApply->is_deal = $FormApply::NOT_DEAL;
        if (!$FormApply->save()){
            Yii::error("更新提交的申请数据失败 res:".json_encode($FormApply->errors,JSON_UNESCAPED_UNICODE));
            $this->addError('数据更新失败');
            return false;
        }
        return true;

    }

    /**
     * 入会申请详情信息
     */
    public function infoApply(){
        if (!$this->validate()) {
            return false;
        }
        $FormApply = FormApply::find()
            ->where(['open_id'=>$this->open_id,'id'=>$this->id])
            ->andWhere(['<>','status',FormApply::STATUS_DEL])->one();

        if (!$FormApply){
            $this->addError('id','数据不存在');
            return false;
        }

        $res = $FormApply->toArray();
        $res['apply_content_json'] = json_decode($res['apply_content_json'],true);
        $res['reject_mark'] = $res['reject_mark'] ? $res['reject_mark'] : '';
        return $res;
    }


    public function customEventApply()
    {
        if (!$this->validate())
        {
            return false;
        }


        /**
         * @var CustomEventForm $customEvent
         */
        $customEvent = CustomEventForm::find()
            ->where(['status' => 1, 'id' => $this->custom_event_id])
            ->andWhere(['<=', 'start_time', time()])
            ->andWhere(['>=', 'end_time', time()])
            ->one();
        if (empty($customEvent)){
            $this->addError("EnterFrom",'活动不存在');
            return false;
        }
        if($customEvent->registered_num >= $customEvent->enter_num) {
            $this->addError("EnterFrom",'报名人数已满');
            return false;
        }

        $FormApply = new CustomEventApply();
        $FormApply->custom_event_id = $this->custom_event_id;
        foreach ($this->apply_content_json as $v){
            if ($v['name'] == 'name'){
                $FormApply->name = $v['value'];
            }
            if ($v['name'] == 'mobile'){
                $FormApply->mobile = (string)$v['value'];
            }
            if ($v['name'] == 'email'){
                $FormApply->email = $v['value'];
            }
        }

        $apply = CustomEventApply::findOne([
            'mobile' => $FormApply->mobile,
            'custom_event_id' => $this->custom_event_id,
            'status' => 1
        ]);
        if($apply) {
            $this->addError("EnterFrom",'你已经报名');
            return false;
        }

        $FormApply->member_id = $this->member_id;
        $FormApply->apply_content_json = json_encode($this->apply_content_json,JSON_UNESCAPED_UNICODE);
        if (!$FormApply->save()){
            \Yii::error("创建表单失败 res:".json_encode($FormApply->errors,JSON_UNESCAPED_UNICODE));
            $this->addError("EnterFrom",'数据添加失败');
            return false;
        }

        //更新报名人数
        CustomEventForm::updateAllCounters(['registered_num' => 1], ['id' => $this->custom_event_id]);

        //发送钉钉通知
        //\Yii::warning("{$FormApply->mobile}报名自定义活动",'custom_event');
        $timeNow = date('Y-m-d H:i:s');
        \Yii::warning("{$FormApply->name}于{$timeNow}报名了{$FormApply->customEvent->title}活动
        （{$FormApply->customEvent->registered_num}/{$FormApply->customEvent->enter_num}）",'custom_event');

        return true;
    }

    /**
     * 自定义活动详情
     * @return array|false
     */
    public function customEventDetail() {
        if(!$this->validate()) {
            return false;
        }

        /**
         * @var CustomEventForm $customEvent
         */
        $customEvent = CustomEventForm::find()
            ->where(['status' => 1, 'id' => $this->custom_event_id])
            ->one();
        if (empty($customEvent)){
            $this->addError("EnterFrom",'活动不存在');
            return false;
        }
        if($customEvent->start_time > time()) {
            $this->addError("EnterFrom",'活动未开始');
            return false;
        }
        if($customEvent->end_time < time()) {
            $this->addError("EnterFrom",'活动已结束');
            return false;
        }

        $res = $customEvent->toArray();
        $res['enter_from_json'] = json_decode($res['enter_from_json'],true);

        return $res;
    }

    /**
     * 更新入会申请记录信息
     */
    public function listApply(){
        if (!$this->validate()) {
            return false;
        }

        $FormApplyModel = FormApply::find()
            ->where(['open_id'=>$this->open_id])
            ->andWhere(['<>','status',FormApply::STATUS_DEL]);

        $res['total'] = 0;
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;
        $res['list'] = [];

        $res['total'] = $FormApplyModel->count();
        $offset = ($this->page-1)*$this->page_size;
        $FormApplys = $FormApplyModel->limit($this->page_size)->offset($offset)->all();

        foreach ($FormApplys as $FormApply){
            /**
             * @var FormApply $FormApply
             */
            $res['list'][] = [
                'id'=>$FormApply->id,
                'title'=>$FormApply->title,
                'created_at'=>$FormApply->created_at,
                'name'=>$FormApply->name,
                'mobile'=>$FormApply->mobile,
                'email'=>$FormApply->email,
                'is_deal'=>$FormApply->is_deal,
                'company_name'=>$FormApply->company_name,
                'reject_mark'=>$FormApply->reject_mark ? $FormApply->reject_mark  : '',
                'number'=>$FormApply->number ? $FormApply->number  : '',
                'url'=>$FormApply->url ? $FormApply->url  : '',
                'certificate_status'=>$FormApply->certificate_status,
            ];
        }

        return $res;
    }

    /**
     * 企业列表
     * @return array|false|\yii\db\ActiveRecord[]
     */
    public function list() {
        if (!$this->validate()) {
            return false;
        }

        $query = FormApply::find()->where(['status' => 1]);
        if ($this->company_name) {
            $query = $query->andFilterWhere(['like', 'company_name', $this->company_name]);
        }
        if ($this->category_id && $this->category_id != 0) {
            $query = $query->andFilterWhere(['category_id' => $this->category_id]);
        }
        $offset = ($this->page - 1) * $this->page_size;
        $list = $query->orderBy(['created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($this->page_size)
            ->all();

        return $list;
    }

    /**
     * 企业详情
     * @return array|false
     */
    public function detail() {
        if(!$this->validate()) {
            return false;
        }

        /**
         * @var FormApply $formApply
         */
        return FormApply::find()
            ->where(['status' => 1, 'id' => $this->id])
            ->one();
    }
}