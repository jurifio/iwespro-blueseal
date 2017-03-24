<?php

namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CDictionaryBrandListAjaxController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CContentTranslationListAjaxController extends AAjaxController
{

    public function get()
    {
        $langs = $this->app->repoFactory->create('Lang')->findAll();

        $sql = "SELECT hash,";
        $sqlLang = [];
        foreach($langs as $lang) {
            $sqlLang[] = "max((CASE 
                    WHEN l.id = ".$lang->id.
                    " THEN t.string 
                   ELSE NULL 
                   END)) as lang_".$lang->id;
            }
        $sql.=implode(',',$sqlLang);
        $sql.=" FROM Lang l
                  LEFT JOIN Translation t ON langId = id
                  where hash is not null
                GROUP BY t.hash";
        $datatable = new CDataTables($sql, ['hash'], $_GET, true);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId', $this->authorizedShops);
        }

        $strings = $this->app->dbAdapter->query($datatable->getQuery(false,true),$datatable->getParams())->fetchAll();
        $count = $this->app->dbAdapter->query($datatable->getQuery(true), $datatable->getParams())->fetch();
        foreach ($count as $c) {
            $count = $c; break;
        }
        $totalCount = $this->app->dbAdapter->query($datatable->getQuery('full'), $datatable->getParams())->fetch();
        foreach ($totalCount as $c) {
            $totalCount = $c; break;
        }

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];

        foreach ($strings as $string) {
            $row = $string;
            unset($string['hash']);
            foreach($string as $key=>$val) {
                $langId = explode('_',$key)[1];
                $row[$key] =
                    '<div class="form-group form-group-default" style="width: 100%">
                        <input class="translation-element form-control" 
                                data-hash="'.$row['hash'].'" 
                                data-lang-id="'.$langId.'" '.($val && !empty($val) ? 'value="'.$val.'"' : '').'>
                    </div>';
            }
            $response['data'][] = $row;
        }
        return json_encode($response);
    }
}