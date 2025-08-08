<?php

use Bitrix\Main\Page\Asset;

global $USER;
Asset::getInstance()->addString('<meta charset="UTF-8">');

Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/css/style.min.css");

Asset::getInstance()->addString('<script type="module" src="' . SITE_TEMPLATE_PATH . '/js/script.js"></script>');
Asset::getInstance()->addString('<script type="module" src="' . SITE_TEMPLATE_PATH . '/js/vendors.js"></script>');
Asset::getInstance()->addString('<script type="module" src="' . SITE_TEMPLATE_PATH . '/js/backend.js"></script>');
Asset::getInstance()->addString('<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>');

