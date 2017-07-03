<? use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

Loc::loadLanguageFile();
Loader::includeModule('highloadblock');

$arHblocks = HighloadBlockTable::getList(
	array(
		"select" => array(
			"ID", "NAME"
		)
	)
);


$arComponentParameters = array(
	"PARAMETERS" => array(
		"CACHE_TIME"  =>  Array("DEFAULT" => 3600),
		"HLBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("MESS_HLBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arHblocks,
			"DEFAULT" => "1",
			"REFRESH" => "Y",
		),
	)
);