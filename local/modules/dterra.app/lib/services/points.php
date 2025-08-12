<?php
namespace Dterra\App\Services;

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\SystemException;

class Points
{
    private const MAP_POINTS_HL_ID = 1; 
    private $entityDataClass;

    public function __construct()
    {
        if (!Loader::includeModule('highloadblock')) {
            throw new SystemException('Module highloadblock not installed');
        }

        $this->initEntity();
    }

    private function initEntity()
    {
        $hlBlock = HL\HighloadBlockTable::getById(self::MAP_POINTS_HL_ID)->fetch();
        if (!$hlBlock) {
            throw new SystemException('HL block not found');
        }

        $entity = HL\HighloadBlockTable::compileEntity($hlBlock);
        $this->entityDataClass = $entity->getDataClass();
    }

    public function getPointsInBounds(float $minLat, float $maxLat, float $minLng, float $maxLng, int $limit = 200): array
    {
        $filter = [
            '>=UF_LATITUDE' => $minLat,
            '<=UF_LATITUDE' => $maxLat,
            '>=UF_LONGITUDE' => $minLng,
            '<=UF_LONGITUDE' => $maxLng
        ];

        $points = $this->entityDataClass::getList([
            'select' => ['ID', 'UF_NAME', 'UF_LATITUDE', 'UF_LONGITUDE'],
            'filter' => $filter,
            'limit' => $limit
        ]);

        $result = [];
        while ($point = $points->fetch()) {
            $result[] = [
                'ID' => (int)$point['ID'],
                'NAME' => $point['UF_NAME'],
                'LATITUDE' => (float)$point['UF_LATITUDE'],
                'LONGITUDE' => (float)$point['UF_LONGITUDE']
            ];
        }

        return $result;
    }
}