<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die(json_encode(['error' => 'Invalid request']));
}

$minLat = (float) $_POST['minLat'];
$maxLat = (float) $_POST['maxLat'];
$minLng = (float) $_POST['minLng'];
$maxLng = (float) $_POST['maxLng'];

$pointsService = new \Dterra\App\Services\Points();
$result = $pointsService->getPointsInBounds($minLat, $maxLat, $minLng, $maxLng);

header('Content-Type: application/json');
echo json_encode($result);
