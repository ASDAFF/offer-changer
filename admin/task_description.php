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
    "POKOEV_ORM_DENIED" => array(
        "title" => Loc::getMessage('TASK_NAME_POKOEV_ORM_DENIED'),
        "description" => Loc::getMessage('TASK_DESC_POKOEV_ORM_DENIED'),
    ),
    "POKOEV_ORM_READ" => array(
        "title" => Loc::getMessage('TASK_NAME_POKOEV_ORM_READ'),
        "description" => Loc::getMessage('TASK_DESC_POKOEV_ORM_READ'),
    ),
    "POKOEV_ORM_FULL_ACCESS" => array(
        "title" => Loc::getMessage('TASK_NAME_POKOEV_ORM_FULL_ACCESS'),
        "description" => Loc::getMessage('TASK_DESC_POKOEV_ORM_FULL_ACCESS'),
    ),
    "MODULE" => array(
        "title" => Loc::getMessage("TASK_BINDING_MODULE"),
    ),
);
