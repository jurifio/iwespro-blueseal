<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CGetAutocompleteSize
 * @package bamboo\blueseal\controllers\ajax
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>, ${DATE}
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CGetAutocompleteSize extends AAjaxController
{
    /**
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     */
    public function get()
    {
        $sql = 'SELECT `name` FROM ProductSize';
        $res = $this->app->dbAdapter->query($sql,array())->fetchAll();
        $rres= array();
        foreach($res as $val){
            $rres[] = $val['name'];
        }
        $rres = implode(',', $rres);
        return $rres;
    }

    public function post(){
        return $this->get();
    }

}