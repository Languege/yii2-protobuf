<?php
/**
 * Created by PhpStorm.
 * User: liugaoyun
 * Date: 2018/12/1
 * Time: 上午11:26
 */

namespace Language\Protobuf;


use yii\helpers\BaseArrayHelper;

class ProtobufManager
{
    public static $protobufMsg = [];

    public static function register($filename){
        self::$protobufMsg = BaseArrayHelper::merge(self::$protobufMsg, require $filename);
    }

    public static function getMessageMeta($classname){
        if(isset(self::$protobufMsg[$classname])){
            return self::$protobufMsg[$classname];
        }else{
            throw new ProtobufException("$classname not registered");
        }
    }
}