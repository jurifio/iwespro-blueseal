<?php

namespace bamboo\controllers\back\ajax;

use bamboo\core\base\CObjectCollection;
use bamboo\core\exceptions\BambooException;
use bamboo\core\exceptions\BambooOrderLineException;
use bamboo\core\exceptions\BambooShipmentException;
use bamboo\domain\entities\CEmailTemplateTag;
use bamboo\domain\repositories\COrderLineRepo;
use bamboo\domain\repositories\CShipmentRepo;
use bamboo\utils\time\STimeToolbox;
use DateTime;
use PDO;
use PDOException;

/**
 * Class CEmailTemplateTagSelectAjaxController
 * @package bamboo\controllers\back\ajax
 */
class CEmailTemplateTagSelectAjaxController extends AAjaxController
{
    public function get()
    {
        $emailTemplateTags=\Monkey::app()->repoFactory->create('EmailTemplateTag')->findAll();
        $tags = [];

            foreach ($emailTemplateTags as $emailTemplateTag) {

                array_push($tags,['id'=>$emailTemplateTag->id,
                                         'tagTemplate'=>$emailTemplateTag->tagTemplate,
                                         'tagName'=>$emailTemplateTag->tagName,
                                         'tagDescription'=>$emailTemplateTag->tagDescription]);
            }

        return json_encode($tags);
    }
}