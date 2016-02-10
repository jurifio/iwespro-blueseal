<?php


namespace bamboo\htdocs\pickyshop\blueseal\business;
use bamboo\core\application\AApplication;
use bamboo\core\db\pandaorm\entities\AEntity;
use bamboo\core\exceptions\RedPandaOutOfBoundException;


/**
 * Class CImporterConnector
 * @package redpanda\htdocs\pickyshop\blueseal\business
 * @author Bambooshoot Team <emanuele@bambooshoot.agency>, 25/01/2016
 * @copyright (c) Bambooshoot snc - All rights reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 *
 * @since ${VERSION}
 */
class CImporterConnectorResolver
{
	protected $app;
	protected $dirtyProduct;
	protected $importerConnector;
	protected $assignment;

	public function __construct(AApplication $app)
	{
		$this->app = $app;
	}

	public function find(){

	}

	/**
	 * @param $dirtyProduct
	 * @param $importerConnector
	 * @return bool
	 * @throws RedPandaOutOfBoundException
	 */
	public function assign($dirtyProduct, $importerConnector)
	{
		$this->dirtyProduct = $dirtyProduct;
		$this->importerConnector = $importerConnector;
		if($this->operate($importerConnector->importerConnectorOperation)){
			$res = $this->assignment;
			$this->assignment = false;
			return $res;
		} else {
			return false;
		}
	}

	/**
	 * @param $operation
	 * @return bool
	 * @throws RedPandaOutOfBoundException
	 */
	private function operate($operation)
	{
		$resoult = false;
		switch($operation->importerOperator->operator) {
			case "==":
				$resoult = $this->retriveField($operation) == $operation->value;
				break;
			case "!=":
				$resoult = $this->retriveField($operation) != $operation->value;
				break;
			case ">":
				$resoult = $this->retriveField($operation) > $operation->value;
				break;
			case "<":
				$resoult = $this->retriveField($operation) < $operation->value;
				break;
			case ">=":
				$resoult = $this->retriveField($operation) >= $operation->value;
				break;
			case "<=":
				$resoult = $this->retriveField($operation) <= $operation->value;
				break;
			/*case "xor":
				$resoult = $this->retriveField($operation) xor $operation->value;
				break;*/
			case "in":
				$resoult = $this->retriveField($operation) == $operation->value;
				break;
			case "not in":
				$resoult = $this->retriveField($operation) == $operation->value;
				break;
			case "=":
				$this->assignment = $operation->value;
				break;
			default:
				throw new RedPandaOutOfBoundException("Operation not yet implemented: %s", [$operation->importerOperator->operator]);
		}
		return $this->concatenate($resoult, $operation);
	}

	/**
	 * @param $resoult
	 * @param $operation
	 * @return bool
	 * @throws RedPandaOutOfBoundException
	 */
	private function concatenate($resoult, $operation)
	{
		if(is_null($operation->nextOperation)) return $resoult;
		else
			switch($operation->logicConnector){
				case "AND":
				case "and":
					return $resoult && $this->operate($operation->nextOperation);
					break;
				case "or":
				case "OR":
					return $resoult || $this->operate($operation->nextOperation);
					break;
				default:
					throw new RedPandaOutOfBoundException("Logic Connector not yet implemented: %s", [$operation->logicConnector]);
			}
	}

	/**
	 * @param AEntity $operation
	 * @return mixed
	 * @throws RedPandaOutOfBoundException
	 */
	private function retriveField(AEntity $operation)
	{
		switch($operation->importerField->fieldLocation){
			case "DirtyProduct":
				return $this->calculateFieldModifier($this->dirtyProduct->{$operation->importerField->field}, $operation->importerFieldModifier);
				break;
			case "DirtyProductExtend":
				return $this->calculateFieldModifier($this->dirtyProduct->extend->{$operation->importerField->field}, $operation->importerFieldModifier);
				break;
			case "DirtySku":
				return $this->calculateFieldModifier($this->dirtyProduct->dirtySku{$operation->importerField->field}, $operation->importerFieldModifier);
				break;
			case "DirtyPhoto":
				return $this->calculateFieldModifier($this->dirtyProduct->dirtyPhoto{$operation->importerField->field}, $operation->importerFieldModifier);
				break;
			case "DirtyDetail":
				return $this->calculateFieldModifier($this->dirtyProduct->dirtyDetail{$operation->importerField->field}, $operation->importerFieldModifier);
				break;
			default:
				throw new RedPandaOutOfBoundException("Field location not yet implemented: %s",[$operation->importerField->fieldLocation]);
		}
	}

	/**
	 * @param $data
	 * @param $modifier
	 * @return mixed
	 */
	private function calculateFieldModifier($data, $modifier){
		switch($modifier->modifier){
			case null:
				return $data;
				break;
			case 'min':
				if($data instanceof \Iterator) {
					$min = null;
					foreach($data as $dat){
						if(is_null($min) || $dat < $min) $min = $dat;
					}
					return $min;
				} else return $data;
				break;
			case 'max':
				if($data instanceof \Iterator) {
					$max = null;
					foreach($data as $dat){
						if(is_null($max) || $dat > $max) $max = $dat;
					}
					return $max;
				} else return $data;
				break;
			case 'avg':
				if($data instanceof \Iterator) {
					$sum = 0;
					$i = 0;
					foreach($data as $dat){
						$i++;
						$sum+=$dat;
					}
					if($i>0) return $sum/$i;
					else return null;
				} else return $data;
				break;
			default:
				if($data instanceof \Iterator) {
					foreach($data as $dat){
						return $dat;
					}
				}
				return $data;
		}
	}
}