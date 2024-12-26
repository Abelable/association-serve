<?php


namespace api\modules\v1\forms;


use common\helpers\ArrayHelper;
use common\models\common\ClassRoom;
use common\models\common\ClassRoomCollect;
use common\models\common\ClassRoomLike;
use common\models\common\ClassRoomTag;
use common\models\common\ClassRoomTagMap;
use common\service\RedisKeyMap;
use yii\base\Model;

class ClassRoomForm extends Model
{
    /**
     * 网商课堂列表
     */
    const SCENARIO_CLASS_ROOM_LIST = 'class_room_list';

    /**
     * 网商课堂列表
     */
    const SCENARIO_CLASS_ROOM_DETAIL = 'class_room_detail';

    /**
     * 视频继续观看，密码验证
     */
    const SCENARIO_KEEP_WATCH = 'keep_watch';

    public $page = 1;

    public $page_size = 15;

    /**
     * @var string 视频标题
     */
    public $title;

    /**
     * @var int 视频id
     */
    public $id;

    /**
     * @var int 观看密码
     */
    public $password;

    private $redis;

    public $uid;

    public function rules()
    {
        return [
            //公共参数
            [['page', 'page_size'], 'integer'],

            //网商课堂列表
            [['title'],'string','on'=>[static::SCENARIO_CLASS_ROOM_LIST]],
            //网商课堂详情
            [['id'],'required', 'on' => static::SCENARIO_CLASS_ROOM_DETAIL],
            [['id'],'integer', 'on' => static::SCENARIO_CLASS_ROOM_DETAIL],
            //继续观看
            [['id', 'password'],'required', 'on' => static::SCENARIO_KEEP_WATCH],
            ['password', 'validPassword', 'on' => static::SCENARIO_KEEP_WATCH]
        ];
    }

    public function init()
    {
        $this->redis = \Yii::$app->redis;
        $this->uid = \Yii::$app->user->id;
    }

    /** -------------------验证函数-------------------------- */

    /**
     * 验证试看课堂视频密码
     * @return false
     */
    public function validPassword(){
        $classRoom = ClassRoom::findOne(['status' => 1, 'id' => $this->id]);
        if(!$classRoom) {
            $this->addError('valid_password', '课堂视频不存在或已删除');
            return false;
        }
        if($classRoom->is_try == 0) {
            $this->addError('valid_password', '课堂视频不是试看视频');
            return false;
        }

        //判断输错是否到达5次
        $redisKey = RedisKeyMap::build(RedisKeyMap::KEEP_WATCH_WRONG_PASS,[$this->uid, $this->id]);

        if($this->password != $classRoom->password) {
            //记录次数
            if(!is_numeric($num = \Yii::$app->redis->get($redisKey))){
                \Yii::$app->redis->setex($redisKey, 3600, 1);
            }else{
                \Yii::$app->redis->incr($redisKey);
            }

            if($num && $num = 5) {
                $this->addError('valid_password', '错误，当前已输入错误5次，已被锁定，请稍后再试');
                return false;
            }
            if($num && $num > 5) {
                $this->addError('valid_password', '已被锁定，请稍后再试');
                return false;
            }

            $this->addError('valid_password', '密码错误');
            return false;
        }

        return true;
    }

    /**
     * 视频列表
     * @return array|false|\yii\db\ActiveRecord[]
     */
    public function list() {
        if (!$this->validate()) {
            return false;
        }

        $query = ClassRoom::find()
            ->with([
                'author' => function($query) {
                    $query->where(['status' => 1]);
                }
            ])
            ->where(['status' => 1])
            ->andFilterWhere(['like','title',$this->title]);
        $offset = ($this->page - 1) * $this->page_size;
        $list = $query->orderBy(['sort' => SORT_DESC, 'created_at' => SORT_DESC])
            ->offset($offset)
            ->limit($this->page_size)
            ->asArray()
            ->all();

        return $list;
    }

    /**
     * 视频详情
     * @return array|array[]|false|object|object[]|string|string[]
     */
    public function detail(){
        if (!$this->validate()) {
            return false;
        }

        ClassRoom::updateAllCounters(['views' => 1], ['id' => $this->id]);
        /**
         * @var ClassRoom $classRoom
         */
        $classRoom = ClassRoom::find()
            ->where(['status' => 1, 'id' => $this->id])
            ->one();
        if(empty($classRoom)){
            return [];
        }
        $author = $classRoom->author;
        $tags = [];
        $tagMaps = $classRoom->tagMap;

        /**
         * @var ClassRoomTagMap $tagMap
         */
        foreach ($tagMaps as $tagMap) {
            array_push($tags, $tagMap->tag->tag_name);
        }

        $data = ArrayHelper::toArray($classRoom);
        $data['author'] = $author;
        $data['tags'] = $tags;

        $userId = \Yii::$app->user->id;
        $like = ClassRoomLike::find()->where(['user_id' => $userId, 'class_room_id' => $this->id, 'is_like'=> 1])->one();
        $data['is_like'] = $like ? 1 : 0;

        $collect = ClassRoomCollect::find()->where(['user_id' => $userId, 'class_room_id' => $this->id, 'is_collect'=> 1])->one();
        $data['is_collect'] = $collect ? 1 : 0;


        return $data;
    }

    public function keepWatch() {
        if(!$this->validate()) {
            return false;
        }
        $key = RedisKeyMap::build(RedisKeyMap::KEEP_WATCH,[$this->uid]);
        $redis = \Yii::$app->redis;
        if(!$redis->hget($key,$this->id)) {
            $redis->hset($key,$this->id, 1);
        }
        return true;
    }
}
