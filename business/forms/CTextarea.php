<?php

namespace bamboo\blueseal\business\forms;

/**
 * Class CTextarea
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
class CTextarea implements IHTMLTag
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
     * CTextarea constructor.
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
        return
            $this->wsf->printLabel()
            .'<textarea '
            .'class="'.(implode(' ',$this->wsf->getCssClasses())).'" '
            .(implode(' ',$this->wsf->getData()))
            .(implode(' ',$this->wsf->getAttrs()))
            .'style="'.(implode(';',$this->wsf->getCss())).'" '
            .'>'.$this->wsf->getValue();
    }

    /**
     * @return string
     */
    public function close()
    {
        return '</textarea>';
    }

    /**
     * @return CWidgetStructureFormField
     */
    public function wsf()
    {
        return $this->wsf;
    }
}