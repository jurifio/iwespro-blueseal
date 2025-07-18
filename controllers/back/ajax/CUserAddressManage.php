<?php
namespace bamboo\controllers\back\ajax;

/**
 * Class CUserAddressList
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Iwes  International Web Ecommerce ServicesTeam <juri@iwes.it>
 *
 * @copyright (c) Iwes International Web Ecommerce Services - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 22/07/2016
 * @since 1.0
 */
class CUserAddressManage extends AAjaxController
{
    /**
     * @return string
     */
	public function get()
	{
        $list = [];
        $user = \Monkey::app()->repoFactory->create('User')->findOneBy(['id'=>$this->app->router->request()->getRequestData('userId')]);
        foreach ($user->userAddress as $userAddress) {
            $address = $userAddress->toArray();
            $country=\Monkey::app()->repoFactory->create('Country')->findOneBy(['id'=>$userAddress->countryId]);
            $address['countryName']=$country->name;
            $address['label'] = $userAddress->name.' '.$userAddress->surname.' - '.$userAddress->address;
            $list[] = $address;
        }
        return json_encode($list);
	}

    /**
     * @return mixed
     */
	public function post()
    {
        $data = $this->app->router->request()->getRequestData();
        $userAddress = \Monkey::app()->repoFactory->create('UserAddress')->getEmptyEntity();
        $userAddress->userId = $data['user_id'];
        $userAddress->name = $data['user_address_name'];
        $userAddress->surname = $data['user_address_surname'];
        $userAddress->phone = $data['user_address_phone'];
        $userAddress->countryId = $data['user_address_country'];
        $userAddress->address = $data['user_address_address'];
        $userAddress->extra = $data['user_address_address2'];
        $userAddress->postcode = $data['user_address_postcode'];
        $userAddress->province = $data['user_address_province'];
        $userAddress->city = $data['user_address_city'];
        $userAddress->isBilling = 1;
        return $userAddress->insert();
    }
}