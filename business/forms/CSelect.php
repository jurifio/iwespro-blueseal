<?php

namespace bamboo\blueseal\business\forms;

/**
 * Class CSelect
 * @package redpanda\blueseal\business\forms
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
class CSelect implements IHTMLTag
{
    /**
     * @var CWidgetStructureFormField
     */
    protected $wsf;
    /**
     * @var string
     */
    protected $type;

    /**
     * CSelect constructor.
     * @param CWidgetStructureFormField $wsf
     */
    public function __construct(CWidgetStructureFormField $wsf)
    {
        $this->wsf = $wsf;
    }

    /**
     * @return string
     */
    public function open()
    {
        $this->wsf()->addAttr('placeholder','seleziona');
        $this->wsf()->addData('placeholder','seleziona');
        return
            $this->wsf->printLabel()
            .'<select '
            .'class="'.(implode(' ',$this->wsf->getCssClasses())).'" '
            .(implode(' ',$this->wsf->getData()))
            .(implode(' ',$this->wsf->getAttrs()))
            .'style="'.(implode(';',$this->wsf->getCss())).'" '
            .'>'.$this->createOptions($this->wsf->getValue());
    }

    /**
     * @return string
     */
    public function close()
    {
        return '</select>';
    }

    /**
     * @param string $selected
     * @return string
     */
    public function createOptions($selected)
    {
        $options = [];
        $options[] = '<option></option>';
        foreach($this->wsf->getWsdField('values') as $idx => $value) {
            if ($selected == $value) {
                $options[] = '<option value="'.$value.'" selected="selected">'.$this->wsf->getWsdField('prettyValues')[$idx].'</option>';
            } else {
                $options[] = '<option value="'.$value.'">'.$this->wsf->getWsdField('prettyValues')[$idx].'</option>';
            }
        }
        return implode("\n",$options);
    }

    /**
     * @return CWidgetStructureFormField
     */
    public function wsf()
    {
        return $this->wsf;
    }
}