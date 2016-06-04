<?php

namespace bamboo\blueseal\controllers\ajax;

/**
 * Class CJobLogDownloadController
 * @package bamboo\blueseal\controllers\ajax
 *
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>
 *
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @date $date
 * @since 1.0
 */
class CJobLogDownloadController extends AAjaxController
{
	/**
	 * @return string
	 */
	public function get()
	{
		try {
			$job = $this->app->router->request()->getRequestData('job');
			return file_get_contents($this->app->rootPath() . $this->app->cfg()->fetch('paths', 'tempFolder') . "/log" . $job . ".csv");
		} catch (\Exception $e) {
			var_dump($e);
		}

		return "";
	}

	public function post()
	{
		try {
			$job = $this->app->router->request()->getRequestData('job');
			$sql = "SELECT id, jobId from JobExecution where status = 'END' and  jobId = ? order by id desc limit 1";
			$a = $this->app->dbAdapter->query($sql, [$job])->fetchAll()[0];
			$sql = "SELECT jl.severity,jl.subject,jl.content,jl.context,jl.timestamp 
					FROM JobLog jl 
					WHERE jl.jobExecutionId = ? and jobId = ?
					ORDER BY jl.id ASC";
			$a = $this->app->dbAdapter->query($sql, [$a['id'],$a['jobId']])->fetchAll();
			$file = fopen($this->app->rootPath() . $this->app->cfg()->fetch('paths', 'tempFolder') . "/log" . $job . ".csv", 'w');
			foreach ($a as $x) {
				$x['context'] = explode(PHP_EOL, $x['context'])[0];
				fputs($file, '"'.implode('";"', $x).'"');
			};
			fflush($file);
			fclose($file);
		} catch (\Exception $e) {
			var_dump($e);
		}

		return $this->app->rootPath() . $this->app->cfg()->fetch('paths', 'tempFolder') . "/log" . $job . ".csv";
	}
}