<?php

namespace bamboo\blueseal\business\forms;

/**
 * Class CGenericHTMLTag
 * @package bamboo\blueseal\business\forms
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/12/2015
 * @since 1.0
 */
class CGenericHTMLTag implements IHTMLTag
{
    /**
     * @var string
     */
    protected $tag;
    /**
     * @var array
     */
    protected $cssClasses;
    /**
     * @var array
     */
    protected $data;
    /**
     * @var array
     */
    protected $value;
    /**
     * @var array
     */
    protected $attributes;
    /**
     * @var array
     */
    protected $cssStyle;
    /**
     * @var bool
     */
    protected $void = false;
    /**
     * @var array
     */
    protected $voidTags = ['area','base','br','col','command','embed','hr','img','input','keygen','link','meta','param','source','track','wbr'];

    /**
     * CFormFieldWrapper constructor.
     * @param $tag
     * @param array $cssClasses
     * @param array $attributes
     * @param array $cssStyle
     */
    public function __construct($tag, array $cssClasses = [], array $attributes = [], array $cssStyle = [])
    {
        $this->tag = $tag;
        if (in_array($this->tag,$this->voidTags) !== false) {
            $this->void = true;
        }
        $this->cssClasses = $cssClasses;
        $this->cssStyle = $cssStyle;
        $this->attributes = $attributes;
        $this->value = null;
        $this->data = [];
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addAttr($key, $value)
    {
        $this->data[$key] = $key.'="'.$value.'"';
    }

    /**
     * @param string $key
     */
    public function removeAttr($key)
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addData($key, $value)
    {
        $this->data[$key] = 'data-'.$key.'="'.$value.'"';
    }

    /**
     * @param string $key
     */
    public function removeData($key)
    {
        if (isset($this->data[$key])) {
            unset($this->data[$key]);
        }
    }

    /**
     * @param string $class
     */
    public function addClass($class)
    {
        $this->cssClasses[] = $class;
    }

    /**
     * @param string $class
     */
    public function removeClass($class)
    {
        if (in_array($class,$this->cssClasses) !== false) {
            unset($this->cssClasses[$class]);
        }
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return array|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addCss($key, $value)
    {
        $this->cssStyle[$key] = $key.':'.$value;
    }

    /**
     * @return string
     */
    public function open()
    {
        $tag =
            '<'.$this->tag.' '
            .'class="'.(implode(' ',$this->cssClasses)).'" '
            .(implode(' ',$this->data))
            .(implode(' ',$this->attributes))
            .'style="'.(implode(';',$this->cssStyle)).'"'
            .'value="'.$this->value.'"';

        if ($this->void) {
            $tag .= ' />';
        } else {
            $tag .= '>'.$this->value;
        }

        return $tag;
    }

    /**
     * @return string
     */
    public function close()
    {
        return ($this->void) ? null : '</'.$this->tag.'>';
    }
}