<?php
namespace registration_thrift;

/**
 * Autogenerated by Thrift Compiler (0.9.0)
 *
 * DO NOT EDIT UNLESS YOU ARE SURE THAT YOU KNOW WHAT YOU ARE DOING
 *  @generated
 */
use Thrift\Base\TBase;
use Thrift\Type\TType;
use Thrift\Type\TMessageType;
use Thrift\Exception\TException;
use Thrift\Exception\TProtocolException;
use Thrift\Protocol\TProtocol;
use Thrift\Exception\TApplicationException;


class RegistrationRequest {
  static $_TSPEC;

  public $email = null;
  public $password = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'email',
          'type' => TType::STRING,
          ),
        2 => array(
          'var' => 'password',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['email'])) {
        $this->email = $vals['email'];
      }
      if (isset($vals['password'])) {
        $this->password = $vals['password'];
      }
    }
  }

  public function getName() {
    return 'RegistrationRequest';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->email);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        case 2:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->password);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('RegistrationRequest');
    if ($this->email !== null) {
      $xfer += $output->writeFieldBegin('email', TType::STRING, 1);
      $xfer += $output->writeString($this->email);
      $xfer += $output->writeFieldEnd();
    }
    if ($this->password !== null) {
      $xfer += $output->writeFieldBegin('password', TType::STRING, 2);
      $xfer += $output->writeString($this->password);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}

class RegistrationResponse {
  static $_TSPEC;

  public $response_message = null;

  public function __construct($vals=null) {
    if (!isset(self::$_TSPEC)) {
      self::$_TSPEC = array(
        1 => array(
          'var' => 'response_message',
          'type' => TType::STRING,
          ),
        );
    }
    if (is_array($vals)) {
      if (isset($vals['response_message'])) {
        $this->response_message = $vals['response_message'];
      }
    }
  }

  public function getName() {
    return 'RegistrationResponse';
  }

  public function read($input)
  {
    $xfer = 0;
    $fname = null;
    $ftype = 0;
    $fid = 0;
    $xfer += $input->readStructBegin($fname);
    while (true)
    {
      $xfer += $input->readFieldBegin($fname, $ftype, $fid);
      if ($ftype == TType::STOP) {
        break;
      }
      switch ($fid)
      {
        case 1:
          if ($ftype == TType::STRING) {
            $xfer += $input->readString($this->response_message);
          } else {
            $xfer += $input->skip($ftype);
          }
          break;
        default:
          $xfer += $input->skip($ftype);
          break;
      }
      $xfer += $input->readFieldEnd();
    }
    $xfer += $input->readStructEnd();
    return $xfer;
  }

  public function write($output) {
    $xfer = 0;
    $xfer += $output->writeStructBegin('RegistrationResponse');
    if ($this->response_message !== null) {
      $xfer += $output->writeFieldBegin('response_message', TType::STRING, 1);
      $xfer += $output->writeString($this->response_message);
      $xfer += $output->writeFieldEnd();
    }
    $xfer += $output->writeFieldStop();
    $xfer += $output->writeStructEnd();
    return $xfer;
  }

}


