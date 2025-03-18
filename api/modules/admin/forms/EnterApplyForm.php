<?php


namespace api\modules\admin\forms;

use common\helpers\ArrayHelper;
use common\helpers\ExcelHelper;
use common\helpers\UploadHelper;
use common\models\api\FormApply;
use common\models\common\Attachment;
use common\models\common\ExpertIntent;
use common\models\common\MemberLevel;
use common\models\common\PersonalApply;
use common\sdk\alibabaCloud\Sms;
use Yii;
use yii\base\Model;
use yii\db\Expression;
use yii\web\UploadedFile;

class EnterApplyForm extends Model
{
    /**
     * 列表场景
     */
    const SCENARIO_LIST = 'scenario_list';

    /**
     * 添加编辑入会申请
     */
    const SCENARIO_STORE = 'scenario_store';

    /**
     * 处理场景
     */
    const SCENARIO_DEAL = 'scenario_deal';

    /**
     * 导出场景
     */
    const SCENARIO_EXPORT = 'scenario_export';

    /**
     * 驳回申请
     */
    const SCENARIO_REJECT = 'scenario_reject';

    /**
     * 删除申请
     */
    const SCENARIO_DEl = 'scenario_del';

    /**
     * 修改会员等级
     */
    const SCENARIO_MODIFY_LEVEL = 'scenario_modify_level';

    /**
     * 个人申请添加、修改
     */
    const SCENARIO_PERSONAL_APPLY_STORE = 'personal_apply_store';

    /**
     * 个人申请列表
     */
    const SCENARIO_PERSONAL_APPLY_LIST = 'personal_apply_list';

    /**
     * 修改个人申请分类
     */
    const SCENARIO_PERSONAL_APPLY_MODIFY_CATEGORY = 'personal_apply_modify_category';

    /**
     * 导出场景
     */
    const SCENARIO_PERSONAL_EXPORT = 'scenario_personal_export';

    /**
     * 导入场景
     */
    const SCENARIO_PERSONAL_IMPORT = 'scenario_personal_import';

    /**
     * 修改状态
     */
    const SCENARIO_STATUS_STORE='scenario_status_store';

    /**
     * @var int 查询时候的页数
     */
    public $page = 1;


    /**
     * @var int 一页的数据
     */
    public $page_size = 20;

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
    public $memeber_id = '0';

    /**
     * @var int 会员等级
     */
    public $member_level = 0;

    /**
     * @var string 小程序openid
     */
    public $open_id = '';

    /**
     * @var int 0未处理1已处理
     */
    public $is_deal = null;

    /**
     * @var int 表单id
     */
    public $enter_from_id = null;

    /**
     * @var string id集合，逗号分割，如（1,2,3,4）
     */
    public $ids = '';

    /**
     * @var string 开始时间
     */
    public $s_time = '';

    /**
     * @var string 结束时间
     */
    public $e_time = '';

    /**
     * @var string 公司名称
     */
    public $company_name = '';

    /**
     * @var string 驳回原因
     */
    public $reject_mark = '';

    /**
     * @var string 工作单位
     */
    public $employer;

    /**
     * @var string 部门
     */
    public $department;

    /**
     * @var string 专家意向id
     */
    public $expert_intent_id;

    /**
     * @var int 人才分类 1-市场监管 2-非市场监管
     */
    public $talent_classification;

    /**
     * @var string base64文件
     */
    public $excel_file;


    public $logo;

    public $address;

    public $latitude; //纬度

    public $longitude; //经度

    public $registration_time;  //报名时间

    public $introduction; //简介

    public $evaluation;  //评价  1-上市，2-荣誉，3-独角兽

    public $short_name; //简称

    public $new_address;

    public $number;

    public $url;

    public $certificate_status;

    public $category_id;

    public $main_business;

    public $banner;

    public $address_detail;

    public $mp_app_id;

