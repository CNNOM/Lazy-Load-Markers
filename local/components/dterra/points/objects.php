<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die(json_encode(['error' => 'Invalid request']));
}

$boundBox = explode(',', $_GET['bbox']);
$callback = $_GET['callback'] ?? 'callback';

$southWestLatitude = (float)$boundBox[0];
$southWestLongitude = (float)$boundBox[1];
$northEastLatitude = (float)$boundBox[2];
$northEastLongitude = (float)$boundBox[3];

$pointsService = new \Dterra\App\Services\Points();
$points = $pointsService->getPointsInBounds(
    $southWestLatitude,
    $northEastLatitude,
    $southWestLongitude,
    $northEastLongitude
);

$collection = [
    'type' => 'FeatureCollection',
    'features' => []
];

foreach ($points as $point) {
    $collection['features'][] = [
        'type' => 'Feature',
        'id' => $point['ID'],
        'geometry' => [
            'type' => 'Point',
            'coordinates' => [(float)$point['LATITUDE'], (float)$point['LONGITUDE']]
        ],
        'properties' => [
            'balloonContent' => $point['NAME'],
        ],

    ];
}

header('Content-Type: application/javascript');
echo sprintf('%s(%s)', $callback, json_encode($collection));