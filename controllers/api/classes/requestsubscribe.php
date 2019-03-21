<?php

namespace bamboo\controllers\api\classes;
use bamboo\controllers\api\AJWTManager;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\domain\entities\CFoisonSubscribeRequest;
use bamboo\domain\entities\CFoisonSubscribeRequestHasWorkCategory;


/**
 * Class orders
 * @package bamboo\controllers\api
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 20/07/2018
 * @since 1.0
 */
class requestsubscribe extends AJWTManager
{

    public function createAction($action)
    {
        if(!is_null($this->auth)){
            return $this->auth;
        }
        return $this->{$action}();
    }

    public function get(){
    }

    public function post(){
        $name = \Monkey::app()->router->request()->getRequestData('name');
        $surname = \Monkey::app()->router->request()->getRequestData('surname');
        $nickName = \Monkey::app()->router->request()->getRequestData('nickName');
        $birthday = \Monkey::app()->router->request()->getRequestData('birthday');
        $phone = \Monkey::app()->router->request()->getRequestData('phone');
        $address = \Monkey::app()->router->request()->getRequestData('address');
        $gender = \Monkey::app()->router->request()->getRequestData('gender');
        $email = \Monkey::app()->router->request()->getRequestData('email');
        $actualWorkPosition = \Monkey::app()->router->request()->getRequestData('actualWorkPosition');
        $eng = \Monkey::app()->router->request()->getRequestData('eng');
        $dtc = \Monkey::app()->router->request()->getRequestData('dtc');

        $att = [];
        $att[1] = \Monkey::app()->router->request()->getRequestData('att1');
        $att[3] = \Monkey::app()->router->request()->getRequestData('att3');
        $att[4] = \Monkey::app()->router->request()->getRequestData('att4');
        $att[5] = \Monkey::app()->router->request()->getRequestData('att5');
        $att[6] = \Monkey::app()->router->request()->getRequestData('att6');
        $att[7] = \Monkey::app()->router->request()->getRequestData('att7');
        $att[8] = \Monkey::app()->router->request()->getRequestData('att8');
        $att[9] = \Monkey::app()->router->request()->getRequestData('att9');
        $att[10] = \Monkey::app()->router->request()->getRequestData('att10');
        $att[11] = \Monkey::app()->router->request()->getRequestData('att11');
        $att[12] = \Monkey::app()->router->request()->getRequestData('att12');


        $attitude = [];

        foreach ($att as $k=>$v){
            if($v){
                $attitude[] = $k;
            }
        }


        /** @var CRepo $fsrRepo */
        $fsrRepo = \Monkey::app()->repoFactory->create('FoisonSubscribeRequest');

        /** @var CFoisonSubscribeRequest $oldFsr */
        $oldFsr = $fsrRepo->findOneBy(["email" => $email]);

        if(!is_null($oldFsr)) return 'User alredy registred';

        /** @var CFoisonSubscribeRequest $fsr */
        $fsr = $fsrRepo->getEmptyEntity();
        $fsr->name = $name;
        $fsr->surname = $surname;
        $fsr->nickName = $nickName;
        $fsr->birthday = $birthday;
        $fsr->phone = $phone;
        $fsr->gender = $gender;
        $fsr->address = $address;
        $fsr->email = $email;
        $fsr->actualWorkPosition = $actualWorkPosition;
        $fsr->language = 'Inglese: ' . $eng . ' | Tedesco: ' . $dtc;
        $fsr->smartInsert();

        /** @var CRepo $reqCatRepo */
        $reqCatRepo = \Monkey::app()->repoFactory->create('FoisonSubscribeRequestHasWorkCategory');

        foreach ($attitude as $sAttitude) {
            $reqCat = $reqCatRepo->getEmptyEntity();
            $reqCat->foisonSubscribeRequestId = $fsr->id;
            $reqCat->workCategoryId = $sAttitude;
            $reqCat->smartInsert();
        }

        return true;

    }

    public function put(){
    }

    public function delete(){
    }


}