    public function rules()
    {
        return [
            //列表场景
            [['page','page_size','enter_from_id','is_deal','member_level','evaluation'],'integer','on'=>[static::SCENARIO_LIST]],
            [['name','mobile','email','title','s_time','e_time','company_name'],'string','on'=>[static::SCENARIO_LIST]],

            //新增编辑场景
            [['apply_content_json', 'address_detail', 'main_business', 'banner', 'mp_app_id'],'string','on'=>[static::SCENARIO_STORE]],
            [['id','certificate_status', 'category_id'],'integer','on'=>[static::SCENARIO_STORE]],
            [['logo','number','url'],'string','on'=>[static::SCENARIO_STORE]],
            [['address'],'string','on'=>[static::SCENARIO_STORE]],
            [['short_name'],'string','on'=>[static::SCENARIO_STORE]],
            [['latitude'],'string','on'=>[static::SCENARIO_STORE]],
            [['longitude'],'string','on'=>[static::SCENARIO_STORE]],
            [['registration_time'],'string','on'=>[static::SCENARIO_STORE]],
            [['introduction'],'string','on'=>[static::SCENARIO_STORE]],
            [['evaluation'],'string','on'=>[static::SCENARIO_STORE]],
            [['id','evaluation'],'required','on'=>[static::SCENARIO_STATUS_STORE]],
            [['id','evaluation'],'integer','on'=>[static::SCENARIO_STATUS_STORE]],
            [['number'], 'unique','targetClass'=>'\common\models\api\FormApply','message'=>'该编号已被占用'],

            //处理场景
            [['ids'],'filter','filter'=>function($v){
                return $v;
            },'on'=>[static::SCENARIO_DEAL,static::SCENARIO_EXPORT,static::SCENARIO_REJECT,static::SCENARIO_DEl,static::SCENARIO_PERSONAL_EXPORT]],

            //导出场景
            [['enter_from_id','is_deal'],'integer','on'=>[static::SCENARIO_EXPORT]],
            [['name','mobile','email','title'],'string','on'=>[static::SCENARIO_EXPORT]],

            //驳回场景
            [['reject_mark'],'string','on'=>[static::SCENARIO_REJECT]],

            //修改会员等级
            [['id','member_level'],'required','on'=>[static::SCENARIO_MODIFY_LEVEL]],
            [['id','member_level'],'integer','on'=>[static::SCENARIO_MODIFY_LEVEL]],

            //新增编辑场景
            [['apply_content_json','new_address','registration_time'],'string','on'=>[static::SCENARIO_PERSONAL_APPLY_STORE]],
            [['id','status'],'integer','on'=>[static::SCENARIO_PERSONAL_APPLY_STORE]],
            ['status','in','range' => [0,1,-1],'on'=>[static::SCENARIO_PERSONAL_APPLY_STORE]],

            //个人申请列表
            [['name','employer','department'],'string','on'=>[static::SCENARIO_PERSONAL_APPLY_LIST]],
            [['expert_intent_id','page','page_size','is_deal','talent_classification'],'integer','on'=>[static::SCENARIO_PERSONAL_APPLY_LIST]],

            //个人申请修改分类
            [['id', 'talent_classification'], 'required', 'on' => [static::SCENARIO_PERSONAL_APPLY_MODIFY_CATEGORY]],

            //个人申请导出场景
            [['expert_intent_id','is_deal'],'integer','on'=>[static::SCENARIO_PERSONAL_EXPORT]],
            [['name','employer','department'],'string','on'=>[static::SCENARIO_PERSONAL_EXPORT]],

            //导入
            //[['excel_file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'xls,xlsx', 'on'=>[static::SCENARIO_PERSONAL_IMPORT]],
            [['excel_file'], 'string', 'on'=>[static::SCENARIO_PERSONAL_IMPORT]],
        ];
    }

