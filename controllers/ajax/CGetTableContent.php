<?php
namespace bamboo\blueseal\controllers\ajax;
use bamboo\core\exceptions\BambooException;

/**
 * Class CBrandDelete
 * @package bamboo\blueseal\controllers\ajax
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CGetTableContent extends AAjaxController
{
    public function get()
    {
        if (!$this->app->getUser()->hasPermission('allShops')) throw new \Exception('Solo gli eletti, appartenenti alla Gilda degli Illuminati possono effettuare questa operazione. Contatta un amministratore');
        $table = $this->app->router->request()->getRequestData('table');
        $fields = $this->app->router->request()->getRequestData('fields');
        $condition = $this->app->router->request()->getRequestData('condition');

        if (!$table) throw new \Exception('la variabile "table" è obbligatoria');
        if (!is_array($fields)) throw new \Exception('la variabile "fields" è obbligatoria e deve essere un array');
        if ((false !== $condition) && ((!is_array($condition) || (!count($condition))))) throw new BambooException('Le condizioni devono essere passate sottoforma di array');

        if ($condition) $OC = $this->app->repoFactory->create($table)->findBy($condition);
        else $OC = $this->app->repoFactory->create($table)->findAll();

        $ret = [];
        $i = 0;
        foreach($OC as $em) {
            $ret[$i] = [];
            foreach($fields as $f) {
                $ret[$i][$f] = $em->{$f};
            }
            $i++;
        }
        return json_encode($ret);
    }
}