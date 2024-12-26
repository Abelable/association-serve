<?php
/**
 * User: sun
 * Date: 2021/9/16
 * Time: 4:12 下午
 */

namespace console\controllers;


use common\models\api\FormApply;
use yii\console\Controller;

class FormApplyController extends Controller
{

    /**
     * 修改公司名称
     */
    public function actionFixCompanyName(){
        $lastId = 0;
        $limit = 100;

        $FormApplyModel = FormApply::find();
        do{
            $FormApplys = $FormApplyModel->where(['>','id',$lastId])->limit($limit)->all();


            // [{"title":"企业名称","name":"company_name","value":"测试"},{"title":"企业简称","name":"abbreviation","value":"测试"},{"title":"企业地址","name":"address","value":"测试"},{"title":"邮政编码","name":"post_code","value":"2222222"},{"title":"网站（网店）网址","name":"website_url","value":"测试"},{"title":"企业员工平均年龄","name":"average_age","value":"22"},{"title":"统一社会信用代码","name":"credit_code","value":"646649494"},{"title":"ICP备案号","name":"ICP","value":"646494"},{"title":"企业类型","name":"company_type","value":"第三方平台"},{"title":"网站电子商务类型","name":"website_type","value":"B2B"},{"title":"交易商品（服务）","name":"trade_commodity","value":"测试"},{"title":"上年交易笔数","name":"trade_count","value":"1"},{"title":"上年交易额","name":"trade_amount","value":"1"},{"title":"姓名","name":"_name","value":"测试"},{"title":"性别","name":"gender","value":"1"},{"title":"职务","name":"job_title","value":"测试"},{"title":"身份证号","name":"identity_number","value":"332555555555555555"},{"title":"学历","name":"education","value":"测试"},{"title":"政治面貌","name":"political_status","value":"测试"},{"title":"手机号","name":"_mobile","value":"18655555555"},{"title":"邮箱","name":"_email","value":"11@qq.com"},{"title":"协会联系人姓名","name":"contacter_name","value":"测试"},{"title":"协会联系人职务","name":"contacter_job_title","value":"测试"},{"title":"协会联系人手机号","name":"contacter_mobile","value":"186555555"},{"title":"协会联系人邮箱","name":"contacter_email","value":"33@qq.com"},{"title":"企业营业执照或副本","name":"license","value":"http://img.ubo.vip/uploads/9732955_612ef55d1eddb.jpeg"},{"title":"注册会员数量","name":"member_count","value":0},{"title":"平台网站内经营者数量","name":"operator_count","value":0}]
            foreach ($FormApplys as $FormApply){
                /**
                 * @var FormApply $FormApply
                 */
                $this->stdout("开始处理id:{$FormApply->id}");

                if ($FormApply->id > $lastId){
                    $lastId = $FormApply->id;
                }

                $apply_content_jsonArr = json_decode($FormApply->apply_content_json,true);
                $company_name = '';


                foreach ($apply_content_jsonArr as $item){
                    if ($item['name'] == 'company_name'){
                        $company_name = $item['value'];
                        break;
                    }
                }

                $this->stdout("id:{$FormApply->id} 企业名称:{$company_name}");

                if (!$company_name){
                    $this->stderr("id:{$FormApply->id} 企业名称为空");
                    continue;
                }
                $FormApply->company_name = $company_name;
                if (!$FormApply->save()){
                    $this->stderr("id:{$FormApply->id} 更新失败 res:".json_encode($FormApply->errors,JSON_UNESCAPED_UNICODE));
                }

            }
        }while($FormApplys);
    }


    public function stderr($string){
        parent::stderr($string."\r\n");
    }

    public function stdout($string){
        parent::stdout($string."\r\n");
    }
}