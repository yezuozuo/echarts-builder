<?php
/**
 * @since  2016-03-21
 */

require_once __DIR__ . '/Config.php';

class ZocoCharts implements \ArrayAccess {
    /**
     * @var array
     */
    public $_options = array();

    /**
     * @param string $dist
     */
    public function __construct($dist = '') {
        if($dist) {
            Config::$dist = $dist;
        }
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset) {
        return isset($this->_options[$offset]);
    }

    /**
     * @param mixed $offset
     * @param mixed $val
     * @return mixed
     */
    public function offsetSet($offset,$val) {
        if(is_null($offset)) {
            return $this->_options[] = $val;
        } else {
            return $this->_options[$offset] = $val;
        }
    }

    /**
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet($offset) {
        if(!$this->offsetExists($offset)) {
            $this->_options[$offset] = new self;
        }
        return $this->_options[$offset];
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset) {
        if($this->offsetExists($offset)) {
            unset($this->_options[$offset]);
        }
    }

    /**
     * @param $offset
     * @param $val
     * @return mixed
     */
    public function __set($offset,$val) {
        return $this->offsetSet($offset,$val);
    }

    /**
     * @param $offset
     * @return mixed
     */
    public function __get($offset) {
        return $this->offsetGet($offset);
    }

    /**
     * @param null $render
     * @return array
     */
    public function getOption($render = null) {
        $options = array();
        is_null($render) && $render = $this->_options;
        foreach($render as $key => $val) {
            if(is_object($val)) {
                $options[$key] = $val->_options;
                if(is_array($options[$key])) {
                    $options[$key] = $this->getOption($options[$key]);
                }
            } else {
                $options[$key] = $val;
            }
        }

        return $options;
    }

    /**
     * @param       $id
     * @param array $attribute
     * @param null  $theme
     * @return string
     */
    public function render($id,$attribute = array(),$theme = null) {
        return Config::render($id,$this->getOption(),$theme,$attribute);
    }
}