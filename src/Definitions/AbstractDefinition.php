<?php
namespace Thenextweb\Definitions;

use Illuminate\Support\Contracts\ArrayableInterface as Arrayable;
use Illuminate\Support\Contracts\Arr;

abstract class AbstractDefinition implements Arrayable, DefinitionInterface
{
    protected $pass = [];

    protected $formatVersion = 1;

    public function __construct()
    {
        $this->pass['formatVersion'] = $this->formatVersion;
    }



    /**
     * @param $name
     * @param $value
     */
    public function __set($name, $value)
    {
        $this->pass[$name] = $value;
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->pass)) {
            return $name;
        }

        return null;
    }

    public function __call($name, $args)
    {
        $matches = [];
        if (preg_match('/set([a-z].*)/ig', $name, $matches) && isset($args[0])) {
            $prop = lcfirst($matches[1]);
            $this->{$prop} = $args[0];

            return $this;
        }

        $matches = [];
        if (preg_match('/get([a-z].*)/ig', $name, $matches)) {
            $prop = lcfirst($matches[1]);
            return array_key_exists($prop, $this->pass) ? $this->pass[$prop] : null;
        }

        throw new \BadMethodCallException();
    }

    /**
     * Returns an array representation of the definition compatible with PassKit Package Format
     */
    public function toArray()
    {

    }
}