<?php

namespace bamboo\controllers\back\ajax;

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
		} catch (\Throwable $e) {
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
					WHERE jl.jobExecutionId = ? and jobId = ? AND jl.content LIKE '%Error while linking skus for product%'
					ORDER BY jl.id ASC";
			$a = $this->app->dbAdapter->query($sql, [$a['id'],$a['jobId']])->fetchAll();
			$file = fopen($this->app->rootPath() . $this->app->cfg()->fetch('paths', 'tempFolder') . "/log" . $job . ".csv", 'w');
			$lines = [];
			foreach ($a as $x) {
				$contents = explode('Id ', $x['content']);
				$id = explode(" - ", $contents[1])[0];
				$variantId = $contents[2];
				//$lines['content'] =  $id . "-" . $variantId;

				$prod = $this->app->repoFactory->create('Product')->findOneBy(['id' => $id, 'productVariantId' => $variantId]);
				$brand = $prod->productBrand->name;
				$shops = [];
				foreach($prod->shop as $shop) $shops[] = $shop->name;
				$shops = implode(', ', $shops);

				//$sizeGroup = $prod->productSizeGroup->name;
				$lines['code'] = $id . "-" . $variantId;
				$lines['brand'] = $brand;
				$lines['shops'] = $shops;

				//$lines['content'] = ": " . $brand . " - " . $shops . " - " ;

				$lines['context'] = explode(', id:', $x['context'])[0];
				fputs($file, '"'.implode('";"', $lines).'"' . PHP_EOL);
			};
			fflush($file);
			fclose($file);
		} catch (\Throwable $e) {
			var_dump($e);
		}

		return $this->app->rootPath() . $this->app->cfg()->fetch('paths', 'tempFolder') . "/log" . $job . ".csv";
	}
}