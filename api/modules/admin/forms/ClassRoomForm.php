<?php


namespace api\modules\admin\forms;


use common\helpers\ArrayHelper;
use common\models\common\Author;
use common\models\common\ClassRoom;
use common\models\common\ClassRoomTag;
use common\models\common\ClassRoomTagMap;
use yii\base\Model;

class ClassRoomForm extends Model
{
    /**
     * 作者新增、编辑、删除
     */
    const SCENARIO_AUTHOR_SAVE = 'author_save';

    /**
     * 网商课堂新增、编辑、删除
     */
    const SCENARIO_CLASS_ROOM_SAVE = 'class_room_save';

    /**
     * 网商课堂列表
     */
    const SCENARIO_CLASS_ROOM_LIST = 'class_room_list';

    /**
     * 作者列表
     */
    const SCENARIO_AUTHOR_LIST = 'author_list';



    /**
     * @var int 查询时候的页数
     */
    public $page = 1;


    /**
     * @var int 一页的数据
     */
    public $page_size = 20;

    /**
     * @var int 编辑、删除传的id
     */
    public $id;

    /**
     * @var string 作者名称
     */
    public $author_name;

    /**
     * @var string 作者头像
     */
    public $head_img;

    /**
     * @var int 状态：-1-删除；0-禁用；1-启用
     */
    public $status;

    /**
     * @var string 课堂名称
     */
    public $title;

    /**
     * @var string 封面
     */
    public $cover_img;

    /**
     * @var string 视频url
     */
    public $media_url;

    /**
     * @var string 视频url
     */
    public $duration;

    /**
     * @var string 作者id
     */
    public $author_id;

    /**
     * @var int 是否试看
     */
    public $is_try;

    /**
     * @var int 试看时间，单位：分
     */
    public $try_time;

    /**
     * @var string 密码
     */
    public $password;

    /**
     * @var string 简介
     */
    public $introduction;

    /**
     * @var int 排序权重
     */
    public $sort;

    /**
     * @var string 标签，英文逗号隔开
     */
    public $tags;

    /**
     * @var string 开始时间
     */
    public $start_time;

    /**
     * @var string 结束时间
     */
    public $end_time;

    public $views;


    public function rules()
    {
        return [
            //公共参数
            [['page','page_size'],'integer', 'on' => [self::SCENARIO_CLASS_ROOM_LIST, self::SCENARIO_AUTHOR_LIST]],

            //作者添加、编辑、删除
            [['author_name', 'head_img'], 'required', 'on' => [self::SCENARIO_AUTHOR_SAVE]],
            [['author_name', 'head_img'], 'string', 'on' => [self::SCENARIO_AUTHOR_SAVE]],
            [['id','status'], 'integer', 'on' => [self::SCENARIO_AUTHOR_SAVE]],
            ['status', 'in', 'range' => [-1,0,1], 'on' => [self::SCENARIO_AUTHOR_SAVE]],

            //网商课堂添加、编辑、删除
            [['title', 'cover_img','media_url', 'author_id', 'is_try', 'tags'], 'required', 'on' => [self::SCENARIO_CLASS_ROOM_SAVE]],
            [['title', 'cover_img','media_url', 'password', 'introduction', 'tags'], 'string', 'on' => [self::SCENARIO_CLASS_ROOM_SAVE]],
            [['id','status','is_try', 'try_time', 'sort', 'author_id', 'duration','views'], 'integer', 'on' => [self::SCENARIO_CLASS_ROOM_SAVE]],
            ['status', 'in', 'range' => [-1,0,1], 'on' => [self::SCENARIO_CLASS_ROOM_SAVE]],

            //网商课堂列表
            [['start_time','end_time'], 'string', 'on' => [self::SCENARIO_CLASS_ROOM_LIST]],
            [['title'], 'string', 'on' => [self::SCENARIO_CLASS_ROOM_LIST]],
        ];
    }

    /**
     * 保存作者信息
     * @return bool
     */
    public function authorSave() {
        if(!$this->validate()) {
            return false;
        }
        $author = Author::findOne(['status' => 1,'id' => $this->id]);
        if(!$author) {
            $author = new Author();
        }
        $author->author_name = $this->author_name;
        $author->head_img = $this->head_img;
        $author->status = $this->status ?? 1;
        if(!$author->save()) {
            $this->addError('author_save', '保存作者信息异常');
            return false;
        }

        return true;
    }

