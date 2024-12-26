<?php


namespace api\modules\admin\controllers;


use api\modules\admin\forms\EnterApplyForm;
use common\helpers\ExcelHelper;
use common\helpers\ResultHelper;
use common\models\api\FormApply;
use common\models\common\Certificate;
use common\models\common\ExpertIntent;
use common\models\common\MemberLevel;
use common\models\common\PersonalApply;
use yii\web\UploadedFile;

class EnterApplyController extends BaseController
{
    public $authMethod = ['export', 'personal-export','data-person','industry-distribution','total-person','total-turnover'];



    public function actionDataPerson(){
        $person=PersonalApply::find()->where(['status'=>1,'is_deal'=>1])->asArray()->all();

        $person_type_num=[
            1=>0,
            2=>0,
            3=>0,
            4=>0,
            5=>0
        ];
        foreach ($person as $key=>$value){

            $expert_intent_ids=explode(',',$value['expert_intent_id']);
            foreach ($expert_intent_ids as $expert_intent_id) {
                $person_type_num[$expert_intent_id]++;
            }
        }

        $person_type_name_arr=[
            1=>'重大项目组',
            2=>'课题调研组',
            3=>'技术支持组',
            4=>'政策法规组',
            5=>'案件执法组'
        ];

        foreach ($person_type_num as $key=>$item) {
            $temp['area']=$person_type_name_arr[$key];
            $temp['pv']=$item;
            $res[]=$temp;
        }

        echo json_encode($res);
        die();
    }


    //行业分布
    public function actionIndustryDistribution(){

        $name=\Yii::$app->request->get('name');
        $person=FormApply::find()->where(['status'=>1,'is_deal'=>1])->asArray()->all();

        $count_num=array();
        foreach ($person as $personItem) {
            $json_data=json_decode($personItem['apply_content_json'],true);
            foreach ($json_data as $json_datum) {
                    if($json_datum['name']==$name){

                        $type_value=explode(',',$json_datum['value']);
                        foreach ($type_value as $item) {
                            $count_num[]=$item;
                        }
                    }
                }

        }

        $res_data=array_count_values($count_num);

        foreach ($res_data as $key=>$res_datum) {
            $temp['area']=$key;
            $temp['pv']=$res_datum;
            $res[]=$temp;
        }

        echo json_encode($res);
        die();
    }


    //员工数
    public function actionTotalPerson(){

//        $name=\Yii::$app->request->get('name');
        $person=FormApply::find()->where(['status'=>1,'is_deal'=>1])->asArray()->all();

        $count_num=array(
            [
              'content'=>'0-10',
              'value'=>0
            ],
            [
                'content'=>'10-1百',
                'value'=>0
            ],
            [
                'content'=>'1百-1千',
                'value'=>0
            ],
            [
                'content'=>'1千-1万',
                'value'=>0
            ],
            [
                'content'=>'1万以上',
                'value'=>0
            ],

        );
        foreach ($person as $personItem) {
            $json_data=json_decode($personItem['apply_content_json'],true);
            foreach ($json_data as $json_datum) {
                if($json_datum['name']=='staff_count'){

                    switch ($json_datum['value']){
                        case $json_datum['value']<=10:
                            $count_num[0]['value']++;
                            break;
                        case $json_datum['value']<=100:
                            $count_num[1]['value']++;
                            break;
                        case $json_datum['value']<=1000:
                            $count_num[2]['value']++;
                            break;
                        case $json_datum['value']<=10000:
                            $count_num[3]['value']++;
                            break;
                        case $json_datum['value']>100000:
                            $count_num[4]['value']++;
                            break;

                    }

//                    $type_value=explode(',',$json_datum['value']);
//                    foreach ($type_value as $item) {
//                        $count_num[]=$item;
//                    }
                }
            }

        }


        echo json_encode($count_num);
        die();
    }




