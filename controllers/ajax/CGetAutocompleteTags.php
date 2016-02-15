<?php
namespace bamboo\controllers\ajax;

/**
 * Class CGetAutocompleteSize
 * @package bamboo\blueseal\controllers\ajax
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CGetAutocompleteTags extends AAjaxController
{
    /**
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     */
    public function get()
    {
        $sql = 'SELECT `slug` FROM Tag';
        $res = $this->app->dbAdapter->query($sql,array())->fetchAll();
        $rres= array();
        foreach($res as $val){
            $rres[] = $val['name'];
        }
        $rres = implode(',', $rres);
        echo $rres;
    }

    public function post(){
        return $this->get();
    }

}