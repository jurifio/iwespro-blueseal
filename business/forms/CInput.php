<?php

namespace bamboo\blueseal\business\forms;

/**
 * Class CInput
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
class CInput implements IHTMLTag
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
     * CInput constructor.
     * @param CWidgetStructureFormField $wsf
     */
    public function __construct(CWidgetStructureFormField $wsf)
    {
        $this->wsf = $wsf;
        $this->type = $this->wsf->getWsdField('type');
    }

    /**
     * @return string
     */
    public function open()
    {
        return
            $this->wsf->printLabel()
            .'<input '
            .'type="'.$this->type.'" '
            .'class="'.(implode(' ',$this->wsf->getCssClasses())).'" '
            .(implode(' ',$this->wsf->getData()))
            .(implode(' ',$this->wsf->getAttrs()))
            .'style="'.(implode(';',$this->wsf->getCss())).'" '
            .'value="'.$this->wsf->getValue().'" '
            .'/>';
    }

    /**
     * @return null
     */
    public function close()
    {
        return null;
    }

    /**
     * @return CWidgetStructureFormField
     */
    public function wsf()
    {
        return $this->wsf;
    }
}