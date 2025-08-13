<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true)
    die; ?>
<main>
    <?php
    LocalRedirect('/map/');
    // if ($_GET['ajax']) {
    //     $APPLICATION->RestartBuffer();

    //     if ($_GET['currency_date']) {
    //         $arFilter['DATE'] = $_GET['currency_date'];
    //     }
    // }

    // $APPLICATION->IncludeComponent(
    //     'custom:currency.rates',
    //     '',
    //     [
    //         // 'HL_BLOCK_ID' => ID_HL_CURRENCY_RATES,
    //         'FILTER' => $arFilter,
    //     ]
    // );

    // if ($_GET['ajax']) {
    //     exit();
    // }
    ?>
</main>