<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

if (!$USER->IsAdmin()) {
    die('Доступ запрещен');
}

Loader::includeModule('highloadblock');

// Конфигурация
$hlblockId = 1; // ID Highload-блока
$numberOfPoints = 25; // Количество случайных точек
$yandexApiKey = 'ваш_api_ключ'; // Ключ Яндекс.Карт

// Границы Москвы (примерные координаты)
$moscowBounds = [
    'min_lat' => 55.573,
    'max_lat' => 55.911,
    'min_lon' => 37.370,
    'max_lon' => 37.857
];

try {
    // Получаем сущность HL-блока
    $hlblock = HL\HighloadBlockTable::getById($hlblockId)->fetch();
    if (!$hlblock) {
        throw new Exception("Highload-блок с ID $hlblockId не найден");
    }
    
    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entityClass = $entity->getDataClass();
    
    $totalAdded = 0;
    
    // Генерация случайных точек в пределах Москвы
    for ($i = 0; $i < $numberOfPoints; $i++) {
        // Генерация случайных координат в пределах Москвы
        $latitude = mt_rand($moscowBounds['min_lat'] * 10000, $moscowBounds['max_lat'] * 10000) / 10000;
        $longitude = mt_rand($moscowBounds['min_lon'] * 10000, $moscowBounds['max_lon'] * 10000) / 10000;
        
        // Получение названия через обратное геокодирование
        $name = "Точка Москвы $i"; // Значение по умолчанию
        $reverseGeocodeUrl = "https://geocode-maps.yandex.ru/1.x/?format=json&apikey=$yandexApiKey&geocode=$longitude,$latitude";
        $response = @file_get_contents($reverseGeocodeUrl);
        
        if ($response) {
            $data = json_decode($response, true);
            if (!empty($data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'])) {
                $name = $data['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['name'];
            }
            
            // Проверяем, что точка действительно в Москве
            if (strpos($response, 'Москва') === false && strpos($response, 'Moscow') === false) {
                echo "Точка ($latitude, $longitude) вне Москвы, пропускаем<br>";
                continue;
            }
        }
        
        // Добавление записи в HL-блок
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