    /**
     * @return array|false
     * @note:列表数据
     */
    public function list()
    {
        if (!$this->validate()){
            return false;
        }

        $FormApply = FormApply::find();

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $offset = ($this->page - 1) * $this->page_size;
        $query = $FormApply->andFilterWhere(['like', 'a.name', $this->name])
            ->andFilterWhere(['like', 'a.mobile', $this->mobile])
            ->andFilterWhere(['like', 'a.email', $this->email])
            ->andFilterWhere(['a.enter_from_id' => $this->enter_from_id])
            ->andFilterWhere(['a.is_deal' => $this->is_deal])
            ->andFilterWhere(['>=','a.created_at', $this->s_time])
            ->andFilterWhere(['<','a.created_at', $this->e_time])
            ->andFilterWhere(['>','a.status', -1])
            ->andFilterWhere(['a.member_level' => $this->member_level])
            ->andFilterWhere(['evaluation'=>$this->evaluation])
            ->andFilterWhere(['like', 'a.company_name', $this->company_name]);

        $list = $query->select(['a.number','a.url','a.certificate_status','a.logo','a.short_name','a.address','a.longitude','a.latitude','a.registration_time','a.introduction','a.evaluation','a.id','a.title','a.name','b.id as level_id','b.name as level_name','a.mobile','a.email','a.apply_content_json','a.is_deal','a.enter_from_id','a.status','a.created_at','a.updated_at','a.company_name','a.reject_mark'])
            ->from(FormApply::tableName() . ' AS a')
            ->leftJoin(MemberLevel::tableName(). ' AS b','a.member_level = b.id')
            ->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        foreach ($list as $key=>$item) {
            $apply_content_json=json_decode($item['apply_content_json'],true);
            foreach ($apply_content_json as $item_n) {
                if ($item_n['name'] == 'contacter_mobile'){
                    $list[$key]['contacter_mobile'] = $item_n['value'];
                }
            }
            $list[$key]['number']=(string)$list[$key]['number'];
        }
        $res['total'] = $query->count();

        $res['list'] = $list;

        $res = ArrayHelper::itemsNumber($res);

        return $res;
    }

    public function import() {
        if (!$this->validate()){
            return false;
        }

        //保存base64文件
        $filePath = \Yii::getAlias("@runtime");
        $filename = '/'.date('YmdHis').rand(1111,9999).'.xlsx';
        $path = $filePath.$filename;
        if(strstr($this->excel_file, ',')){
            $this->excel_file = explode(',', $this->excel_file);
            $this->excel_file = $this->excel_file[1];
        }
        //文件写入
        file_put_contents($path, base64_decode($this->excel_file));
        opcache_reset();

        //导入
        //$path = $this->excel_file->tempName;
        $data = ExcelHelper::import($path, 2);


        $res = true;
        foreach ($data as $val) {
            if (count($val) < 23) {
                continue;
            }
            if (!$val[5]) {
                break;
            }
            $applyContentJson = [
                ['title' => '企业入会编号', 'name' => 'number', 'value' => $val[0]],
                ['title' => '企业等级名称', 'name' => 'member_level', 'value' => $val[1]],
                ['title' => '企业营业执照或副本', 'name' => 'member_level', 'value' => $val[4]],
                ['title' => '季度估值', 'name' => 'quarter_valuation', 'value' => []],
                ['title' => '企业名称', 'name' => 'company_name', 'value' => $val[5]],
                ['title' => '企业简称', 'name' => 'short_name', 'value' => $val[6]],
                ['title' => '网站名称', 'name' => 'website_url', 'value' => $val[7]],
                ['title' => '信用代码', 'name' => 'ICP', 'value' => $val[8]],
                ['title' => '上年度GMV (亿元)', 'name' => 'trade_amount', 'value' => $val[9]],
                ['title' => '上年度营收 (亿元)', 'name' => 'revenue', 'value' => $val[10]],
                ['title' => '上年度纳税额(亿元)', 'name' => 'tax_amount', 'value' => $val[11]],
                ['title' => '员工人数', 'name' => 'staff_count', 'value' => $val[12]],
                ['title' => '党员人数', 'name' => 'gang_count', 'value' => $val[13]],
                ['title' => '用户数量', 'name' => 'user_count', 'value' => $val[14]],
                ['title' => '商家数量', 'name' => 'merchant_count', 'value' => $val[15]],
                ['title' => '企业类型', 'name' => 'company_type', 'value' => $val[16]],
                ['title' => '企业二级类型', 'name' => 'company_sub_type', 'value' => $val[17]],
                ['title' => '协会联系人姓名', 'name' => 'contacter_title', 'value' => $val[20]],
                ['title' => '协会联系人职务', 'name' => 'contacter_job', 'value' => $val[21]],
                ['title' => '协会联系人手机号', 'name' => 'contacter_mobile', 'value' => $val[22]],
            ];

            //添加数据
            $model = new FormApply();
            if(!empty($applyContentJson)) {
                foreach ($applyContentJson as $v){
                    if ($v['name'] == '_name'){
                        $model->name = $v['value'];
                    }
                    if ($v['name'] == '_mobile'){
                        $model->mobile = $v['value'];
                    }
                    if ($v['name'] == '_email'){
                        $model->email = $v['value'];
                    }
                    if ($v['name'] == 'company_name'){
                        $model->company_name = $v['value'];
                    }
                    if(!$model->email){
                        $model->email='1';
                    }
                }
            }
            $evaluationMap = ['上市'=> 1, '荣誉'=> 2, '独角兽'=> 3];
            $model->apply_content_json = json_encode($applyContentJson, JSON_UNESCAPED_UNICODE);
            $model->is_deal = 1;
            $model->title = '入会申请';
            $model->logo=$val[1];
            // $model->registration_time=$this->registration_time;
            // $model->introduction=$this->introduction;
            $model->number=$val[0];
            $model->is_deal = 0;
            $model->status = 1;
            $model->evaluation = $evaluationMap[$val[2]] ?? 1;
            $item = MemberLevel::find()->where(['name'=> $val[1]])->one();
            $model->member_level = $item ? $item->level : 1;
            if(!$model->save()) {
                $res = false;
                Yii::error($val[0].'导入失败');
            }


            // $model->apply_content_json = json_encode($applyContentJson, JSON_UNESCAPED_UNICODE);
            // $model->introduction = $val[20];
            // $model->logo = $val[3];
            // $model->registration_time = strtotime('Y-m-d H:i:s', $v[19]);
            // $model->is_deal = 1;
            // $model->title = '入会申请';
            // $model->evaluation = $val[2];
            // $model->number= $val[0];
            //
            //
            // if(!$model->save()) {
            //     $res = false;
            //     Yii::error($val[0].'导入失败');
            // }
        }

        unlink($path);
        //保存数据
        return $res;
    }


