<?php

use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Type\Date;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

class CurrencyRatesComponent extends CBitrixComponent
{
    private const MAX_ITEMS_LIMIT = 100;
    private const CBR_API_URL = 'http://www.cbr.ru/scripts/XML_daily.asp?date_req=';
    private const UPDATE_HOUR = 5;

    private $entityClass;

    public function executeComponent()
    {
        if (!$this->validateParameters()) {
            return;
        }

        if (!$this->initializeHighloadBlock()) {
            return;
        }

        $this->prepareResult();
    }
    private function validateParameters(): bool
    {
        if (empty($this->arParams['HL_BLOCK_ID'])) {
            ShowError('Не указан ID highload-блока');
            return false;
        }
        return true;
    }

    private function initializeHighloadBlock(): bool
    {
        if (!Loader::includeModule('highloadblock')) {
            ShowError('Модуль highload-блоков не установлен');
            return false;
        }

        $hlBlockId = (int) $this->arParams['HL_BLOCK_ID'];
        $hlBlock = HighloadBlockTable::getById($hlBlockId)->fetch();

        if (!$hlBlock) {
            ShowError('Highload-блок не найден');
            return false;
        }

        $this->entityClass = HighloadBlockTable::compileEntity($hlBlock)->getDataClass();
        return true;
    }

    private function prepareResult(): void
    {
        $filterDate = $this->getFilterDate();

        $todayRates = $this->processCurrencyData($filterDate);

        $this->arResult = [
            'HL_BLOCK_ID' => $this->arParams['HL_BLOCK_ID'],
            'CURRENT_DATE' => $filterDate->format('d.m.Y'),
            'TODAY_RATES' => $todayRates,
            'ITEMS' => $this->getAllRates(),
        ];

        $this->includeComponentTemplate();
    }

    private function getFilterDate(): Date
    {
        return !empty($this->arParams['FILTER']['DATE'])
            ? new Date($this->arParams['FILTER']['DATE'], 'Y-m-d')
            : new Date(date('Y-m-d'), 'Y-m-d');
    }

    private function processCurrencyData(Date $filterDate): array
    {
        $todayRates = $this->getTodayRates($filterDate);

        if (empty($todayRates) || $this->shouldUpdateRates($filterDate, $todayRates)) {
            $apiRates = $this->fetchAndSaveApiRates($filterDate, $todayRates);
            $todayRates = !empty($apiRates) ? $apiRates : $todayRates;
        }

        return $todayRates;
    }

    private function shouldUpdateRates(Date $filterDate, array $todayRates): bool
    {
        $currentHour = (int) (new DateTime())->format('G');
        $isToday = $filterDate->format('Y-m-d') === date('Y-m-d');
        $isUpdateTime = $currentHour >= self::UPDATE_HOUR;
        $missingCurrencies = !$this->checkCurrency($todayRates, 'USD') || !$this->checkCurrency($todayRates, 'EUR');

        return $isToday && $isUpdateTime && $missingCurrencies;
    }

    private function fetchAndSaveApiRates(Date $date, array $existingRates): array
    {
        $hasUSD = !$this->checkCurrency($existingRates, 'USD');
        $hasEUR = !$this->checkCurrency($existingRates, 'EUR');


        return $this->getCourseApi($date, $hasUSD, $hasEUR);
    }

    private function getTodayRates(Date $date): array
    {
        return $this->entityClass::getList([
            'select' => ['*'],
            'filter' => ['=UF_DATE' => $date],
            'order' => ['ID' => 'ASC']
        ])->fetchAll();
    }

    private function getAllRates(): array
    {
        return $this->entityClass::getList([
            'select' => ['*'],
            'order' => ['UF_DATE' => 'DESC', 'ID' => 'ASC'],
            'limit' => self::MAX_ITEMS_LIMIT
        ])->fetchAll();
    }

    private function checkCurrency(array $rates, string $currency): bool
    {
        foreach ($rates as $rate) {
            if ($rate['UF_CURRENCY'] === $currency) {
                return true;
            }
        }
        return false;
    }

    private function getCourseApi(Date $date, bool $hasUSD, bool $hasEUR): array
    {
        $xmlData = $this->fetchXmlData($date);

        if ($xmlData === false) {
            return [];
        }

        return $this->parseAndSaveXmlRates($xmlData, $date, $hasUSD, $hasEUR);
    }

    private function fetchXmlData(Date $date): SimpleXMLElement|bool
    {
        $dateFormatted = $date->format('d/m/Y');
        return simplexml_load_file(self::CBR_API_URL . $dateFormatted);
    }

    /**
     * Парсинг XML с курсами валют и сохранение в HL
     * @param SimpleXMLElement $xml XML-данные курсов валют от ЦБ РФ
     * @param Date $date Дата актуальности курсов
     * @param bool $hasUSD Флаг провер обработки доллара США (USD)
     * @param bool $hasEUR Флаг необходимости обработки евро (EUR)
     * 
     * @return array Массив курсов валют
     */
    private function parseAndSaveXmlRates(SimpleXMLElement $xml, Date $date, bool $hasUSD, bool $hasEUR): array
    {
        $rates = [];
        foreach ($xml->Valute as $valute) {
            $code = (string) $valute->CharCode;

            if (($hasUSD && $code === 'USD') || ($hasEUR && $code === 'EUR')) {
                $rate = $this->createRateArray($valute, $date);
                $savedRate = $this->saveRate($rate);

                if ($savedRate) {
                    $rates[] = $savedRate;
                }
            }
        }
        return $rates;
    }

    private function createRateArray(SimpleXMLElement $valute, Date $date): array
    {
        return [
            'UF_DATE' => $date,
            'UF_CURRENCY' => (string) $valute->CharCode,
            'UF_BUY' => $this->parseCurrencyValue((string) $valute->Value),
            'UF_SALE' => $this->parseCurrencyValue((string) $valute->VunitRate),
        ];
    }

    private function parseCurrencyValue(string $value): float
    {
        return (float) str_replace(',', '.', $value);
    }

    private function saveRate(array $rate): ?array
    {
        $result = $this->entityClass::add($rate);
        if ($result->isSuccess()) {
            $rate['ID'] = $result->getId();
            return $rate;
        }
        return null;
    }
}