<?php
namespace bamboo\controllers\ajax;
use bamboo\ecommerce\views\VBase;

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
class CGetDataSheet extends AAjaxController
{
    /**
     * @throws \bamboo\core\exceptions\RedPandaDBALException
     */
    public function get()
    {
        $view = new VBase(array());
        $view->setTemplatePath($this->app->rootPath().$this->app->cfg()->fetch('paths','blueseal').'/template/parts/sheetDetails.php');
        $langs = $this->app->repoFactory->create('Lang')->findAll('limit 9999', '');

        $name = $this->app->router->request()->getRequestData('value');

        $em = $this->app->entityManagerFactory->create('ProductAttribute');
        $detailsGroups = array();
        foreach ($langs as $lang) {
            $sql = 'SELECT DISTINCT productAttributeId AS id FROM ProductSheet WHERE `name` = ?';
            $detailsGroups[$lang->lang] = $em->findBySql($sql,[$name]);
        }
        $langs->rewind();

        echo $view->render([
            'langs' => $langs,
            'detailsGroups' =>$detailsGroups
        ]);
    }

    public function post()
    {
        return $this->get();
    }
}