    /**
     * 添加编辑入会申请
     * @param $params
     * @return bool
     */
    public function store(){
        if (!$this->validate()){
            return false;
        }

        if(!empty($this->id)) {
            $model = FormApply::find()->where(['id' => $this->id])->one();
            $model->id = $this->id;
            //unset($params['id'],$params['member_level'],$params['name'],$params['mobile'],$params['email'],$params['company_name']);
        }else{
            $model = new FormApply();
            //unset($params['member_level'],$params['name'],$params['mobile'],$params['email'],$params['company_name']);
        }

        $applyContentJson = json_decode($this->apply_content_json, true);
        foreach ($applyContentJson as $v){
            if ($v['name'] == '_name'){
                $model->name = $v['value'];
            }
            if ($v['name'] == '_mobile'){
                $model->mobile = $v['value'];
            }
            if ($v['name'] == '_email'){
                $model->email = $v['value'];
            }
            if ($v['name'] == 'company_name'){
                $model->company_name = $v['value'];
            }
            if ($v['name'] == 'member_level'){
                $model->member_level = (int)$v['value'];
            }
        }

        /*$model->member_level = $this->member_level;
        $model->name = $this->name;
        $model->mobile = $this->mobile;
        $model->email = $this->email;
        $model->company_name = $this->company_name;*/
        if(!$model->email){
            $model->email='1';
        }
        $model->apply_content_json = $this->apply_content_json;
        $model->is_deal = 1;
        $model->title = '入会申请';
        $model->logo=$this->logo;
        $model->address=$this->address;
        $model->longitude=$this->longitude;
        $model->latitude=$this->latitude;
        $model->registration_time=$this->registration_time;
        $model->introduction=$this->introduction;
        $model->evaluation=$this->evaluation;
        $model->number=$this->number;
        $model->category_id=$this->category_id ?? 0;
        $model->main_business=$this->main_business ?? '';
        $model->banner = $this->banner ?? '';
        $model->address_detail = $this->address_detail ?? '';
        $model->mp_app_id=$this->mp_app_id ?? '';
//        $model->url=$this->url;
//        $model->certificate_status=$this->certificate_status;
        if(!$model->save()) {
            return false;
        }

        return true;
    }

