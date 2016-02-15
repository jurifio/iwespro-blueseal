<?php

namespace bamboo\blueseal\business\forms;
use bamboo\core\exceptions\RedPandaInvalidArgumentException;

/**
 * Class CWidgetStructureFormField
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
class CWidgetStructureFormField
{
    /**
     * @var string
     */
    protected $tag;
    /**
     * @var array
     */
    protected $cssClasses = [];
    /**
     * @var array
     */
    protected $data = [];
    /**
     * @var string
     */
    protected $value;
    /**
     * @var array
     */
    protected $attributes = [];
    /**
     * @var array
     */
    protected $cssStyle = [];
    /**
     * @var bool
     */
    protected $void = false;
    /**
     * @var array
     */
    protected $voidTags = ['area','base','br','col','command','embed','hr','img','input','keygen','link','meta','param','source','track','wbr'];
    /**
     * @var array
     */
    protected $wsd;
    /**
     * @var CGenericHTMLTag
     */
    protected $label;

    /**
     * CWidgetStructureFormField constructor.
     * @param array $widgetStructureDescriptor
     * @param string $id
     */
    public function __construct($id, array $widgetStructureDescriptor)
    {
        $this->id = $id;
        $this->wsd = $widgetStructureDescriptor;
        $this->tag = $widgetStructureDescriptor['tag'];

        if (in_array($this->tag,$this->voidTags) !== false) {
            $this->void = true;
        }

        $this->cssClasses = explode(' ',$widgetStructureDescriptor['css']);

        if ($widgetStructureDescriptor['required']) {
            $this->addAttr('required','required');
        }

        $this->label = new CGenericHTMLTag('label');
        $this->label->setValue($widgetStructureDescriptor['label']);
    }

    /**
     * @return string
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function addAttr($key, $value)
    {
        $this->attributes[$key] = $key.'="'.$value.'"';
    }

    /**
     * @param string $key
     */
    public function removeAttr($key)
    {
        if (isset($this->attributes[$key])) {
            unset($this->attributes[$key]);
        }
    }

    /**
     * @return array
     */
    public function getAttrs()
    {
        return $this->attributes;
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
     * @return array
     */
    public function getData()
    {
        return $this->data;
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
     * @return array
     */
    public function getCssClasses()
    {
        return $this->cssClasses;
    }

    /**
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
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
     * @return array
     */
    public function getCss()
    {
        return $this->cssStyle;
    }

    /**
     * @return bool
     */
    public function isVoid()
    {
        return $this->void;
    }

    /**
     * @param $field
     * @return null
     */
    public function getWsdField($field)
    {
        return (isset($this->wsd[$field])) ? $this->wsd[$field] : null;
    }

    /**
     * @return CGenericHTMLTag
     */
    public function label()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function printLabel()
    {
        return $this->label->open().$this->label->close();
    }

    /**
     * @return CInput
     * @throws RedPandaInvalidArgumentException
     */
    public function create()
    {
        switch ($this->tag) {
            case 'input':
                return new CInput($this);
                break;
            case 'textarea':
                return new CTextarea($this);
                break;
            case 'select':
                return new CSelect($this);
                break;
        }

        throw new RedPandaInvalidArgumentException('Tag %s not allowed',$this->tag);
    }
}