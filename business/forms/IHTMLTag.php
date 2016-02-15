<?php

namespace bamboo\blueseal\business\forms;

/**
 * Interface IHTMLTag
 * @package bamboo\blueseal\business\forms
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 4/12/2015
 * @since 1.0
 */
interface IHTMLTag
{
    /**
     * @return string
     */
    public function open();
    /**
     * @return string|null
     */
    public function close();
}