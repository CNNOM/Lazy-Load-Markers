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

try {
    $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
    if (!$hlblock) {
        throw new Exception("Highload-блок с ID $hlblockId не найден");
    }
    
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entityClass = $entity->getDataClass();
    
    $totalAdded = 0;
    
    for ($i = 0; $i < $numberOfPoints; $i++) {
        $latitude = mt_rand(-900000, 900000) / 10000; 
        $longitude = mt_rand(-1800000, 1800000) / 10000;
        
        $name = "Точка $i"; 
        $reverseGeocodeUrl = "https://geocode-maps.yandex.ru/1.x/?format=json&apikey=$yandexApiKey&geocode=$longitude,$latitude";
        $response = @file_get_contents($reverseGeocodeUrl);
        
        if ($response) {
            $data = json_decode($response, true);
            if (!empty($data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'])) {
                $name = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'];
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
    
    echo "Готово! Добавлено $totalAdded записей";
    
} catch (Exception $e) {
    echo "Ошибка: " . $e->getMessage();
}

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php';