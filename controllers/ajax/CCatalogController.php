<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\core\intl\CLang;
use bamboo\core\theming\CRestrictedAccessWidgetHelper;
use bamboo\ecommerce\views\widget\VBase;

/**
 * Class CUserSellRecapController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 2016/04/08
 * @since 1.0
 */
class CCatalogController extends AAjaxController
{
	protected $urls = [];
	protected $authorizedShops = [];
	protected $em;

	/**
	 * @param $action
	 * @return mixed
	 */
	public function createAction($action)
	{
		$this->app->setLang(new CLang(1, 'it'));
		$this->urls['base'] = $this->app->baseUrl(false) . "/blueseal/";
		$this->urls['page'] = $this->urls['base'] . "prodotti/movimenti/inserisci";
		$this->urls['dummy'] = $this->app->cfg()->fetch('paths', 'dummyUrl');

		$this->em = new \stdClass();
		$this->em->products = $this->app->entityManagerFactory->create('Product');

		return $this->{$action}();
	}

	public function get()
    {
        $search = $this->app->router->request()->getRequestData('search');
        if (false !== strpos($search, '-')) $type = 'code';
        elseif (false !== strpos($search, '#')) $type = 'cpf';
        elseif ((0 == strpos($search, '12')) && (10 == strlen($search))) $type = 'barcode';
        $shop = $this->app->getUser()->shop;
        foreach($shop as $v) {
            $shopId = $v->id;
        }
        $skuRepo = $this->rfc('ProductSku');
        $variantRepo = $this->rfc('productVariant');
        $prodRepo = $this->rfc('Product');
        $sizesToMove = [];

        switch($type) {
            case 'code':
                $prod = $prodRepo->findOneByStringId($search);
                break;
            case 'cpf':
                list($itemno, $variant) = explode('#', $search);
                $itemno = trim($itemno);
                $variant = trim($variant);
                $prodVariant = $variantRepo->findOneBy(['name' => $variant]);
                $prod = $prodRepo->findOneBy(['itemno' => $itemno, 'productVariantId' => $prodVariant->id]);
                break;
            case 'barcode':
                $sku = $skuRepo->findOneBy(['barcode' => $search]);
                $sizesToMove[$sku->productSizeId] = 1;
                $prod = $sku->product;
                break;
        }
        $ret = ($prod) ? $this->getAllProductData($prod, $shopId, $sizesToMove) : false;

        return json_encode($ret);
    }

    private function getAllProductData($em, $shopId, $sizesToMove = []){
        $arrRet = $em->toArray();
        $arrRet['productVariant'] = $em->productVariant->name;
        $arrRet['sizes'] = [];
        $arrRet['sku'] = [];
        foreach($em->productSizeGroup->productSize as $v){
            $arrRet['sizes'][$v->id] = $v->name;
        }
        foreach($em->productSku as $v) {
            if (($v->stockQty) && ($shopId == $v->shopId))  {
                $arrRet['sku'][$v->productSizeId] = $v->stockQty;
            }
        }
        $arrRet['moves'] = [];
        foreach($sizesToMove as $k => $v) {
            $arrRet['moves'][$k] = $v;
        }
        return $arrRet;
    }