    /**
     * 修改状态
     * @return false|void
     */
    public function statusStore(){
        if (!$this->validate()){
            return false;
        }

        if(!empty($this->id)) {
            $model = FormApply::find()->where(['id' => $this->id])->one();
//            $model->id = $this->id;
            $model->evaluation=$this->evaluation;
            if(!$model->save()) {
                return false;
            }

            return true;
        }
        return false;
    }

    /**
     * @return false
     * @note:处理申请
     */
    public function deal()
    {
        if (!$this->validate()){
            return false;
        }

        $ids = explode(',',$this->ids);
        if (!$FormApply = FormApply::find()->where(['id' => $ids])){
            $this->addError("EnterApplyForm",'数据不存在');
            return false;
        }

        $Transaction = \Yii::$app->db->beginTransaction();

//        $FormApply->is_deal = $FormApply::IS_DEAL;
        if (!FormApply::updateAll(['is_deal'=>FormApply::IS_DEAL,'updated_at'=>time()],['id'=>$ids])){
            $Transaction->rollBack();
            \Yii::error("处理失败");
            $this->addError("EnterApplyForm",'处理失败');
            return false;
        }
        $Transaction->commit();
        //发送短信通知
        $res = Sms::sendSms([
            'phoneNumbers' => $FormApply->mobile,
            'signName' => Yii::$app->params['sms']['template']['pass']['sign'],
            'templateCode' => Yii::$app->params['sms']['template']['pass']['code'],
        ]);
        if(!$res) {
            Yii::error("{$FormApply->mobile}申请入会报名审核异常");
        }

        return true;
    }

    /**
     * @return false
     * @note:驳回申请
     */
    public function reject()
    {
        if (!$this->validate()){
            return false;
        }

        $ids = explode(',',$this->ids);
        if (!$FormApply = FormApply::find()->where(['id' => $ids])){
            $this->addError("EnterApplyForm",'数据不存在');
            return false;
        }

        $Transaction = \Yii::$app->db->beginTransaction();

//        $FormApply->is_deal = $FormApply::IS_DEAL;
        if (!FormApply::updateAll(['is_deal'=>FormApply::IS_REJECT,'updated_at'=>time(),'reject_mark'=>$this->reject_mark],['id'=>$ids])){
            $Transaction->rollBack();
            \Yii::error("处理失败");
            $this->addError("EnterApplyForm",'处理失败');
            return false;
        }
        $Transaction->commit();

        //发送短信通知
        $res = Sms::sendSms([
            'phoneNumbers' => $FormApply->mobile,
            'signName' => Yii::$app->params['sms']['template']['refund']['sign'],
            'templateCode' => Yii::$app->params['sms']['template']['refund']['code'],
            'templateParam' => json_encode(['phone' => '0571-89769991'], JSON_UNESCAPED_UNICODE)
        ]);
        if(!$res) {
            Yii::error("{$FormApply->mobile}申请入会报名审核异常");
        }
        return true;
    }

    /**
     * @return bool
     * @throws \yii\db\Exception
     * @note:申请删除
     */
    public function del()
    {
        if (!$this->validate()){
            return false;
        }

        $ids = explode(',',$this->ids);
        if (!$FormApply = FormApply::find()->where(['id' => $ids])){
            $this->addError("EnterApplyForm",'数据不存在');
            return false;
        }

        $Transaction = \Yii::$app->db->beginTransaction();

//        $FormApply->is_deal = $FormApply::IS_DEAL;
        if (!FormApply::updateAll(['status'=>FormApply::STATUS_DEL,'updated_at'=>time()],['id'=>$ids])){
            $Transaction->rollBack();
            \Yii::error("处理失败");
            $this->addError("EnterApplyForm",'处理失败');
            return false;
        }
        $Transaction->commit();
        return true;
    }

