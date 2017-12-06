<?php

namespace bamboo\controllers\back\ajax;

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
        $langs = \Monkey::app()->repoFactory->create('Lang')->findAll();

        $sql = "SELECT hash,hint,";
        $sqlLang = [];
        foreach ($langs as $lang) {
            $sqlLang[] = "max((CASE 
                    WHEN l.id = " . $lang->id .
                " THEN t.string 
                   ELSE NULL 
                   END)) as lang_" . $lang->id;
        }
        $sql .= implode(',', $sqlLang);
        $sql .= " FROM Lang l
                  LEFT JOIN Translation t ON langId = id
                  where hash is not null
                GROUP BY t.hash";
        $datatable = new CDataTables($sql, ['hash'], $_GET, true);

        if (!empty($this->authorizedShops)) {
            $datatable->addCondition('shopId', $this->authorizedShops);
        }

        $strings = $this->app->dbAdapter->query($datatable->getQuery(false, true), $datatable->getParams())->fetchAll();
        $count = $this->app->dbAdapter->query($datatable->getQuery(true), $datatable->getParams())->fetch();
        foreach ($count as $c) {
            $count = $c;
            break;
        }
        $totalCount = $this->app->dbAdapter->query($datatable->getQuery('full'), $datatable->getParams())->fetch();
        foreach ($totalCount as $c) {
            $totalCount = $c;
            break;
        }

        $response = [];
        $response ['draw'] = $this->app->router->request()->getRequestData('draw');
        $response ['recordsTotal'] = $totalCount;
        $response ['recordsFiltered'] = $count;
        $response ['data'] = [];
        $templatePath =  $this->app->rootPath() . $this->app->cfg()->fetch("paths", "public");
        foreach ($strings as $string) {
            $row = $string;
            $row['hint'] = substr(str_replace(
                $templatePath,
                    '...',
                    $row['hint']),0,70).'..';
            $row['hint'] = str_replace(
                ' -> ',
                    '<br />',
                    $row['hint']);
            unset($string['hash']);
            unset($string['hint']);
            foreach ($string as $key => $val) {
                $langId = explode('_', $key)[1];
                $row[$key] =
                    '<div class="form-group form-group-default" style="width: 100%">
                        <textarea class="translation-element form-control" 
                                style="width: 100%"
                                data-hash="' . $row['hash'] . '"
                                data-encoded-value="'.base64_encode($val).'"
                                data-lang-id="' . $langId . '"></textarea>
                    </div>';
            }
            $response['data'][] = $row;
        }
        return json_encode($response);
    }
}