<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# source: msg.proto

namespace PbApp;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>PbApp.Reponse</code>
 */
class Reponse extends \Google\Protobuf\Internal\Message
{
    /**
     *解析是使用的反射类
     *
     * Generated from protobuf field <code>string ReflectClass = 1;</code>
     */
    private $ReflectClass = '';
    /**
     *自定义pb数据
     *
     * Generated from protobuf field <code>bytes Body = 2;</code>
     */
    private $Body = '';
    /**
     * Generated from protobuf field <code>repeated string ArrayParams = 3;</code>
     */
    private $ArrayParams;
    /**
     * Generated from protobuf field <code>map<string, .PbApp.Meta> MapParams = 4;</code>
     */
    private $MapParams;

    public function __construct() {
        \GPBMetadata\Msg::initOnce();
        parent::__construct();
    }

    /**
     *解析是使用的反射类
     *
     * Generated from protobuf field <code>string ReflectClass = 1;</code>
     * @return string
     */
    public function getReflectClass()
    {
        return $this->ReflectClass;
    }

    /**
     *解析是使用的反射类
     *
     * Generated from protobuf field <code>string ReflectClass = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setReflectClass($var)
    {
        GPBUtil::checkString($var, True);
        $this->ReflectClass = $var;

        return $this;
    }

    /**
     *自定义pb数据
     *
     * Generated from protobuf field <code>bytes Body = 2;</code>
     * @return string
     */
    public function getBody()
    {
        return $this->Body;
    }

    /**
     *自定义pb数据
     *
     * Generated from protobuf field <code>bytes Body = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setBody($var)
    {
        GPBUtil::checkString($var, False);
        $this->Body = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>repeated string ArrayParams = 3;</code>
     * @return \Google\Protobuf\Internal\RepeatedField
     */
    public function getArrayParams()
    {
        return $this->ArrayParams;
    }

    /**
     * Generated from protobuf field <code>repeated string ArrayParams = 3;</code>
     * @param string[]|\Google\Protobuf\Internal\RepeatedField $var
     * @return $this
     */
    public function setArrayParams($var)
    {
        $arr = GPBUtil::checkRepeatedField($var, \Google\Protobuf\Internal\GPBType::STRING);
        $this->ArrayParams = $arr;

        return $this;
    }

    /**
     * Generated from protobuf field <code>map<string, .PbApp.Meta> MapParams = 4;</code>
     * @return \Google\Protobuf\Internal\MapField
     */
    public function getMapParams()
    {
        return $this->MapParams;
    }

    /**
     * Generated from protobuf field <code>map<string, .PbApp.Meta> MapParams = 4;</code>
     * @param array|\Google\Protobuf\Internal\MapField $var
     * @return $this
     */
    public function setMapParams($var)
    {
        $arr = GPBUtil::checkMapField($var, \Google\Protobuf\Internal\GPBType::STRING, \Google\Protobuf\Internal\GPBType::MESSAGE, \PbApp\Meta::class);
        $this->MapParams = $arr;

        return $this;
    }

}
