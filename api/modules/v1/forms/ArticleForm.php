<?php

namespace api\modules\v1\forms;

use yii\base\Model;
use common\enums\StatusEnum;
use common\helpers\RegularHelper;
use common\models\member\Member;
use common\models\common\SmsLog;
use common\enums\AccessTokenGroupEnum;

/**
 * Class MobileLogin
 * @package api\modules\v1\models
 * @author jianyan74 <751393839@qq.com>
 */
class ArticleForm extends Model
{
    /**
     * @var
     */
    public $article_id;

    /**
     * @var
     */
    public $class_id;

    /**
     * @var
     */
    public $type;

    /**
     * @var
     */
    public $last_id;

    /**
     * @var
     */
    public $action;

    /**
     * @var
     */
    public $limit;

    /**
     * @var
     */
    public $page;

    /**
     * @var
     */
    public $page_size;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['article_id','class_id','type'], 'required'],
            [['class_id','type','last_id','action','limit','page','page_size'],'integer'],
            ['type','in','range' => [0,1]]
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'class_id' => '类别',
        ];
    }

    public function scenarios()
    {
        return [
            'article_list' => ['class_id','page','page_size'],
            'article_info' => ['article_id','last_id','action','limit','class_id'],
            'article_like' => ['article_id','type'],
            'article_look' => ['article_id'],
        ];
    }

}