    //营收数
    public function actionTotalTurnover(){

//        $name=\Yii::$app->request->get('name');
        $person=FormApply::find()->where(['status'=>1,'is_deal'=>1])->asArray()->all();

        $count_num=array(
            [
                'content'=>'0-1百万',
                'value'=>0
            ],
            [
                'content'=>'1百万-1亿',
                'value'=>0
            ],
            [
                'content'=>'1亿-1百亿',
                'value'=>0
            ],
            [
                'content'=>'1百亿-1千亿',
                'value'=>0
            ],
            [
                'content'=>'1千亿以上',
                'value'=>0
            ],

        );
        foreach ($person as $personItem) {
            $json_data=json_decode($personItem['apply_content_json'],true);
            foreach ($json_data as $json_datum) {
                if($json_datum['name']=='trade_amount'){

                    switch ($json_datum['value']){
                        case $json_datum['value']<=0.01:
                            $count_num[0]['value']++;
                            break;
                        case $json_datum['value']<=1:
                            $count_num[1]['value']++;
                            break;
                        case $json_datum['value']<=100:
                            $count_num[2]['value']++;
                            break;
                        case $json_datum['value']<=1000:
                            $count_num[3]['value']++;
                            break;
                        case $json_datum['value']>1000:
                            $count_num[4]['value']++;
                            break;

                    }

//                    switch ($json_datum['value']){
//                        case $json_datum['value']<=1000000:
//                            $count_num[0]['value']++;
//                            break;
//                        case $json_datum['value']<=100000000:
//                            $count_num[1]['value']++;
//                            break;
//                        case $json_datum['value']<=10000000000:
//                            $count_num[2]['value']++;
//                            break;
//                        case $json_datum['value']<=100000000000:
//                            $count_num[3]['value']++;
//                            break;
//                        case $json_datum['value']>100000000000:
//                            $count_num[4]['value']++;
//                            break;
//
//                    }

//                    $type_value=explode(',',$json_datum['value']);
//                    foreach ($type_value as $item) {
//                        $count_num[]=$item;
//                    }
                }
            }

        }


        echo json_encode($count_num);
        die();
    }

