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
class CBankRegistryData extends AAjaxController
{
	public function get()
	{
                $list = [];
      $banks= \Monkey::app()->repoFactory->create('BankRegistry')->findAll();
                foreach ($banks as $bank) {
                    $list[] = ['id'=>$bank->id,
                        'abi'=>$bank->abi,
                        'cab'=> $bank->cab,
                        'name'=> $bank->name,
                        'location'=> $bank->location];
                }
                return json_encode($list);



	}
}