<?
/**
 * Created by PhpStorm
 * User: Sergey Pokoev
 * www.pokoev.ru
 * @ Академия 1С-Битрикс - 2015
 * @ academy.1c-bitrix.ru
 *
 * файл include.php
 */

namespace Pwd\Offerchanger;

/**
 * Базовый каталог модуля
 */
const BASE_DIR = __DIR__;


$event = new \Bitrix\Main\Event('pwd.offerchanger', 'onModuleInclude');
$event->send();