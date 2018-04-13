<?php

//load.php

/** @var \bamboo\domain\entities\CEditorialPlan $editorialPlan */
$editorialPlan = \Monkey::app()->repoFactory->create('EditorialPlan')->findOneBy(['id' => $idEditorialPlan]);

/** @var \bamboo\core\base\CObjectCollection $editorialPlanDetail */
$editorialPlanDetail = $editorialPlan->editorialPlanDetail;


$data = [];
$i = 0;
/** @var \bamboo\domain\entities\CEditorialPlanDetail $singleDetail */
foreach ($editorialPlanDetail as $singleDetail) {
    $data[$i]["title"] = $singleDetail->title;
    $data[$i]["start"] = $singleDetail->startEventDate;
    $data[$i]["end"] = $singleDetail->endEventDate;
    $i++;
}

echo json_encode($data);
