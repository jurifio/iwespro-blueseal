<?php
namespace bamboo\controllers\back\ajax;

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

				/** @var CProduct $prod */
		$prod = \Monkey::app()->repoFactory->create('Product')->getEmptyEntity();
		$prod->readId($prodCode);
		$prod = \Monkey::app()->repoFactory->create('Product')->findOneBy($prod->getIds());
		$response = "";
		foreach ($prod->marketplaceAccountHasProduct as $mahp) {
			$response.= $mahp->marketplaceAccount->marketplace->name .' - '.$mahp->marketplaceAccount->name.'<br>';
			$response.='-------------<br>';
			$response.='<textarea disabled="disabled" style="width:100%;height:200px">';
			$response.= $mahp->lastResponse.'</textarea><br>';
		}
		return $response;
	}
}