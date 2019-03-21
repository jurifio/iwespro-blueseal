<?php

namespace bamboo\controllers\api\classes;
use bamboo\controllers\api\AJWTManager;
use bamboo\utils\time\SDateToolbox;
use bamboo\utils\time\STimeToolbox;


/**
 * Class AApi
 * @package bamboo\controllers\api\classes
 * @author Iwes Team <it@iwes.it>, 18/03/2019
 * @copyright (c) Iwes snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class AApi extends AJWTManager
{

    protected $data;

    const GET = 'GET';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';

    /**
     * AApi constructor.
     * @param $app
     * @param $data
     * @throws \bamboo\core\exceptions\BambooConfigException
     * @throws \bamboo\core\exceptions\BambooException
     * @throws \bamboo\core\exceptions\RedPandaCookieException
     */
    public function __construct($app, $data)
    {
        parent::__construct();
        $this->data = $data;
    }

    /**
     * @param string $callType
     * @param string $source
     * @param string $severity
     * @param string $title
     * @param string|null $message
     * @param string $uniqueIdCall
     * @param int $idApiSite
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    protected function report(string $callType, string $source, string $severity, string $title, string $message = null, string $uniqueIdCall, int $idApiSite){

        \Monkey::app()->dbAdapter->insert('ApiLog',
            [
                'callType'=>$callType,
                'source'=>$source,
                'severity'=>$severity,
                'title'=>$title,
                'message'=>$message,
                'uniqueIdCall'=>$uniqueIdCall,
                'idApiSite'=>$idApiSite
            ]);
    }

    protected function checkIntervalForNextCall($callType, $source, $interval){

        $lastCall = \Monkey::app()->dbAdapter->query(
            'SELECT MAX(creationDate) as lastCall
                    FROM ApiLog
                    WHERE callType = ? AND source = ?',
            [$callType, $source]
        )->fetch();


        $now = date("Y-m-d H:i:s");
        $limitTime = date("Y-m-d H:i:s", strtotime('+' . $interval . ' seconds', strtotime($lastCall['lastCall'])));

        if($now > $limitTime) return true;

        return false;
    }

}