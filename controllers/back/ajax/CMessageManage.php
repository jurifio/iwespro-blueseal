<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\db\pandaorm\repositories\CRepo;
use bamboo\core\exceptions\BambooException;
use bamboo\domain\entities\CFoison;
use bamboo\domain\entities\CMessage;
use bamboo\domain\entities\CMessageHasUser;
use bamboo\domain\entities\CProductSizeGroup;
use bamboo\domain\entities\CProductSizeMacroGroup;
use bamboo\domain\entities\CUser;
use bamboo\domain\repositories\CEmailRepo;
use bamboo\domain\repositories\CFoisonRepo;
use bamboo\domain\repositories\CProductSizeGroupRepo;
use bamboo\domain\repositories\CProductSizeRepo;
use bamboo\domain\repositories\CUserRepo;


/**
 * Class CMessageManage
 * @package bamboo\controllers\back\ajax
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 31/05/2018
 * @since 1.0
 */
class CMessageManage extends AAjaxController
{
    /**
     * @return string
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function post()
    {
        $title = \Monkey::app()->router->request()->getRequestData('title');
        $mex = \Monkey::app()->router->request()->getRequestData('mex');
        $pr = \Monkey::app()->router->request()->getRequestData('pr');

        if(empty($title) || empty($mex)) return 'Inserisci sia il titolo che il corpo del messagio';

        /** @var CRepo $messageRepo */
        $messageRepo = \Monkey::app()->repoFactory->create('Message');


        /** @var CMessage $message */
        $message = $messageRepo->getEmptyEntity();
        $message->title = $title;
        $message->text = $mex;
        $message->priority = $pr;
        $message->smartInsert();

        $prName = '';

        switch ($pr){
            case 'L':
                $prName = 'Bassa';
                break;
            case 'M':
                $prName = 'Media';
                break;
            case 'H':
                $prName = 'Alta';
                break;
        }

        /** @var CObjectCollection $foisons */
        $foisons = \Monkey::app()->repoFactory->create('Foison')->findAll();

        /** @var CEmailRepo $mail */
        $mail = \Monkey::app()->repoFactory->create('Email');


        $url = $this->app->baseUrl(false) . "/blueseal/message/".$message->id;

        $body = "Un nuovo messaggio con priorità $prName è stato postato all'interno della tua area
                 riservata, accedi con il seguente link per visualizzarne il contenuto.<br>Link messaggio: 
                <a href='$url' target='_blank'>Vai al messaggio</a><br><br>
                Iwes Operator Team";
        /** @var CFoison $foison */
        foreach ($foisons as $foison){
            $mail->newMail('gianluca@iwes.it', [$foison->email], [],[], 'Nuovo messaggio da Pickyshop', $body);
        }



        return 'Messaggio inviato con successo';
    }


    /**
     * @return string
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function put()
    {
        $title = \Monkey::app()->router->request()->getRequestData('title');
        $mex = \Monkey::app()->router->request()->getRequestData('mex');
        $pr = \Monkey::app()->router->request()->getRequestData('pr');
        $mesId = \Monkey::app()->router->request()->getRequestData('m');

        if(empty($title) || empty($mex)) return 'Inserisci sia il titolo che il corpo del messagio';

        /** @var CRepo $messageRepo */
        $messageRepo = \Monkey::app()->repoFactory->create('Message');


        /** @var CMessage $message */
        $message = $messageRepo->findOneBy(['id'=>$mesId]);
        $message->title = $title;
        $message->text = $mex;
        $message->priority = $pr;
        $message->update();

        /** @var CObjectCollection $foisons */
        $foisons = \Monkey::app()->repoFactory->create('Foison')->findAll();

        /** @var CEmailRepo $mail */
        $mail = \Monkey::app()->repoFactory->create('Email');

        $url = $this->app->baseUrl(false) . "/blueseal/message/".$message->id;

        $body = "Il messaggio postato nella tua area riservata è stato modificato, 
                accedi con il seguente link per visualizzarne il contenuto.<br>Link messaggio: 
                <a href='$url' target='_blank'>Vai al messaggio</a><br><br>
                Iwes Operator Team";
        $title = "Il messaggio n. $mesId è stato modificato";
        /** @var CFoison $foison */
        foreach ($foisons as $foison){
            $mail->newMail('gianluca@iwes.it', [$foison->email], [],[], $title, $body);
        }



        return 'Messaggio modificato con successo';
    }

    /**
     * @throws BambooException
     * @throws \bamboo\core\exceptions\BambooORMInvalidEntityException
     * @throws \bamboo\core\exceptions\BambooORMReadOnlyException
     */
    public function delete() {

        $ids = \Monkey::app()->router->request()->getRequestData('ids');

        /** @var CRepo $mesR */
        $mesR = \Monkey::app()->repoFactory->create('Message');

        foreach ($ids as $id){
            /** @var CMessage $mes */
            $mes = $mesR->findOneBy(['id'=>$id]);

            /** @var CObjectCollection $seens */
            $seens = $mes->messageHasUser;

            /** @var CMessageHasUser $seen */
            foreach ($seens as $seen){
                $seen->delete();
            }

            $mes->delete();
        }

        return 'Tutti i messaggi sono stati eliminati con successo';

    }

}