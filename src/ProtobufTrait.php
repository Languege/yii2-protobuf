<?php
/**
 * Created by PhpStorm.
 * User: liugaoyun
 * Date: 2018/12/1
 * Time: 下午4:55
 */

namespace Language\Protobuf;


use yii\web\Response;

trait ProtobufTrait
{
    /**
     * Notes: 在yii\web\Controller子类中引用 use \Language\Protobuf\ProtobufTrait
     * User: LanguageY
     * @param $data ['class'=>, 'data'=>]
     * @return mixed
     */
    public function asProtobuf($data){
        $response = Yii::$app->getResponse();
        $response->format = 'protobuf';
        $response->data = $data;
        return $response;
    }
}