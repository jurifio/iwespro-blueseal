<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;

/**
 * Class CDictionaryImageSizeListAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 04/02/2019
 * @since 1.0
 */
class CDictionaryImageSizeListAjaxController extends AAjaxController
{
    protected $urls = [];
    protected $authorizedShops = [];
    protected $em;

    /**
     * @param $action
     * @return mixed
     */
    Public function get()
    {
        $sql = "SELECT dis.id, 
                       S.name AS name,
                       dis.heightImage as heightImage,
                       dis.widthImage as widthImage,
                       dis.destinationfile as destinationfile,
                       dis.destinationXPoint as destinationXPoint,
                       dis.destinationYPoint as destinationYPoint,
                       dis.divisionByX as divisionByX,
                       dis.divisionByY as divisionByY,
                       dis.emptyZero as emptyZero,
                       dis.heightPercentageVariation as heightPercentageVariation,
                       dis.widthPercentageVariation as widthPercentageVariation,
                       dis.useDivision as useDivision,
                       dis.coverImage as coverImage,
                       dis.renameAction as renameAction
                       FROM DictionaryImageSize dis  JOIN Shop S ON dis.shopId=S.id";
        $datatable = new CDataTables($sql, ['id'], $_GET, true);

        $datatable->doAllTheThings('true');

        foreach ($datatable->getResponseSetData() as $key => $row) {
            if($row['renameAction']==1){
                $row['renameAction']='Rinominate Automaticamente';
            }else{
                $row['renameACtion']='Da Rinominare Manualmente';
            }

        }

        return $datatable->responseOut();
    }
}