    /**
     * @return bool
     * @note:导出申请
     */
    public function export()
    {
        if (!$this->validate()){
            return false;
        }

        $ids = $this->ids ? explode(',',$this->ids) : null;

        $FormApply = FormApply::find()
        ->andFilterWhere(['like', 'name', $this->name])
        ->andFilterWhere(['like', 'mobile', $this->mobile])
        ->andFilterWhere(['like', 'email', $this->email])
        ->andFilterWhere(['enter_from_id' => $this->enter_from_id])
        ->andFilterWhere(['is_deal' => $this->is_deal]);

        if ($ids){
            $FormApply = FormApply::find()->andFilterWhere(['id' => $ids]);
        }
        if (!$list = $FormApply->asArray()->all()){
            $this->addError("EnterApplyForm",'数据不存在');
            return false;
        }

        $exportArr = [];
        $header = [];

        /** 组装header */
        $arr = json_decode($list[0]['apply_content_json'],true);
        foreach ($arr as $v){
            $header[] = [$v['title'],$v['name']];
        }
        /** end */

        /** 组装list */
        foreach ($list as $v){
            $arr = json_decode($v['apply_content_json'],true);
            foreach ($arr as $vv){
//                $header[] = [$vv['title'],$vv['name']];
                if ($vv['name'] === 'address') {
                    $address = json_decode($vv['value'], true);
                    $tmp[$vv['name']] = sprintf("%s%s%s", $address['province'], $address['city'], $address['area']);
                }else{
                    $tmp[$vv['name']] = $vv['value'] ?? '';
                }
            }
            array_push($exportArr,$tmp);
        }
        /** end */
        return ExcelHelper::exportData($exportArr,$header,'申请记录_'.time());
    }

    public function modifyLevel(){
        if (!$this->validate()){
            return false;
        }

        $model = FormApply::find()->where(['id' => $this->id])->one();
        if(empty($model)) {
            return false;
        }
        $model->member_level = $this->member_level;
        if(!$model->save()) {
            return false;
        }
        return true;
    }


    /**
     * 添加编辑个人申请
     * @param $params
     * @return bool
     */
    public function personalApplyStore(){
        if (!$this->validate()){
            return false;
        }

        if(!empty($this->id)) {
            /**
             * @var PersonalApply $model
             */
            $model = PersonalApply::find()->where(['id' => $this->id])->one();
            $model->id = $this->id;
            $model->status = $this->status;
        }else{
            $model = new PersonalApply();
        }

        $applyContentJson = json_decode($this->apply_content_json, true);
        if(!empty($applyContentJson)) {
            foreach ($applyContentJson as $v){
                if ($v['name'] == 'name' && isset($v['value'])){
                    $model->name = $v['value'];
                }
                if ($v['name'] == 'mobile' && isset($v['value'])){
                    $model->mobile = $v['value'];
                }
                if ($v['name'] == 'sex' && isset($v['value'])){
                    $model->sex = (int)$v['value'];
                }
                if ($v['name'] == 'employer' && isset($v['value'])){
                    $model->employer = $v['value'];
                }
                if ($v['name'] == 'department' && isset($v['value'])){
                    $model->department = $v['value'];
                }
                if ($v['name'] == 'expert_intent_id' && isset($v['value'])){
                    $model->expert_intent_id = $v['value'];
                }
                if ($v['name'] == 'score' && isset($v['value'])) {
                    $model->score = (int)$v['value'];
                }
                if($v['name'] == 'talent_classification' && isset($v['value'])) {
                    $model->talent_classification = (int)$v['value'];
                }
                if($v['name'] == 'Introduction' && isset($v['value'])) {
                    $model->introduction = (string)$v['value'];
                }
//                if($v['name'] == 'registration_time' && isset($v['value'])) {
//                    $model->registration_time = (string)$v['value'];
//                }
//                if($v['name'] == 'new_address' && isset($v['value'])) {
//                    $model->new_address = (string)$v['value'];
//                }
            }
        }


        $model->registration_time=$this->registration_time;
        $model->new_address=$this->new_address;
        $model->apply_content_json = $this->apply_content_json;
        $model->is_deal = 1;

        if(!$model->save()) {
            return false;
        }

        return true;
    }