    public function post() {
        $get = $this->app->router->request()->getRequestData();
        try {

            $prodEm = $this->rfc('Product');
            $skuEm = $this->rfc('ProductSku');
            $SOEm = $this->rfc('StorehouseOperation');
            $SOLEm = $this->rfc('StorehouseOperationLine');
            $SOCEm = $this->rfc('StorehouseOperationCause');
            $SEm = $this->rfc('Storehouse');

            $user = $this->app->getUser();
            $shop = $user->shop->getFirst();


            if (isset($get['storehouseId'])) {
                $storehouse = $SEm->findOneBy(['id' => $get['storehouseId']]);
            }
            else {
                $storehouse = $SEm->findOneBy(['shopId' => $shop->id]);
            }

            if (!$storehouse) throw new \Exception('il Magazzino impostato non esiste nel sistema');

            if (!$SOC = $SOCEm->findOne([$get['cause']])) throw new \Exception('La causale è obbligatoria');

            $this->app->dbAdapter->beginTransaction();

            $newOp = $SOEm->getEmptyEntity();
            $newOp->shopId = $shop->id;
            $newOp->storehouseId = $storehouse->id;
            $newOp->storehouseOperationCauseId = $get['cause'];
            $newOp->userId = $user->id;
            $newOp->operationDate = date("Y-m-d H:i:s", strtotime($get['date']));
            $operationId = $newOp->insert();

        foreach($get['products'] as $v) {
            $prod = $prodEm->findOneBy(['productVariantId' => $v['productVariantId']]);
            if (!$prod) throw new \Exception('Prodotto non trovato! Codice fornito: ' . $v['id'] . '-' . $v['productVariantId']);
            /*
             * RECUPERI I PREZZI: controllo prima se c'è lo sku da aggiornare. Se c'è aggiorno quello.
             * Se non c'è, cerco il prezzo sugli altri sku. Se il prezzo non è uguale per tutti gli sku parte l'eccezione.
             * Se non ci sono altri sku, cerco il prezzo dalla tabella shopHasProduct
             * Se non c'è neanche lì niente prezzo e altra eccezione.
             */
            $allSkus = $skuEm->findBy([
                'productVariantId' => $v['productVariantId'],
                'productId' => $v['id'],
                'shopId' => $shop->id,
            ]);
            $value = 0;
            $price = 0;
            $salePrice = 0;
            $onSale = null;
            $isUsable = null;
            $i = 0;


            foreach($allSkus as $s) {
                $isUsable = true;
                if (0 == $i) {
                    $value = $s->value;
                    $price = $s->price;
                    $salePrice = $s->salePrice;
                    $onSale = $s->isOnSale;
                } elseif (
                        ($price != $s->price) ||
                        ($salePrice != $s->salePrice) ||
                        ($onSale != $s->isOnSale)
                    ) {
                    $isUsable = false;
                    break;
                }
                $i++;
            }
            //if (!$isUsable) throw new \Exception('Non riesco ad assegnare il prezzo di uno dei prodotti. L\'inserimento dei movimenti non è andato a buon fine');

            if (false === $isUsable) {
                $shp = $prod->shopHasProduct->findOneByKeys([
                    'productVariantId' => $v['productVariantId'],
                    'productId' => $v['id'],
                    'shopId' => $shop->id,
                ]);
                if ($shp) {
                    $price = $shp->price;
                    $value = $shp->value;
                    $salePrice = $shp->salePrice;
                } else {
                    throw new \Exception("Il prezzo di uno o più prodotti in elenco non è stato impostato. I movimenti non sono stati registrati");
                }
            }

            //per ogni movimento
            foreach($v['movements'] as $mov) {

                //modifico le quantità negli sku
                $actualSku = $allSkus->findOneByKeys([
                    'productVariantId' => $v['productVariantId'],
                    'shopId' => $shop->id,
                    'productSizeId' => $mov['size']
                ]);
                if (!$actualSku) {

                    if (0 > $mov['qtMove']) throw new \Exception(
                        'Impossibile togliere quantità di un articolo mai caricato: ' . $v['id'] . '-' . $v['productVariantId']
                    );
                    $newSku = $skuEm->getEmptyEntity();
                    $newSku->productId = $v['id'];
                    $newSku->productVariantId = $v['productVariantId'];
                    $newSku->productSizeId = $mov['size'];
                    $newSku->shopId = $shop->id;
                    $newSku->currencyId = 1;
                    //$newSku->barcode = null; //TODO barcodes;
                    $newSku->value = $value;
                    $newSku->price = $price;
                    $newSku->salePrice = $salePrice;
                    $newSku->isOnSale = (null === $onSale) ? 0 : $onSale;
                    $newSku->insert();
                } else {
                    if (0 > $actualSku->stockQty + $mov['qtMove']) throw new \Exception('I movimenti non possono portare le quantità in stock in negativo');
                    $actualSku->stockQty = $actualSku->stockQty + $mov['qtMove'];
                    $actualSku->update();
                }

                //inserisco il movimento
                $SOL = $SOLEm->getEmptyEntity();
                $SOL->storehouseOperationId = $operationId;
                $SOL->shopId = $shop->id;
                $SOL->storehouseId = $storehouse->id;
                $SOL->productId = $v['id'];
                $SOL->productVariantId = $v['productVariantId'];
                $SOL->productSizeId = $mov['size'];
                $SOL->qty = $mov['qtMove'];
                $SOL->insert();
            }
        }
        $this->app->dbAdapter->commit();
        } catch (\Exception $e) {
            $this->app->dbAdapter->rollBack();
            return $e->getMessage();
        }
    }
}