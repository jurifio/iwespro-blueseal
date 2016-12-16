<?php
namespace bamboo\blueseal\jobs;

use bamboo\core\exceptions\BambooException;
use bamboo\core\jobs\ACronJob;

/**
 * Class CDispatchPreorderToFriend
 * @package bamboo\blueseal\jobs
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, ${DATE}
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CStatisticsGenerateFilesForQlik extends ACronJob
{
    /**
     * @param null $args
     */
    public function run($args = null)
    {
        $path = $this->app->rootPath()
            . $this->app->cfg()->fetch('paths', 'exportedStatistics');

        $files = scandir($path);

        foreach($files as $v) {
            if ('.' === $v || '..' === $v) continue;
            unlink($path . $v);
        }

        $sql = [];

        $sql['ordini'] =
            "SELECT * FROM vQlikOrdini";


        $dba = \Monkey::app()->dbAdapter;
        foreach($sql as $k => $v) {
            $res = $dba->query($v, [])->fetchAll();
            $file = fopen($path . $k . '.csv', 'x');
            if (!$file) throw new BambooException('Can\'t create the file');
            $fieldNames = [];
            foreach($res[0] as $fk => $fv) {
                $fieldNames[] = $fk;
            }
            array_unshift($res, $fieldNames);
            reset($res);
            foreach($res as $fields) {
                fputcsv($file, $fields, ';', '"', "\\");
            }
            $this->report('file statistiche', 'file ' . $k .'.csv creato');
            fclose($file);
        }
    }

}