<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (!$USER->IsAdmin()) {
    die('Доступ запрещен');
}

Loader::includeModule('highloadblock');

$hlblockId = 1; 
$numberOfPoints = 25; 

$moscowBounds = [
    'min_lat' => 55.573,
    'max_lat' => 55.911,
    'min_lon' => 37.370,
    'max_lon' => 37.857
];

try {
    $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
    if (!$hlblock) {
        throw new Exception("Highload-блок с ID $hlblockId не найден");
    }
    
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entityClass = $entity->getDataClass();
    
    $totalAdded = 0;
    
    for ($i = 0; $i < $numberOfPoints; $i++) {
        $latitude = mt_rand($moscowBounds['min_lat'] * 10000, $moscowBounds['max_lat'] * 10000) / 10000;
        $longitude = mt_rand($moscowBounds['min_lon'] * 10000, $moscowBounds['max_lon'] * 10000) / 10000;
        
        $name = "Точка Москвы $i";
        $reverseGeocodeUrl = "https://geocode-maps.yandex.ru/1.x/?format=json&apikey=$yandexApiKey&geocode=$longitude,$latitude";
        $response = @file_get_contents($reverseGeocodeUrl);
        
        if ($response) {
            $data = json_decode($response, true);
            if (!empty($data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'])) {
                $name = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'];
            }
            
            if (strpos($response, 'Москва') === false && strpos($response, 'Moscow') === false) {
                echo "Точка ($latitude, $longitude) вне Москвы, пропускаем<br>";
                continue;
            }
        }
        
        $result = $entityClass::add([
            'UF_NAME' => $name,
            'UF_LATITUDE' => $latitude,
            'UF_LONGITUDE' => $longitude,
        ]);
        
        if ($result->isSuccess()) {
            $totalAdded++;
            echo "Добавлено: $name ($latitude, $longitude)<br>";
        } else {
            echo "Ошибка: " . implode(', ', $result->getErrorMessages()) . "<br>";
        }
    }
    
    echo "Готово! Добавлено $totalAdded записей в пределах Москвы";
    
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';