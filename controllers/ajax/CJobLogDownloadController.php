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
		$job = $this->app->router->request()->getRequestData('job');
		$sql = "SELECT jl.severity,jl.subject,jl.content,jl.context,jl.timestamp from Job j, JobExecution je, JobLog jl WHERE j.id= je.jobId and je.jobId = jl.jobId and je.id = jl.jobExecutionId and j.id = ? and je.status = 'END' order by je.id desc, jl.id asc";
		$a = $this->app->dbAdapter->query($sql,[$job])->fetchAll();
		$output = "";
		foreach($a as $x) {
			$output.=implode(';',$x)."\n";
		};
		return $output;
	}
}