    /**
     * @return array|mixed
     * @note:列表数据
     */
    public function actionList()
    {
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_LIST);
        // 加载数据
        $EnterApplyForm->load(\Yii::$app->request->get(),'');
        if (!$res = $EnterApplyForm->list()){
            return  ResultHelper::json('422',"获取列表失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 导入个人申请
     * @return array|mixed
     */
    public function actionImport() {
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_PERSONAL_IMPORT);
        $EnterApplyForm->load(\Yii::$app->request->post(),'');
        if (\Yii::$app->request->isPost) {
            //$EnterApplyForm->excel_file = UploadedFile::getInstanceByName('excel_file');
            if (!$res = $EnterApplyForm->import()){
                return  ResultHelper::json('422',"导入失败",$EnterApplyForm->errors);
            }
            return  ResultHelper::json('200',"success",[]);
        }


    }

    /**
     * 保存入会申请
     * @return array|mixed
     */
    public function actionStore(){
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_STORE);
        // 加载数据
        $EnterApplyForm->load(\Yii::$app->request->post(),'');
        if (!$res = $EnterApplyForm->store()){
//            return  ResultHelper::json('422',"保存失败",$EnterApplyForm->errors);
//            var_dump($EnterApplyForm->errors);
//            die();
            return  ResultHelper::json('422',"保存失败".($EnterApplyForm->errors)['number'][0]??'',[]);
//            return  ResultHelper::json('422',json_encode($EnterApplyForm->errors),[]);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 修改状态
     * @return array|mixed
     */
    public function actionEvaluationStore(){
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_STATUS_STORE);
        // 加载数据
        $EnterApplyForm->load(\Yii::$app->request->post(),'');
        if (!$res = $EnterApplyForm->statusStore()){
            return  ResultHelper::json('422',"保存失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * @note:处理申请
     */
    public function actionDeal()
    {
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_DEAL);
        $EnterApplyForm->load(\Yii::$app->request->post(),'');

        if (!$res = $EnterApplyForm->deal()){
            return  ResultHelper::json('422',"处理失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }

    /**
     * @note:驳回申请
     */
    public function actionReject()
    {
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_REJECT);
        $EnterApplyForm->load(\Yii::$app->request->post(),'');

        if (!$res = $EnterApplyForm->reject()){
            return  ResultHelper::json('422',"处理失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }

    /**
     * @return array|mixed
     * @note:申请删除
     */
    public function actionDel()
    {
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_DEl);
        $EnterApplyForm->load(\Yii::$app->request->post(),'');

        if (!$res = $EnterApplyForm->del()){
            return  ResultHelper::json('422',"处理失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }

    /**
     * @return array|mixed
     * @note:导出申请
     */
    public function actionExport()
    {
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_EXPORT);
        $EnterApplyForm->load(\Yii::$app->request->get(),'');

        if (!$res = $EnterApplyForm->export()){
            return  ResultHelper::json('422',"导出失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }

    /**
     * 获取会员等级
     * @return array|mixed
     */
    public function actionLevelInfo(){
        $data = MemberLevel::find()->select(['id', 'level','name'])->all();
        return  ResultHelper::json('200',"success",$data);
    }

    /**
     * 编辑会员等级
     * @return array|mixed
     */
    public function actionModifyLevel(){
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_MODIFY_LEVEL);
        $EnterApplyForm->load(\Yii::$app->request->post(),'');

        if (!$res = $EnterApplyForm->modifyLevel()){
            return  ResultHelper::json('422',"修改失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }


    /**
     * 保存个人申请
     * @return array|mixed
     */
    public function actionPersonalApplyStore(){
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_PERSONAL_APPLY_STORE);
        // 加载数据
        $EnterApplyForm->load(\Yii::$app->request->post(),'');
        if (!$res = $EnterApplyForm->personalApplyStore()){
            return  ResultHelper::json('422',"保存失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 获取专家意向列表
     * @return array|mixed
     */
    public function actionExpertIntent() {
        $data = ExpertIntent::find()->where(['status' => 1])->asArray()->all();
        return  ResultHelper::json('200',"success",$data);
    }

    /**
     * 个人申请列表
     * @return array|mixed
     */
    public function actionPersonalApplyList() {
        $form = new EnterApplyForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_PERSONAL_APPLY_LIST);
        // 加载数据
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->personalApplyList()){
            return  ResultHelper::json('422',"保存失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * 修改个人申请分类
     * @return array|mixed
     */
    public function actionModifyCategory() {
        $form = new EnterApplyForm();
        // 设置场景
        $form->setScenario($form::SCENARIO_PERSONAL_APPLY_MODIFY_CATEGORY);
        // 加载数据
        $form->load(\Yii::$app->request->post(),'');
        if (!$res = $form->modifyCategory()){
            return  ResultHelper::json('422',"保存失败",$form->errors);
        }
        return  ResultHelper::json('200',"success",$res);
    }


    /**
     * @return array|mixed
     * @note:导出个人申请
     */
    public function actionPersonalExport()
    {
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_PERSONAL_EXPORT);
        $EnterApplyForm->load(\Yii::$app->request->get(),'');

        if (!$res = $EnterApplyForm->personalExport()){
            return  ResultHelper::json('422',"导出失败",$EnterApplyForm->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }

    /**
     * 导入个人申请
     * @return array|mixed
     */
    public function actionPersonalImport() {
        $EnterApplyForm = new EnterApplyForm();
        // 设置场景
        $EnterApplyForm->setScenario($EnterApplyForm::SCENARIO_PERSONAL_IMPORT);
        $EnterApplyForm->load(\Yii::$app->request->post(),'');
        if (\Yii::$app->request->isPost) {
            //$EnterApplyForm->excel_file = UploadedFile::getInstanceByName('excel_file');
            if (!$res = $EnterApplyForm->personalImport()){
                return  ResultHelper::json('422',"导入失败",$EnterApplyForm->errors);
            }
            return  ResultHelper::json('200',"success",[]);
        }


    }

    /**
     * 新增编辑证书
     * @return array|mixed
     */
    public function actionSaveCertificate(){
        $post_data=\Yii::$app->request->post();

        if(isset($post_data['id'])){
            $model=FormApply::findOne($post_data['id']);
        }

        $model['certificate_status']=$post_data['certificate_status']??0;
        if(isset($post_data['url'])){
            $model['url']=$post_data['url'];
        }

        if(!$model->save()) {
            return  ResultHelper::json('422',"失败",$model->errors);
        }
        return  ResultHelper::json('200',"success",[]);
    }


    /**
     * 获取最大编号+1
     * @return array|mixed
     */
    public function actionGetCertificateNumber(){
//       $number=FormApply::find()->max('number')+1;
//       $res['number']=(string)$number;
//        return  ResultHelper::json('200',"success",$res);


        FormApply::updateAll(['number'=>null],['status'=>-1]);

        $data=FormApply::find()->select('number')->where(['>','number','0'])->orderBy('number ASC')->all();
        $start=1;
        if($data){
            foreach ($data as $datum) {

                if(($datum->number)!=($start)){
                    break;
                }
                $start++;
            }
        }else{
            $res['number']='1';
            return  ResultHelper::json('200',"success",$res);
        }

        $res['number']=(string)($start);
        return  ResultHelper::json('200',"success",$res);
    }

    /**
     * @return void
     */
    public function actionGetCertificate(){
        $post_data=\Yii::$app->request->post();
//        $apply_id=$post_data['apply_id'];
        $data=Certificate::findOne(['apply_id'=>$post_data['apply_id']]);
        return  ResultHelper::json('200',"success",$data);
    }

}
