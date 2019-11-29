<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/html">
<head>
    <?php include "parts/head.php" ?>
    <?php echo $app->getAssets(['ui', 'forms'], $page); ?>
    <title>BlueSeal - <?php echo $page->getTitle(); ?></title>
   <?php echo $htmlBody?>