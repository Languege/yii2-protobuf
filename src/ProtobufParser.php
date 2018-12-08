<?php
namespace Language\Protobuf;
use PbApp\FlexiableRequest;
use yii\base\BaseObject;
use yii\web\BadRequestHttpException;

/**
 * Created by PhpStorm.
 * User: liugaoyun
 * Date: 2018/11/30
 * Time: 下午2:55
 */
class ProtobufParser  extends BaseObject  implements  \yii\web\RequestParserInterface
{
    /**
     * @var bytes  protobuf binary raw data
     */
    public $raw = null;

    /**
     * @var \PbApp\Request
     */
    public $protobuf = null;

    /**
     * @var array
     */
    public $params = [];

    public $contentType = 'application/x-protobuf';

    public $requestProtoClass = 'PbApp\Request';

    public function init()
    {
        parent::init();
    }

    public function parse($rawBody, $contentType)
    {
        //判断请求方式是否是protobuf
        if($contentType == $this->contentType){
            $this->raw = $rawBody;

            try{
                if($this->requestProtoClass == FlexiableRequest::class){
                    $this->protobuf = new FlexiableRequest();
                    $this->protobuf->mergeFromString($this->raw);

                    $dataObj = new $this->protobuf->getProtoClass();
                    $dataObj->mergeFromString($this->protobuf->getProtoData());

                    $this->params = json_decode($dataObj->serializeToJsonString(), true);
                }else{
                    $this->protobuf = new $this->requestProtoClass;
                    $this->protobuf->mergeFromString($this->raw);

                    $this->params = array_merge((array)$this->protobuf->getBodyParams(), (array)$this->protobuf->getHeaders());
                }


                return $this->params;
            }catch(\Exception $e){
                throw new BadRequestHttpException("Invalid Protobuf Data In Request Body :" . $e->getMessage());
            }
        }else{
            //TODO:是否报异常
        }
    }
}