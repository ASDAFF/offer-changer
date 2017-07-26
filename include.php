<?
namespace Pwd\Offerchanger;

/**
 * Базовый каталог модуля
 */
const BASE_DIR = __DIR__;


$event = new \Bitrix\Main\Event('pwd.offerchanger', 'onModuleInclude');
$event->send();