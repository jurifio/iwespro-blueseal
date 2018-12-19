<?php

namespace bamboo\domain\repositories;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\ARepo;

/**
 * Class CFaqRepo
 * @package bamboo\domain\repositories
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 19/12/2018
 * @since 1.0
 */
class CFaqRepo extends ARepo
{

    public function searchFaq(string $search) : CObjectCollection{

        /** @var CObjectCollection $faqs */
        $faqs = $this->findBySql("SELECT * FROM Faq WHERE question LIKE ? or answer LIKE ?", ['%'.$search.'%', '%'.$search.'%']);

        return $faqs;
    }

}