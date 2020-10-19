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
        try {
            $name = \Monkey::app()->router->request()->getRequestData('name');
            $surname =\Monkey::app()->router->request()->getRequestData('surname');
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
            $att1=\Monkey::app()->router->request()->getRequestData('att1');
            $att2 = \Monkey::app()->router->request()->getRequestData('att2');
            $att3 = \Monkey::app()->router->request()->getRequestData('att3');
            $att4 = \Monkey::app()->router->request()->getRequestData('att4');
            $att5 = \Monkey::app()->router->request()->getRequestData('att5');
            $att6 = \Monkey::app()->router->request()->getRequestData('att6');
            $att7 = \Monkey::app()->router->request()->getRequestData('att7');
            $att8 = \Monkey::app()->router->request()->getRequestData('att8');
            $att9 = \Monkey::app()->router->request()->getRequestData('att9');
            $att10 = \Monkey::app()->router->request()->getRequestData('att10');
            $att11 = \Monkey::app()->router->request()->getRequestData('att11');
            $att12 = \Monkey::app()->router->request()->getRequestData('att12');
            $att14 = \Monkey::app()->router->request()->getRequestData('att14');
            $att15 = \Monkey::app()->router->request()->getRequestData('att15');
            $att16 = \Monkey::app()->router->request()->getRequestData('att16');
            $att17 = \Monkey::app()->router->request()->getRequestData('att17');
            $att18 = \Monkey::app()->router->request()->getRequestData('att18');
            $att19 = \Monkey::app()->router->request()->getRequestData('att19');
            $att20 = \Monkey::app()->router->request()->getRequestData('att20');
            $att21 = \Monkey::app()->router->request()->getRequestData('att21');
            $att22 = \Monkey::app()->router->request()->getRequestData('att22');
            $att23 = \Monkey::app()->router->request()->getRequestData('att23');
            $att24 = \Monkey::app()->router->request()->getRequestData('att24');
            $att25 = \Monkey::app()->router->request()->getRequestData('att25');
            $att26 = \Monkey::app()->router->request()->getRequestData('att26');
            $att27 = \Monkey::app()->router->request()->getRequestData('att27');
            $att28 = \Monkey::app()->router->request()->getRequestData('att28');
            $att29 = \Monkey::app()->router->request()->getRequestData('att29');
            $att30 = \Monkey::app()->router->request()->getRequestData('att30');
            $att31 = \Monkey::app()->router->request()->getRequestData('att31');
            $att32 = \Monkey::app()->router->request()->getRequestData('att32');
            $att33 = \Monkey::app()->router->request()->getRequestData('att33');
            if(isset($att1)) {
                $att[1] = $att1;
            }
            if(isset($att2)) {
                $att[2] = $att2;
            }
            if(isset($att3)) {
                $att[3] = $att3;
            }
            if(isset($att4)) {
                $att[4] = $att4;
            }
            if(isset($att5)) {
                $att[5] = $att5;
            }
            if(isset($att6)) {
                $att[6] = $att6;
            }
            if(isset($att7)) {
                $att[7] = $att7;
            }
            if(isset($att8)) {
                $att[8] = $att8;
            }
            if(isset($att9)) {
                $att[9] = $att9;
            }
            if(isset($att10)) {
                $att[10] = $att10;
            }
            if(isset($att11)) {
                $att[11] = $att11;
            }
            if(isset($att12)) {
                $att[12] = $att12;
            }
            if(isset($att14)) {
                $att[14] = $att14;
            }
            if(isset($att15)) {
                $att[15] = $att15;
            }
            if(isset($att16)) {
                $att[16] = $att16;
            }
            if(isset($att17)) {
                $att[17] = $att17;
            }
            if(isset($att18)) {
                $att[18] = $att18;
            }
            if(isset($att19)) {
                $att[19] = $att19;
            }
            if(isset($att20)) {
                $att[20] = $att20;
            }
            if(isset($att21)) {
                $att[21] = $att21;
            }
            if(isset($att22)) {
                $att[22] = $att22;
            }
            if(isset($att23)) {
                $att[23] = $att23;
            }
            if(isset($att24)) {
                $att[24] = $att24;
            }
            if(isset($att25)) {
                $att[25] = $att25;
            }
            if(isset($att26)) {
                $att[26] = $att26;
            }
            if(isset($att27)) {
                $att[27] = $att27;
            }
            if(isset($att28)) {
                $att[28] = $att28;
            }
            if(isset($att29)) {
                $att[29] = $att29;
            }
            if(isset($att30)) {
                $att[30] = $att30;
            }
            if(isset($att31)) {
                $att[31] = $att31;
            }
            if(isset($att32)) {
                $att[32] = $att32;
            }
            if(isset($att33)) {
                $att[33] = $att33;
            }



            $attitude = [];

            foreach ($att as $k => $v) {
                if ($v) {
                    $attitude[] = $k;
                }
            }


            /** @var CRepo $fsrRepo */
            $fsrRepo = \Monkey::app()->repoFactory->create('FoisonSubscribeRequest');

            /** @var CFoisonSubscribeRequest $oldFsr */
            $oldFsr = $fsrRepo->findOneBy(["email" => $email]);

            if (!is_null($oldFsr)) return 'User alredy registred';

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
            $fsr->language = 'Inglese: ' . $eng . ' | Tedesco: ' . $dtc . ' | Russo: ' . $rus . ' | Cinese: ' . $chi . ' | Francese: ' . $fre;
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
        }catch(\Throwable $e){
            \Monkey::app()->applicationLog('requestsubscribe','Error','error api subscribe: ' . $e->getMessage(),$e->getLine(),$e->getCode());
            return false;
        }
    }

    public function post(){
        try {
            $name = \Monkey::app()->router->request()->getRequestData('name');
            $surname =\Monkey::app()->router->request()->getRequestData('surname');
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
            $att = [];
            $att1=\Monkey::app()->router->request()->getRequestData('att1');
            $att2 = \Monkey::app()->router->request()->getRequestData('att2');
            $att3 = \Monkey::app()->router->request()->getRequestData('att3');
            $att4 = \Monkey::app()->router->request()->getRequestData('att4');
            $att5 = \Monkey::app()->router->request()->getRequestData('att5');
            $att6 = \Monkey::app()->router->request()->getRequestData('att6');
            $att7 = \Monkey::app()->router->request()->getRequestData('att7');
            $att8 = \Monkey::app()->router->request()->getRequestData('att8');
            $att9 = \Monkey::app()->router->request()->getRequestData('att9');
            $att10 = \Monkey::app()->router->request()->getRequestData('att10');
            $att11 = \Monkey::app()->router->request()->getRequestData('att11');
            $att12 = \Monkey::app()->router->request()->getRequestData('att12');
            $att14 = \Monkey::app()->router->request()->getRequestData('att14');
            $att15 = \Monkey::app()->router->request()->getRequestData('att15');
            $att16 = \Monkey::app()->router->request()->getRequestData('att16');
            $att17 = \Monkey::app()->router->request()->getRequestData('att17');
            $att18 = \Monkey::app()->router->request()->getRequestData('att18');
            $att19 = \Monkey::app()->router->request()->getRequestData('att19');
            $att20 = \Monkey::app()->router->request()->getRequestData('att20');
            $att21 = \Monkey::app()->router->request()->getRequestData('att21');
            $att22 = \Monkey::app()->router->request()->getRequestData('att22');
            $att23 = \Monkey::app()->router->request()->getRequestData('att23');
            $att24 = \Monkey::app()->router->request()->getRequestData('att24');
            $att25 = \Monkey::app()->router->request()->getRequestData('att25');
            $att26 = \Monkey::app()->router->request()->getRequestData('att26');
            $att27 = \Monkey::app()->router->request()->getRequestData('att27');
            $att28 = \Monkey::app()->router->request()->getRequestData('att28');
            $att29 = \Monkey::app()->router->request()->getRequestData('att29');
            $att30 = \Monkey::app()->router->request()->getRequestData('att30');
            $att31 = \Monkey::app()->router->request()->getRequestData('att31');
            $att32 = \Monkey::app()->router->request()->getRequestData('att32');
            $att33 = \Monkey::app()->router->request()->getRequestData('att33');
            if(isset($att1)) {
                $att[1] = $att1;
            }
            if(isset($att2)) {
                $att[2] = $att2;
            }
            if(isset($att3)) {
                $att[3] = $att3;
            }
            if(isset($att4)) {
                $att[4] = $att4;
            }
            if(isset($att5)) {
                $att[5] = $att5;
            }
            if(isset($att6)) {
                $att[6] = $att6;
            }
            if(isset($att7)) {
                $att[7] = $att7;
            }
            if(isset($att8)) {
                $att[8] = $att8;
            }
            if(isset($att9)) {
                $att[9] = $att9;
            }
            if(isset($att10)) {
                $att[10] = $att10;
            }
            if(isset($att11)) {
                $att[11] = $att11;
            }
            if(isset($att12)) {
                $att[12] = $att12;
            }
            if(isset($att14)) {
                $att[14] = $att14;
            }
            if(isset($att15)) {
                $att[15] = $att15;
            }
            if(isset($att16)) {
                $att[16] = $att16;
            }
            if(isset($att17)) {
                $att[17] = $att17;
            }
            if(isset($att18)) {
                $att[18] = $att18;
            }
            if(isset($att19)) {
                $att[19] = $att19;
            }
            if(isset($att20)) {
                $att[20] = $att20;
            }
            if(isset($att21)) {
                $att[21] = $att21;
            }
            if(isset($att22)) {
                $att[22] = $att22;
            }
            if(isset($att23)) {
                $att[23] = $att23;
            }
            if(isset($att24)) {
                $att[24] = $att24;
            }
            if(isset($att25)) {
                $att[25] = $att25;
            }
            if(isset($att26)) {
                $att[26] = $att26;
            }
            if(isset($att27)) {
                $att[27] = $att27;
            }
            if(isset($att28)) {
                $att[28] = $att28;
            }
            if(isset($att29)) {
                $att[29] = $att29;
            }
            if(isset($att30)) {
                $att[30] = $att30;
            }
            if(isset($att31)) {
                $att[31] = $att31;
            }
            if(isset($att32)) {
                $att[32] = $att32;
            }
            if(isset($att33)) {
                $att[33] = $att33;
            }


            $attitude = [];

            foreach ($att as $k => $v) {
                if ($v) {
                    $attitude[] = $k;
                }
            }


            /** @var CRepo $fsrRepo */
            $fsrRepo = \Monkey::app()->repoFactory->create('FoisonSubscribeRequest');

            /** @var CFoisonSubscribeRequest $oldFsr */
            $oldFsr = $fsrRepo->findOneBy(["email" => $email]);

            if (!is_null($oldFsr)) return 'User alredy registred';

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
            $fsr->language = 'Inglese: ' . $eng . ' | Tedesco: ' . $dtc . ' | Russo: ' . $rus . ' | Cinese: ' . $chi . ' | Francese: ' . $fre;
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
        }catch(\Throwable $e){
            \Monkey::app()->applicationLog('requestsubscribe','Error','error api subscribe: ' . $e->getMessage(),$e->getLine(),$e->getCode());
            return false;
        }

    }

    public function put(){
    }

    public function delete(){
    }


}