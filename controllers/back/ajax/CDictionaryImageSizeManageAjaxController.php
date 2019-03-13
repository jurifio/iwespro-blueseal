<?php

namespace bamboo\controllers\back\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CDictionaryImageSize;

/**
 * Class CDictionaryImageSizeManageAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 12/03/2019
 * @since 1.0
 */
class CDictionaryImageSizeManageAjaxController extends AAjaxController
{


    Public function post()
    {
        $data = $this->app->router->request()->getRequestData();
        $shopId = $data["shopId"];
        $widthImage = $data["widthImage"];
        $heightImage = $data["heightImage"];
        $widhtImageCopy = $data["widthImageCopy"];
        $heightImageCopy = $data['heightImageCopy'];
        $divisionByX = $data["divisionByX"];
        $divisionByY = $data["divisionByY"];
        $widthPercentageVariation = $data["widthPercentageVariation"];
        $heightPercentageVariation = $data["heightPercentageVariation"];
        $destinationfile = $data["destinationfile"];
        $renameAction = $data["renameAction"];
        $useDivision = $data["useDivision"];
        $destinationXPoint = $data["destinationXPoint"];
        $destinationYPoint = $data["destinationYPoint"];
        $emptyZero = $data["emptyZero"];
        $coverImage = $data["coverImage"];

        /**
         * @var CRepo $repoDictionaryImageSizeRepo
         **/
        $repoDictionaryImageSizeRepo = \Monkey::app()->repoFactory->create('DictionaryImageSize');

        $finddictionaryImageSize =$repoDictionaryImageSizeRepo->findOneBy(['shopId'=>$shopId]);
        if(is_null($finddictionaryImageSize)) {
            /** @var CDictionaryImageSize $dictionaryImageSize */
            $dictionaryImageSize = $repodictionaryImageSizeRepo->getEmptyEntity();
            $dictionaryImageSize->shopId=$shopId;
            $dictionaryImageSize->widthImage=$widthImage;
            $dictionaryImageSize->heightImage=$heightImage;
            $dictionaryImageSize->widhtImageCopy=$widhtImageCopy;
            $dictionaryImageSize->heightImageCopy=$heightImageCopy;
            $dictionaryImageSize->divisionByX=$divisionByX;
            $dictionaryImageSize->divisionByY=$divisionByY;
            $dictionaryImageSize->widhtPercentageVariation=$widthPercentageVariation;
            $dictionaryImageSize->heightPercentageVariation=$heightPercentageVariation;
            $dictionaryImageSize->destinationfile=$destinationfile;
            $dictionaryImageSize->renameAction=$renameAction;
            $dictionaryImageSize->useDivision=$useDivision;
            $dictionaryImageSize->destinationXPoint=$destinationXPoint;
            $dictionaryImageSize->destinationYPoint=$destinationYPoint;
            $dictionaryImageSize->emptyZero=$emptyZero;
            $dictionaryImageSize->coverImage=$coverImage;
            $dictionaryImageSize->smartInsert();
            $res='Inserimento parametri elaborazione Immagini Eseguita Correttamente';
        }else{
            $res='Esiste Già una parametrizzazione per lo Shop Selezionato';
        }
return $res;
    }
}