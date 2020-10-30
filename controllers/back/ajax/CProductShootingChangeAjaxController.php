<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CDocument;
use bamboo\domain\entities\CInvoiceType;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CSectional;
use bamboo\domain\entities\CShooting;
use bamboo\domain\entities\CShootingBooking;
use bamboo\domain\entities\CShop;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CDocumentRepo;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CProductHasShootingRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CSectionalRepo;
use bamboo\domain\repositories\CShootingBookingRepo;
use bamboo\domain\repositories\CShootingRepo;
use bamboo\domain\repositories\CShopRepo;


/**
 * Class CProductShootingChangeAjaxController
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 23/03/2018
 * @since 1.0
 */
class CProductShootingChangeAjaxController extends AAjaxController
{
    /**
     * @return string
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooInvoiceException
     */
    public function post()
    {
        $res = "";
        $data = \Monkey::app()->router->request()->getRequestData();
        $productsIds = $data["products"];
        $newShootingId = $data["shootingId"];


        foreach ($productsIds as $product) {
            $codeProduct = explode('-',$product);
            $phs = \Monkey::app()->repoFactory->create('ProductHasShooting')->findOneBy(['productId' => $codeProduct[0],'productVariantId' => $codeProduct[1]]);
            $shootingId = $phs->shootingId;
            $phs->shootingId = $newShootingId;
            $phs->update();
            $res .= 'prodotto ' . $codeProduct[0] . '-' . $codeProduct[1] . '  Associato  dallo Shooting ' . $shootingId . '</br>';
            $shooting = \Monkey::app()->repoFactory->create('Shooting')->findOneBy(['id' => $shootingId]);
            $lastPieces = $shooting->pieces;
            $totPieces = $lastPieces - 1;
            $shooting->pieces = $totPieces;
            $shooting->update();
            $res .= 'Aggiornato il totale dei pezzi a ' . $totPieces . ' dello  shooting cambiato con  numero ' . $shootingId.'</br>' ;
            $shooting = \Monkey::app()->repoFactory->create('Shooting')->findOneBy(['id' => $newShootingId]);
            $lastPieces = $shooting->pieces;
            $totPieces = $lastPieces + 1;
            $shooting->pieces = $totPieces;
            $shooting->update();
            $res .= 'Aggiornato il totale dei pezzi a ' . $totPieces . ' dello  shooting aggiornato con  numero ' . $newShootingId.'</br>' ;

        }


        return $res;
    }


}