    public function classRoomSave() {
        if(!$this->validate()) {
            return false;
        }

        $tags = explode(',', $this->tags);

        //查看作者是否存在
        $author = Author::findOne(['id' => $this->author_id, 'status' => 1]);
        if(!$author) {
            $this->addError('class_room_save', '作者不存在');
            return false;
        }

        $tran = \Yii::$app->db->beginTransaction();
        try {
            $tagIds = $this->saveTags($tags);

            $classRoom = ClassRoom::findOne(['id' => $this->id]);
            if(!$classRoom) {
                $classRoom = new ClassRoom();
            }else {
                //清空之前的标签
                ClassRoomTagMap::deleteAll(['class_room_id' => $classRoom->id, 'status' => 1]);
            }
            $classRoom->title = $this->title;
            $classRoom->author_id = $this->author_id;
            $classRoom->cover_img = $this->cover_img;
            $classRoom->media_url = $this->media_url;
            $classRoom->duration = $this->duration ?? 0;
            $classRoom->is_try = $this->is_try;
            $classRoom->try_time = $this->try_time ?? 0;
            $classRoom->password = $this->password ?? '';
            $classRoom->introduction = $this->introduction;
            $classRoom->sort = $this->sort ?? 0;
            $classRoom->views = $this->views ?? 0;
            $classRoom->status = $this->status ?? 1;

            /**
             * @var ClassRoom $model
             */
            if(!$classRoom->save()) {
                throw new \Exception('保存课堂信息失败');
            }

            //更新课堂标签
            foreach ($tagIds as $tagId) {
                $tagMap = new ClassRoomTagMap();
                $tagMap->class_room_id = $classRoom->id;
                $tagMap->tag_id = $tagId;
                if(!$tagMap->save()) {
                    throw new \Exception('添加视频标签失败');
                }
            }

            $tran->commit();
            return true;

        }catch (\Exception $e) {
            $tran->rollBack();
            $this->addError('class_room_save', $e->getMessage());
            return false;
        }
    }

    /**
     * 课堂列表
     * @return false
     */
    public function list() {
        if (!$this->validate()){
            return false;
        }

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $offset = ($this->page - 1) * $this->page_size;
        $query = ClassRoom::find()
            ->where(['status' => 1])
            ->andFilterWhere(['like', 'title', $this->title]);

        if(!empty($this->start_time) && !empty($this->end_time)) {
            $startTime = strtotime($this->start_time.' 00:00:00');
            $endTime = strtotime($this->end_time.' 23:59:59');
            $query = $query->andWhere(['>=','created_at', $startTime])
            ->andWhere(['<=','created_at', $endTime]);
        }
        $res['total'] = $query->count();
        $classRoom = $query->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['sort' => SORT_DESC,'id' => SORT_DESC])
            ->all();

        $tags = [];
        $author = [];
        /**
         * @var ClassRoom $val
         */
        foreach($classRoom as $key => $val) {
            $tagMaps = $val->tagMap;
            /**
             * @var ClassRoomTagMap $tagMap
             */
            foreach ($tagMaps as $tagMap) {
                $tags[$key][] = $tagMap->tag->tag_name;
            }
            $author[$key] = $val->author;
        }

        $list = ArrayHelper::toArray($classRoom);
        foreach ($list as $k=> &$v) {
            $v['tags'] = $tags[$k];
            $v['author'] = $author[$k];
        }

        $res['list'] = $list;
        return $res;
    }

    /**
     * 作者列表
     * @return false
     */
    public function authorList() {
        if (!$this->validate()){
            return false;
        }

        $res['total'] = 0;
        $res['list'] = [];
        $res['page'] = $this->page;
        $res['page_size'] = $this->page_size;

        $offset = ($this->page - 1) * $this->page_size;
        $query = Author::find()
            ->where(['status' => 1]);
        $res['total'] = $query->count();
        $list = $query->limit($this->page_size)
            ->offset($offset)
            ->orderBy(['id' => SORT_DESC])
            ->asArray()
            ->all();

        $res['list'] = $list;
        return $res;
    }

    /** ==============数据库操作================ */
    public function saveTags($tags) {
        if(!is_array($tags)) {
            return false;
        }

        $tagIds = [];
        foreach ($tags as $val) {
            $tag = ClassRoomTag::findOne(['tag_name' => $val]);
            if(!$tag) {
                $tag = new ClassRoomTag();
                $tag->tag_name = $val;
                $tag->use_num = 1;
            }else {
                $tag->use_num += 1;
            }
            /**
             * @var ClassRoomTag $res
             */
            $tag->save();
            $tagIds[] = $tag->id;
        }

        return $tagIds;
    }

}
