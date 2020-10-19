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
        $surname = $name;
        $nickName = \Monkey::app()->router->request()->getRequestData('nickName');
        $birthday = \Monkey::app()->router->request()->getRequestData('birthday');
        $phone = \Monkey::app()->router->request()->getRequestData('phone');
        $address = \Monkey::app()->router->request()->getRequestData('address');
        $gender = \Monkey::app()->router->request()->getRequestData('gender');
        $email = \Monkey::app()->router->request()->getRequestData('email');
        $actualWorkPosition = \Monkey::app()->router->request()->getRequestData('actualWorkPosition');
        $eng = \Monkey::app()->router->request()->getRequestData('eng');
        $dtc = \Monkey::app()->router->request()->getRequestData('dtc');
        $rus = \Monkey::app()->router->request()->getRequestData('rus');
        $chi = \Monkey::app()->router->request()->getRequestData('chi');
        $fre = \Monkey::app()->router->request()->getRequestData('fre');


        $att = [];
        $att[1] = \Monkey::app()->router->request()->getRequestData('att1');
        $att[2] = \Monkey::app()->router->request()->getRequestData('att2');
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
        $att[14] = \Monkey::app()->router->request()->getRequestData('att14');
        $att[15] = \Monkey::app()->router->request()->getRequestData('att15');
        $att[16] = \Monkey::app()->router->request()->getRequestData('att16');
        $att[17] = \Monkey::app()->router->request()->getRequestData('att17');
        $att[18] = \Monkey::app()->router->request()->getRequestData('att18');
        $att[19] = \Monkey::app()->router->request()->getRequestData('att19');
        $att[20] = \Monkey::app()->router->request()->getRequestData('att20');
        $att[21] = \Monkey::app()->router->request()->getRequestData('att21');
        $att[22] = \Monkey::app()->router->request()->getRequestData('att22');
        $att[23] = \Monkey::app()->router->request()->getRequestData('att23');
        $att[24] = \Monkey::app()->router->request()->getRequestData('att24');
        $att[25] = \Monkey::app()->router->request()->getRequestData('att25');
        $att[26] = \Monkey::app()->router->request()->getRequestData('att26');
        $att[27] = \Monkey::app()->router->request()->getRequestData('att27');
        $att[28] = \Monkey::app()->router->request()->getRequestData('att28');
        $att[29] = \Monkey::app()->router->request()->getRequestData('att29');
        $att[30] = \Monkey::app()->router->request()->getRequestData('att30');
        $att[31] = \Monkey::app()->router->request()->getRequestData('att31');
        $att[32] = \Monkey::app()->router->request()->getRequestData('att32');
        $att[33] = \Monkey::app()->router->request()->getRequestData('att33');



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
        $fsr->language = 'Inglese: ' . $eng . ' | Tedesco: ' . $dtc. ' | Russo: ' . $rus. ' | Cinese: ' . $chi. ' | Francese: ' . $fre;
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