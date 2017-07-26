<?
/**
 * Created by PhpStorm
 * User: Uriy Smirnov & Denis Denisov
 * p-w-d.ru
 * @ Pride Web Development
 */

namespace Pwd\Offerchanger;

use\Pwd\Offerchanger\Hl as HL,
    \Bitrix\Main\Config\Option;

/**
 * Основной класс модуля
 */
class Offer
{


    protected static $strHlCode = 'OfferChanger';
    protected static $strModuleId = 'pwd.offerchanger';

    /**
     * Обработчик вызывается при выводе буферизированного контента.
     *
     * @param $content
     */
    public static function onEndBufferContentHandler(&$content)
    {

        self::setOffers($content);

    }

    /**
     * Устаналвиет в контент блоки замен
     *
     * @param $content
     * @return bool
     */
    public static function setOffers(&$content)
    {

        // Проверяет настроку модуля(Только на индексной странице?)
        $boolOnlyIndex = (Option::get(self::$strModuleId, 'only_index') == 'Y') ? true : false;
        if ($boolOnlyIndex && !\CSite::InDir('/index.php')) {
            return false;
        }


        if (!class_exists('DOMDocument')) {
            return false;
        }

        $objDocument = new \DOMDocument();

        if (!is_object($objDocument)) {
            return false;
        }


        $objDocument->loadHTML($content);

        $arListOffers = self::getOffers();
        if (!empty($arListOffers) && count($arListOffers)) {

            foreach ($arListOffers as $arOfferItem) {

                if (empty($arOfferItem['ID'])) {
                    continue;
                }

                $arOfferItem['ID'] = trim($arOfferItem['ID']);

                $objDomElement = $objDocument->getElementById($arOfferItem['ID']);

                if (empty($objDomElement) || !is_object($objDomElement)) {
                    continue;
                }


                switch ($arOfferItem['TYPE']) {
                    case 'BG':
                        //background-image:

                        if (!empty($arOfferItem['BANNER']) && strlen($arOfferItem['BANNER'])) {

                            $strNewStyles = 'background-image: url("' . $arOfferItem['BANNER'] . '");';
                            $strOldStyles = $objDomElement->getAttribute('style');

                            $objDomElement->setAttribute('style', $strOldStyles . " " . $strNewStyles);

                        }

                        break;
                    case 'IMG':
                        //Изображения
                        if (!empty($arOfferItem['BANNER']) && strlen($arOfferItem['BANNER'])) {

                            $strNewImg = $arOfferItem['BANNER'];
                            $strOldImg = $objDomElement->getAttribute('src');

                            $objDomElement->setAttribute('src', $strNewImg);
                            $objDomElement->setAttribute('data-old-src', $strOldImg);

                        }
                        break;
                    case 'STRING':
                    default:
                        //строка
                        if (!empty($arOfferItem['TEXT'])) {
                            $objDomElement->textContent = $arOfferItem['TEXT'];
                        }
                        break;
                }
            }
        }

        $content = $objDocument->saveHTML();

        return true;
    }

    /**
     * Получает блоки замен из HL-блоков
     *
     * @return array
     */
    protected static function getOffers()
    {

        $strReferrerName = Option::get(self::$strModuleId, 'referrer_name');
        $strReferrerName = $strReferrerName !== '' ? $strReferrerName : 'referrer';


        $arHeaders = HL::getInstance(self::$strHlCode)->getHeaders($strReferrerName);

        $arOffersList = [];
        foreach ($arHeaders as $arHeader) {

            if (!empty($arHeader)) {
                $arResOffer = array();


                if ($arHeader['UF_TYPE'] > 0) {
                    $rsGender = \CUserFieldEnum::GetList(array(), array("ID" => $arHeader['UF_TYPE']));

                    if ($arGender = $rsGender->GetNext()) {
                        $arResOffer['TYPE'] = $arGender['XML_ID'];
                    }

                } else {
                    $arResOffer['TYPE'] = 'STRING';
                }


                $arResOffer['ID'] = $arHeader['UF_BLOCK_ID'];
                $arResOffer['TEXT'] = $arHeader['UF_BLOCK_TEXT'];

                $arResOffer['BANNER_TMP'] = \CFile::GetFileArray($arHeader['UF_BANNER']);
                if (!empty($arResOffer['BANNER_TMP'])) {
                    $arResOffer['BANNER'] = $arResOffer['BANNER_TMP']['SRC'];
                }
                unset($arResOffer['BANNER_TMP']);

                $arOffersList[] = $arResOffer;
            }
        }
        return $arOffersList;
    }
}