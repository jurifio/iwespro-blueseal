<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CBankRegistryData
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/07/2016
 * @since 1.0
 */
class CCurrencyData extends AAjaxController
{
	public function get()
	{
                $list = [];
      $currencys= \Monkey::app()->repoFactory->create('Currency')->findAll();
                foreach ($currencys as $currency) {
                    $list[] = ['id'=>$currency->id,
                        'code'=>$currency->code
                       ];
                }
                return json_encode($list);



	}
}