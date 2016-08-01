<?php
namespace bamboo\blueseal\controllers\ajax;

use bamboo\blueseal\business\CDataTables;
use bamboo\core\events\EGenericEvent;
use bamboo\core\intl\CLang;
use bamboo\domain\entities\CProduct;

/**
 * Class CMarketplaceProductResponse
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 01-08-2016
 * @since 1.0
 */
class CMarketplaceProductResponse extends AAjaxController
{
	public function get()
	{
		$prodCode = $this->app->router->request()->getRequestData('rows')[0];

		\BlueSeal::dump($prodCode);
		/** @var CProduct $prod */
		$prod = $this->app->repoFactory->create('Product')->getEmptyEntity();
		$prod->readId($prodCode);
		$prod = $this->app->repoFactory->create('Product')->findOneBy($prod->getIds());
		$response = "";
		foreach ($prod->marketplaceAccountHasProduct as $mahp) {
			$response.= $mahp->marketplaceAccount->marketplace->name .' - '.$mahp->marketplaceAccount->name.'<br>';
			$response.='-------------<br>';
			$response.='<textarea disabled="disabled" style="width:100%;height:300px">';
			$response.= $mahp->lastResponse.'</textarea><br>';
		}
		return $response;
	}
}