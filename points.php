<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Entity;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
Loader::includeModule('highloadblock');

// ID вашего Highload-блока (замените на реальный)
$hlblockId = 1; 
$entity = HL\HighloadBlockTable::getById($hlblockId)->fetch();
$entityClass = HL\HighloadBlockTable::compileEntity($entity)->getDataClass();

// API-ключ Яндекс.Карт (получите здесь: https://developer.tech.yandex.ru/)
$yandexApiKey = 'ваш_api_ключ'; 

// Список городов России для выборки точек
$cities = [
    'Москва', 'Санкт-Петербург', 'Казань', 
    'Екатеринбург', 'Сочи', 'Нижний Новгород',
    'Краснодар', 'Владивосток', 'Калининград'
];

foreach ($cities as $city) {
    // Запрос к API Яндекс.Карт (поиск достопримечательностей)
    $query = urlencode("достопримечательности $city");
    $url = "https://search-maps.yandex.ru/v1/?text=$query&type=biz&lang=ru_RU&results=10&apikey=$yandexApiKey";
    
    $response = file_get_contents($url);
    $data = json_decode($response, true);

    if (!empty($data['features'])) {
        foreach ($data['features'] as $item) {
            $name = $item['properties']['name'];
            $longitude = $item['geometry']['coordinates'][0]; // Долгота
            $latitude = $item['geometry']['coordinates'][1];   // Широта

            // Добавляем запись в HLBLOCK
            $result = $entityClass::add([
                'UF_NAME' => $name,
                'UF_LATITUDE' => $latitude,
                'UF_LONGITUDE' => $longitude,
            ]);

            if ($result->isSuccess()) {
                echo "Добавлено: $name ($latitude, $longitude) <br>";
            } else {
                echo "Ошибка: " . implode(', ', $result->getErrorMessages()) . "<br>";
            }
        }
    }
}

echo "Готово! Точки добавлены в Highload-блок.";