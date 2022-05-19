<?php

namespace bamboo\blueseal\business\forms;

/**
 * Class CImg
 * @package bamboo\blueseal\business\forms
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01/12/2015
 * @since 1.0
 */
class CImg extends CGenericHTMLTag implements IHTMLTag
{
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
            .'src="'.$this->value.'"';

        if ($this->void) {
            $tag .= ' />';
        } else {
            $tag .= '>'.$this->value;
        }

        return $tag;
    }
}