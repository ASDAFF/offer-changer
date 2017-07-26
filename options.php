<?php
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;

$module_id = 'pwd.offerchanger'; //обязательно, иначе права доступа не работают!

Loc::loadMessages($_SERVER["DOCUMENT_ROOT"] . BX_ROOT . "/modules/main/options.php");
Loc::loadMessages(__FILE__);

if ($APPLICATION->GetGroupRight($module_id) < "S") {
    $APPLICATION->AuthForm(Loc::getMessage("ACCESS_DENIED"));
}

\Bitrix\Main\Loader::includeModule($module_id);


$request = \Bitrix\Main\HttpApplication::getInstance()->getContext()->getRequest();

#Описание опций
$aTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => Loc::getMessage('ACADEMY_D7_TAB_SETTINGS'),
        'OPTIONS' => array(

            array(
                "active",
                GetMessage("PWD_OFFER_CHANGER_FIELD_ACTIVE"),
                "Y",
                Array("checkbox")
            ),
            array(
                'referrer_name',
                Loc::getMessage('PWD_OFFER_CHANGER_FIELD_REFERRER_NAME_TITLE'),
                'referrer',
                array('text', 10)
            ),
            array(
                "only_index",
                GetMessage("PWD_OFFER_CHANGER_FIELD_ONLY_INDEX_TITLE"),
                "Y",
                Array("checkbox")
            ),
        )
    ),
);

#Сохранение
if ($request->isPost() && $request['Update'] && check_bitrix_sessid()) {

    foreach ($aTabs as $aTab) {
        //Или можно использовать __AdmSettingsSaveOptions($MODULE_ID, $arOptions);
        foreach ($aTab['OPTIONS'] as $arOption) {
            if (!is_array($arOption)) //Строка с подсветкой. Используется для разделения настроек в одной вкладке
                continue;

            if ($arOption['note']) //Уведомление с подсветкой
                continue;


            //Или __AdmSettingsSaveOption($MODULE_ID, $arOption);
            $optionName = $arOption[0];

            $optionValue = $request->getPost($optionName);


            if ($arOption[3][0] == "checkbox" && $optionValue != "Y")
                $optionValue = "N";

            Option::set($module_id, $optionName, is_array($optionValue) ? implode(",", $optionValue) : $optionValue);
        }
    }
}

#Визуальный вывод

$tabControl = new CAdminTabControl('tabControl', $aTabs);

?>
<? $tabControl->Begin(); ?>
<form method='post'
      action='<? echo $APPLICATION->GetCurPage() ?>?mid=<?= htmlspecialcharsbx($request['mid']) ?>&amp;lang=<?= $request['lang'] ?>'
      name='pwd_offerchanger_settings'>

    <? foreach ($aTabs as $aTab):
        if ($aTab['OPTIONS']):?>
            <? $tabControl->BeginNextTab(); ?>
            <? __AdmSettingsDrawList($module_id, $aTab['OPTIONS']); ?>

        <? endif;
    endforeach; ?>

    <?
    $tabControl->BeginNextTab();


    $tabControl->Buttons(); ?>

    <input type="submit" name="Update" value="<? echo GetMessage('MAIN_SAVE') ?>">
    <input type="reset" name="reset" value="<? echo GetMessage('MAIN_RESET') ?>">
    <?= bitrix_sessid_post(); ?>
</form>
<? $tabControl->End(); ?>