    public function personalApplyList(){
        if (!$this->validate()){
            return false;
        }

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $offset = ($this->page - 1) * $this->page_size;
        $query = PersonalApply::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'employer', $this->employer])
            ->andFilterWhere(['like', 'department', $this->department])
            //->andFilterWhere(['expert_intent_id' => $this->expert_intent_id])
            ->andFilterWhere(['is_deal' => $this->is_deal])
            ->andFilterWhere(['talent_classification' => $this->talent_classification]);
        if(!empty($this->expert_intent_id)) {
            $query = $query->where(new Expression('FIND_IN_SET(:expert_id, expert_intent_id)'))
                ->addParams([':expert_id' => $this->expert_intent_id]);
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

    /**
     * 修改个人申请分类
     * @return bool
     */
    public function modifyCategory() {
        if(!$this->validate()) {
            return false;
        }
        $model = PersonalApply::find()->where(['id' => $this->id])->one();
        if(empty($model)) {
            return false;
        }
        /**
         * @var PersonalApply $model
         */
        $model->talent_classification = $this->talent_classification;
        $applyJson = !empty($model->apply_content_json) ? json_decode($model->apply_content_json, true) : '';
        if(!empty($applyJson)) {
            $keysArr = ArrayHelper::getColumn($applyJson, 'name');
            if(!in_array('talent_classification', $keysArr)) {
                $applyJson[] = ['title' => '人才分类', 'name' => 'talent_classification', 'value' => $this->talent_classification];
            }
            foreach ($applyJson as $key => $val) {
                if($val['name'] == 'talent_classification') {
                    $applyJson[$key]['value'] = $this->talent_classification;
                }
            }
            $model->apply_content_json = json_encode($applyJson, JSON_UNESCAPED_UNICODE);
        }

        if(!$model->save()) {
            return false;
        }
        return true;
    }

    /**
     * @return bool
     * @note:导出申请
     */
    public function personalExport()
    {
        if (!$this->validate()){
            return false;
        }

        $ids = $this->ids ? explode(',',$this->ids) : null;

        $query = PersonalApply::find()
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'employer', $this->employer])
            ->andFilterWhere(['like', 'department', $this->department])
            ->andFilterWhere(['expert_intent_id' => $this->expert_intent_id])
            ->andFilterWhere(['is_deal' => $this->is_deal])
            ->andFilterWhere(['talent_classification' => $this->talent_classification]);

        if ($ids){
            $query = PersonalApply::find()->andFilterWhere(['id' => $ids]);
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
            $header[] = [$v['title'],$v['name']];
        }
        /** end */

        /** 组装list */
        foreach ($list as $v){
            $arr = json_decode($v['apply_content_json'],true);
            foreach ($arr as $vv){
//                $header[] = [$vv['title'],$vv['name']];
                if ($vv['name'] == 'address') {
                    $address = json_decode($vv['value'],true);
                    $tmp[$vv['name']] = sprintf("%s%s%s", $address['province']??'', $address['city']??'', $address['area']??'');
                }else{
                    $tmp[$vv['name']] = $vv['value']??'';
                }
            }
            array_push($exportArr,$tmp);
        }
        /** end */
        return ExcelHelper::exportData($exportArr,$header,'个人申请记录_'.time());
    }

    public function personalImport() {
        if (!$this->validate()){
            return false;
        }

        //保存base64文件
        $filePath = \Yii::getAlias("@runtime");
        $filename = '/'.date('YmdHis').rand(1111,9999).'.xlsx';
        $path = $filePath.$filename;
        if(strstr($this->excel_file, ',')){
            $this->excel_file = explode(',', $this->excel_file);
            $this->excel_file = $this->excel_file[1];
        }
        //文件写入
        file_put_contents($path, base64_decode($this->excel_file));

        //导入
        //$path = $this->excel_file->tempName;
        $data = ExcelHelper::import($path, 2);


        $res = true;
        $expertIntent = ExpertIntent::find()->select(['id','title'])->where(['status' => 1])->asArray()->all();
        $expert = ArrayHelper::arrayKey($expertIntent, 'title');
        foreach ($data as $val) {
            $expertArr = explode(',', $val[9]);
            $expertStr = '';
            foreach ($expertArr as $v) {
                if(array_key_exists($v,$expert)) {
                    $expertStr .= $expert[$v]['id'].',';
                }
            }
            switch ($val[1]) {
                case '男':
                    $sex = 1;
                    break;
                case '女':
                    $sex = 2;
                    break;
                default:
                    $sex = 0;
            }
            $applyContentJson = [
                ['title' => '姓名', 'name' => 'name', 'value' => $val[0]],
                ['title' => '性别', 'name' => 'sex', 'value' => $sex],
                ['title' => '政治面貌', 'name' => 'political_status', 'value' => $val[2]],
                ['title' => '参加工作日期', 'name' => 'work_time', 'value' => $val[3]],
                ['title' => '身份证号码', 'name' => 'id_number', 'value' => $val[4]],
                ['title' => '工作单位', 'name' => 'employer', 'value' => $val[5]],
                ['title' => '具体工作部门或所', 'name' => 'department', 'value' => $val[6]],
                ['title' => '现任职务', 'name' => 'position', 'value' => $val[7]],
                ['title' => '入库意向', 'name' => 'inventory_ntention', 'value' => $val[8]],
                ['title' => '专家库意向', 'name' => 'expert_intent_id', 'value' => trim($expertStr,',')],
                ['title' => '固话', 'name' => 'telephone', 'value' => $val[10]],
                ['title' => '移动电话', 'name' => 'mobile', 'value' => $val[11]],
                ['title' => '传真', 'name' => 'fax', 'value' => $val[12]],
                ['title' => '电子邮箱', 'name' => 'email', 'value' => $val[13]],
                ['title' => 'QQ', 'name' => 'QQ', 'value' => $val[14]],
                ['title' => '微信号', 'name' => 'wechat', 'value' => $val[15]],
                ['title' => '毕业院校', 'name' => 'graduated_school', 'value' => $val[16]],
                ['title' => '学历及专业', 'name' => 'profession', 'value' => $val[17]],
                ['title' => '工作经历', 'name' => 'work_experience', 'value' => $val[18]],
                ['title' => '所获荣誉以及网监专长', 'name' => 'honor', 'value' => $val[19]],
                ['title' => '已有职业资格', 'name' => 'professional_qualification', 'value' => $val[20]],
                ['title' => '通讯地址', 'name' => 'address', 'value' => $val[21]],
                ['title' => '录入人', 'name' => 'input_person', 'value' => $val[22]],
                ['title' => '录入时间', 'name' => 'input_time', 'value' => $val[23]],
                ['title' => '录入单位', 'name' => 'input_unit', 'value' => $val[24]],
            ];

            //添加数据
            if(!empty($applyContentJson)) {
                $model = new PersonalApply();
                foreach ($applyContentJson as $v){
                    if ($v['name'] == 'name'){
                        $model->name = $v['value'];
                    }
                    if ($v['name'] == 'mobile'){
                        $model->mobile = $v['value'];
                    }
                    $model->sex = $sex;
                    if ($v['name'] == 'employer'){
                        $model->employer = $v['value'];
                    }
                    if ($v['name'] == 'department'){
                        $model->department = $v['value'];
                    }
                    if ($v['name'] == 'expert_intent_id'){
                        $model->expert_intent_id = $v['value'];
                    }
                    if ($v['name'] == 'score') {
                        $model->score = (int)$v['value'];
                    }
                    if($v['name'] == 'talent_classification') {
                        $model->talent_classification = (int)$v['value'];
                    }
                    if($v['name'] == 'Introduction') {
                        $model->introduction = (int)$v['value'];
                    }
                }
            }


            $model->apply_content_json = json_encode($applyContentJson, JSON_UNESCAPED_UNICODE);
            $model->is_deal = 1;

            if(!$model->save()) {
                $res = false;
                Yii::error($val[0].'导入失败');
            }
        }

        unlink($path);
        //保存数据
        return $res;
    }
}
