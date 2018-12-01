<?php
/**
 * Created by PhpStorm.
 * User: liugaoyun
 * Date: 2018/11/30
 * Time: 下午4:53
 */

namespace Language\Protobuf;


use yii\base\Component;
use yii\web\Response;
use yii\web\ResponseFormatterInterface;

class ProtobufResponseFormatter extends Component implements ResponseFormatterInterface
{
    public $contentType = 'application/x-protobuf';

    public function init()
    {
        parent::init();
        ProtobufManager::register(dirname(__DIR__) . '/proto/msg.proto.php');
    }

    /**
     * Formats the specified response.
     * @param Response $response the response to be formatted.
     */
    public function format($response)
    {
        $obj = $this->serialize($response->data['class'], $response->data['data']);
        $response->getHeaders()->set('Content-Type', $this->contentType);
        $response->content = $obj->serializeToString();
    }

    /**
     * Notes:protobuf数据ser
     * User: LanguageY
     * @param $namespace    序列化用的
     * @param $data 数组（哈希表）
     */
    public function serialize($classname, $data){
        if(is_object($classname)){
            $obj = $classname;
        }else{
            if(!class_exists($classname)){
                throw new ProtobufException("class $classname not found");
            }

            $obj = new $classname;
        }

        if (empty($data)) {
            return $obj;
        }

        //安全属性过滤
        $this->encode($obj, $data);

        return $obj;
    }

    private function encode(\Google\Protobuf\Internal\Message $obj, $data){
        $class_name = get_class($obj);

        //获取PB类的成员变量名
        $meta = ProtobufManager::getMessageMeta($class_name);
        $fields = array_keys($meta);
        foreach ($data as $key => $val) {
            if(!in_array($key, $fields)){//未定义的属性
                continue;
            }

            if(empty($val)){    //空值（null, 0, ''，false）不用管，就是protobuf的默认值
                continue;
            }

            $fieldmeta = $meta[$key];
            if($fieldmeta['IsScalarType'] == true){//标量
                $method = 'set' . ucfirst($key);
                $obj->$method($val);
            }else{//自定义对象
                if($fieldmeta['IsMap']){//字典<scalar, Object>
                    if(!is_array($val)){//data不是哈希表报错
                        throw new ProtobufException('Not Array Type');
                    }

                    $map = [];
                    foreach ($val as $mkey => $mvalue){
                        $mobj = new $fieldmeta['FieldType'];
                        $this->encode($mobj, $mvalue);
                        $map[$mkey] = $mobj;
                    }

                    $method = 'set' . ucfirst($key);
                    $obj->$method($map);
                }elseif ($fieldmeta['IsArray']){//数组 repeated Object
                    if(!is_array($val)){//data不是哈希表报错
                        throw new ProtobufException('Not Array Type');
                    }

                    $arr = [];
                    foreach ($val as $avalue){
                        $aobj = new $fieldmeta['FieldType'];
                        $this->encode($aobj, $avalue);
                        $arr[] = $aobj;
                    }

                    $method = 'set' . ucfirst($key);
                    $obj->$method($arr);
                }else{//单一值 属性  Object
                    $sub_obj = new $fieldmeta['FieldType'];
                    $this->encode($sub_obj, $val);

                    $method = 'set' . ucfirst($key);
                    $obj->$method($sub_obj);
                }
            }
        }
    }
}