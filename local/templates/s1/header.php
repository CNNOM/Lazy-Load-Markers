<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
  die(); ?>
<?
IncludeTemplateLangFile(__FILE__);
?>

<head>
    <title><?= $APPLICATION->ShowTitle() ?></title>
    <? $APPLICATION->ShowHead();
    require $_SERVER['DOCUMENT_ROOT'] . SITE_TEMPLATE_PATH . '/include/templates/assets.php';
    CModule::IncludeModule('iblock');
    ?>
</head>

<header class="header">
  <?$APPLICATION->ShowPanel();?>
  <div class="inner"><a class="header__logo" href="/">
      <svg class="svg-icon svg-icon--icon_logo2">
        <use xlink:href="img/sprite.svg#icon_logo2"></use>
      </svg></a>
    <div class="header__content">
      <div class="header__wrap">
        <div class="header__headings"><a class="header__heading header__heading--active"
            href="main-business.html">Бизнесу</a><a class="header__heading" href="main-private.html">Частным лицам</a><a
            class="header__heading" href="about.html">О Банке</a></div><a class="header__bvi bvi-open" href="#">Версия
          для слабовидящих</a>
        <div class="header-search">
          <div class="header-search__icon"></div>
          <div class="ui-input header-search__ui-input">
            <input id="headerSearch" type="text" placeholder="Поиск" />
          </div>
          <div class="header-search__close"></div>
        </div>
        <div class="header__burger"><span></span><span></span><span></span></div>
      </div>
      <div class="header__wrap">
        <ul class="header__menu">
          <li class="header__item"><a class="header__link" href="rko.html">
              <div class="ui-p1 ui-p1--link">РКО
              </div>
            </a>
          </li>
          <li class="header__item"><a class="header__link header__link--arrow" href="credits-business.html">
              <div class="ui-p1 ui-p1--link">Кредиты
              </div>
            </a>
            <ul class="header__list">
              <li class="header__item"><a class="header__link" href="credits-inner-business.html">
                  <div class="ui-p2">Овердрафт
                  </div>
                </a></li>
              <li class="header__item"><a class="header__link" href="credits-inner-business.html">
                  <div class="ui-p2">Кредитная линия
                  </div>
                </a></li>
              <li class="header__item"><a class="header__link" href="credits-inner-business.html">
                  <div class="ui-p2">Инвестиционный
                  </div>
                </a></li>
              <li class="header__item"><a class="header__link" href="credits-inner-business.html">
                  <div class="ui-p2">Универсальный
                  </div>
                </a></li>
              <li class="header__item"><a class="header__link" href="credits-inner-business.html">
                  <div class="ui-p2">Быстрый
                  </div>
                </a></li>
              <li class="header__item"><a class="header__link" href="credits-inner-business.html">
                  <div class="ui-p2">Залоговый
                  </div>
                </a></li>
              <li class="header__item"><a class="header__link" href="credits-inner-business.html">
                  <div class="ui-p2">Тендерный
                  </div>
                </a></li>
              <li class="header__item"><a class="header__link" href="credits-inner-business.html">
                  <div class="ui-p2">Льготный кредит в рамках Программы 1764
                  </div>
                </a></li>
            </ul>
          </li>
          <li class="header__item"><a class="header__link" href="deposits-business.html">
              <div class="ui-p1 ui-p1--link">Депозиты
              </div>
            </a>
          </li>
          <li class="header__item"><a class="header__link header__link--arrow">
              <div class="ui-p1 ui-p1--link">Прочее
              </div>
            </a>
            <ul class="header__list">
              <li class="header__item"><a class="header__link" href="salary-project.html">
                  <div class="ui-p2">Зарплатный проект
                  </div>
                </a></li>
              <li class="header__item"><a class="header__link" href="corporate-cards.html">
                  <div class="ui-p2">Корпоративные карты
                  </div>
                </a></li>
              <li class="header__item"><a class="header__link" href="fea.html">
                  <div class="ui-p2">ВЭД
                  </div>
                </a></li>
              <li class="header__item"><a class="header__link" href="online-banking.html">
                  <div class="ui-p2">Интернет-Банк для юридических лиц
                  </div>
                </a></li>
            </ul>
          </li>
        </ul>
        <div class="ui-button header__ui-button ui-button--hollow">Интернет-банк
          <div class="header__banking"><a href="#">
              <div class="ui-p4 header__ui-p4">Физическим лицам
              </div>
            </a><a href="#">
              <div class="ui-p4 header__ui-p4">Юридическим лицам
              </div>
            </a></div>
        </div>
      </div>
    </div>
  </div>
</header>