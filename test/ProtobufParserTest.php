<?php
/**
 * Created by PhpStorm.
 * User: liugaoyun
 * Date: 2018/12/1
 * Time: 下午3:10
 */


use Language\Protobuf\ProtobufParser;


class ProtobufParserTest extends \PHPUnit\Framework\TestCase
{
    public function testPaser(){
        require_once dirname(__DIR__) . '/autoload.php';
        require_once dirname(__DIR__ ). '/vendor/autoload.php';
        require_once dirname(dirname(dirname(__DIR__ ))). '/autoload.php';
        require_once dirname(dirname(dirname(__DIR__ ))). '/yiisoft/yii2/Yii.php';
        $obj = new Language\Protobuf\ProtobufParser();

        $params = $obj->parse(null, 'application/x-protobuf');

        $this->assertEquals(0, count($params));


        return $obj;
    }
}
