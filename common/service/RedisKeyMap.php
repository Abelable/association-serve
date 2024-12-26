<?php


namespace common\service;


class RedisKeyMap
{
    //记录用户是否观看过课堂
    const KEEP_WATCH = "hash:keep_watch";
    //记录用户观看课堂密码输错次数
    const KEEP_WATCH_WRONG_PASS = "string:keep_watch_wrong_pass";

    //锁
    const LOCK = "string:lock";


    /**
     * @param string $key 使用 self::xxx的形式传入
     * @param array $args
     * @return string
     */
    public static function build(string $key, array $args = []): string
    {
        $prefix = "government:";
        if (empty($args)) {
            return $prefix . $key;
        } else {
            $key = $prefix . $key;

            foreach ($args as $k => $v) {
                if (!is_numeric($k)) {
                    $key .= ":{$k}_{$v}";
                } else {
                    $key .= ":{$v}";
                }
            }
            return $key;
        }
    }
}