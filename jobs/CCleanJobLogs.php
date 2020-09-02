<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\jobs\ACronJob;

/**
 * Class CCleanLogs
 * @package bamboo\blueseal\jobs
 *
 * @author Iwes Team <it@iwes.it>
 *
 * @copyright (c) Iwes  snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date 14/05/2018
 * @since 1.0
 */
class CCleanJobLogs extends ACronJob
{
    /**
     * @param null $args
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function run($args = null)
    {
        $this->deleteJobLogs();
    }

    /**
     * @param int $days
     * @throws \bamboo\core\exceptions\BambooDBALException
     */
    public function deleteJobLogs($days = 3)

    {
        if (ENV === 'prod') {
            $db_host = '5.189.159.187';
            $db_name = 'pickyshopfront';
            $db_user = 'pickyshop4';
            $db_pass = 'rrtYvg6W!';
        } else {
            $db_host = 'localhost';
            $db_name = 'iwesPrestaDB';
            $db_user = 'root';
            $db_pass = 'geh44fed';
        }
        $res = "";
        try {
            $db_con = new PDO("mysql:host={$db_host};dbname={$db_name}",$db_user,$db_pass);
            $db_con->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $res = " connessione ok <br>";
        } catch (PDOException $e) {
            $res = $e->getMessage();
        }
        try {
            $CCleanJobLogs = $db_con->prepare('delete from JobLog');
            $CCleanJobLogs->execute();
            \Monkey::app()->jobLog('CCleanJobLogs','success','Deleting JobLog',$e->getMessage());
        }catch(\Throwable $e){
            \Monkey::app()->jobLog('CCleanJobLogs','error','Error Deleting JobLog',$e->getMessage());
        }

    }
}