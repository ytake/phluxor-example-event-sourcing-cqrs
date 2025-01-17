<?php
# Generated by the protocol buffer compiler.  DO NOT EDIT!
# NO CHECKED-IN PROTOBUF GENCODE
# source: event.proto

namespace App\Event\ProtoBuf;

use Google\Protobuf\Internal\GPBType;
use Google\Protobuf\Internal\RepeatedField;
use Google\Protobuf\Internal\GPBUtil;

/**
 * Generated from protobuf message <code>protobuf.UserCreated</code>
 */
class UserCreated extends \Google\Protobuf\Internal\Message
{
    /**
     * Generated from protobuf field <code>string userName = 1;</code>
     */
    protected $userName = '';
    /**
     * Generated from protobuf field <code>string email = 2;</code>
     */
    protected $email = '';
    /**
     * Generated from protobuf field <code>string userID = 3;</code>
     */
    protected $userID = '';
    /**
     * Generated from protobuf field <code>int64 version = 4;</code>
     */
    protected $version = 0;

    /**
     * Constructor.
     *
     * @param array $data {
     *     Optional. Data for populating the Message object.
     *
     *     @type string $userName
     *     @type string $email
     *     @type string $userID
     *     @type int|string $version
     * }
     */
    public function __construct($data = NULL) {
        \App\Event\Metadata\Event::initOnce();
        parent::__construct($data);
    }

    /**
     * Generated from protobuf field <code>string userName = 1;</code>
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Generated from protobuf field <code>string userName = 1;</code>
     * @param string $var
     * @return $this
     */
    public function setUserName($var)
    {
        GPBUtil::checkString($var, True);
        $this->userName = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string email = 2;</code>
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Generated from protobuf field <code>string email = 2;</code>
     * @param string $var
     * @return $this
     */
    public function setEmail($var)
    {
        GPBUtil::checkString($var, True);
        $this->email = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>string userID = 3;</code>
     * @return string
     */
    public function getUserID()
    {
        return $this->userID;
    }

    /**
     * Generated from protobuf field <code>string userID = 3;</code>
     * @param string $var
     * @return $this
     */
    public function setUserID($var)
    {
        GPBUtil::checkString($var, True);
        $this->userID = $var;

        return $this;
    }

    /**
     * Generated from protobuf field <code>int64 version = 4;</code>
     * @return int|string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Generated from protobuf field <code>int64 version = 4;</code>
     * @param int|string $var
     * @return $this
     */
    public function setVersion($var)
    {
        GPBUtil::checkInt64($var);
        $this->version = $var;

        return $this;
    }

}

