<?
/**
 * Created by PhpStorm
 * User: Sergey Pokoev
 * www.pokoev.ru
 * @ Академия 1С-Битрикс - 2015
 * @ academy.1c-bitrix.ru
 */
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

return array(
    "POKOEV_ORM_READ" => array(
        "title" => Loc::getMessage('OP_NAME_POKOEV_ORM_READ'),
        "description" => Loc::getMessage('OP_DESC_POKOEV_ORM_READ'),
    ),
    "POKOEV_ORM_SETTINGS" => array(
        "title" => Loc::getMessage('OP_NAME_POKOEV_ORM_SETTINGS'),
        "description" => Loc::getMessage('OP_DESC_POKOEV_ORM_SETTINGS'),
    ),
    "POKOEV_ORM_EDIT" => array(
        "title" => Loc::getMessage('OP_NAME_POKOEV_ORM_EDIT'),
        "description" => Loc::getMessage('OP_DESC_POKOEV_ORM_EDIT'),
    ),
);