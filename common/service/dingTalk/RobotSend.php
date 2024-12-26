<?php
namespace common\service\dingTalk;

use yii\httpclient\Client;
use yii\log\Target;

class RobotSend extends Target
{


    /**
     * @var string 发送机器的api接口
     */
    public $apiUrl = '';

    /**
     * @var string 钉钉机器人 发送时候的关键字
     */
    public $keyWord = '';


    /**
     * @var int[] 不需要发送的响应请求码
     */
    public $exceptHttpCode = [404,401,403];

    /**
     * @var string[]  排除代码中一些使用异常作为返回的message 信息
     */
    public $exceptMessage = [
    ];

    /**
     * @var string[] 模糊匹配的字符  匹配命中不发送
     */
    public $exceptLikeMessage = [
    ];


    public function export()
    {
        // 如果响应代码是 404 或者401 则不发送
        $exceptHttpCode = $this->exceptHttpCode;


        // 排除代码中一些使用异常作为返回的message 信息
        $exceptMessage = $this->exceptMessage;



        // 模糊匹配的字符  匹配命中不发生
        $exceptLikeMessage = $this->exceptLikeMessage;

//        return ;
        if (YII_DEBUG) {
            return;
        }




        $data = \Yii::$app->response->data;


        if (is_array($data) && isset($data['code']) && in_array($data['code'], $exceptHttpCode)) {
            return;
        }

        if (in_array(\Yii::$app->response->statusCode, $exceptHttpCode)){
            return;
        }


        if (isset($data['message']) ) {
            if ( in_array($data['message'], $exceptMessage)){
                return;
            }
            foreach ($exceptLikeMessage as $message){
                // 匹配到 则表示不需要发送消息
                if (strpos($data['message'], $message) !== false){
                    return;
                }
            }

        }


        // 获取最原始的  input数据
        $input = file_get_contents('php://input');

        $text = implode("\n", array_map([$this, 'formatMessage'], $this->messages)) . "\n";

        $text .= "\r\n【input】: {$input}";

//        $heads =  \Yii::$app->request->headers->toArray();
//        $headsStr = json_encode($heads,JSON_UNESCAPED_UNICODE);
//        $text .= "\r\nheaders: {$headsStr}";
//
//        $text .= "\r\n【ip】: ".\Yii::$app->request->getUserIP();
//
//
        $client = new Client(['baseUrl' => $this->apiUrl]);

        //自定义活动报名
        if($this->messages[0][2] == 'custom_event') {
            $response = $client->createRequest()
                ->setMethod('POST')
                ->setFormat(Client::FORMAT_JSON)
                ->setData([
                    'msgtype' => 'text',
                    'text' => [
                        'content' => "{$this->keyWord}:".$this->messages[0][0],
                    ],
                ])
                ->send();
            return;
        }

        $response = $client->createRequest()
            ->setMethod('POST')
            ->setFormat(Client::FORMAT_JSON)
            ->setData([
                'msgtype' => 'text',
                'text' => [
                    'content' => "{$this->keyWord}:".$text,
                ],
            ])
            ->send();
    }


    /**
     * 上面的export 方法 测试时候  发现框架打开为debug环境  导致不需要发送的错误信息存在问题
     * 该方法先预留  如果哪天打开debug 为关闭状态 则使用下面方法
     */
    public function exportNew(){
        // 如果响应代码是 404 或者401 则不发送
        $exceptHttpCode = $this->exceptHttpCode;


        // 排除代码中一些使用异常作为返回的message 信息
        $exceptMessage = $this->exceptMessage;



        // 模糊匹配的字符  匹配命中不发生
        $exceptLikeMessage = $this->exceptLikeMessage;


        foreach ($this->messages as $message){
            /**
             * @var \Exception $message
             */
            if ( in_array($message->getMessage(), $exceptMessage)){
                return;
            }

            foreach ($exceptLikeMessage as $message){
                // 匹配到 则表示不需要发送消息
                if (strpos($message->getMessage(), $message) !== false){
                    return;
                }
            }

        }


        if (YII_ENV != 'prod') {
            return;
        }


        // 获取最原始的  input数据
        $input = file_get_contents('php://input');

        $text = implode("\n", array_map([$this, 'formatMessage'], $this->messages)) . "\n";

        $text .= "\r\n【input】: {$input}";

        $client = new Client(['baseUrl' => $this->apiUrl]);
        $response = $client->createRequest()
            ->setMethod('POST')
            ->setFormat(Client::FORMAT_JSON)
            ->setData([
                'msgtype' => 'text',
                'text' => [
                    'content' => "{$this->keyWord}:".$text,
                ],
            ])
            ->send();

    }
}
