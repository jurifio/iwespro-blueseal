<?php


namespace bamboo\blueseal\marketplace\prestashop;
use bamboo\core\exceptions\BambooException;

/**
 * Class CPrestashopShop
 * @package bamboo\blueseal\marketplace\prestashop
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 11/02/2019
 * @since 1.0
 */

class CPrestashopShop extends APrestashopMarketplace
{
    /**
     * @return array
     * @throws BambooException
     * @throws \PrestaShopWebserviceException
     */
    public function getAllPrestashopShops() : array {


       /** @var \SimpleXMLElement $shopsXml */
       $shopsXml = $this->ws->get(array('resource' => 'shops'));

       if($shopsXml instanceof \SimpleXMLElement){
           $ids = [];

           foreach ($shopsXml->children()->children() as $shopAtt){
               $ids[] = (int)$shopAtt['id'];
           }

           return $ids;
       } else throw new BambooException('Error while retriving shops');

    }

}