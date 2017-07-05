<?
/**
 * Created by PhpStorm
 * User: Uriy Smirnov & Denis Denisov
 * p-w-d.ru
 * @ Pride Web Development
 */

namespace Pwd\Offerchanger;

use \Bitrix\Main\Context,
    \Pwd\Offerchanger\Utils as Utils,
    \Pwd\Offerchanger\HL as HL,
    \Bitrix\Main\Config\Option,
    \Bitrix\Main\EventManager;

/**
 * Основной класс модуля
 */
class Module
{

    protected static $hl_code = 'OfferChanger';
    protected static $MODULE_ID = 'pwd.offerchanger';

    /**
     * Обработчик начала отображения страницы
     *
     * @return void
     */
    public static function onPageStart()
    {

        if( ! \CSite::InDir('/bitrix/') ){
            self::setupEventHandlers();
        }

    }


    /**
     * Добавляет обработчики событий
     *
     * @return void
     */
    public static function setupEventHandlers()
    {
        $eventManager = EventManager::getInstance();

        $eventManager->addEventHandler(
            "",
            "OfferChangerOnAfterUpdate",
            array('\Pwd\Offerchanger\HL', 'HeadersOnAfterUpdateHandler')
        );
        $eventManager->addEventHandler(
            "",
            "OfferChangerOnAfterAdd",
            array('\Pwd\Offerchanger\HL', 'HeadersOnAfterUpdateHandler')
        );
        $eventManager->addEventHandler(
            "main",
            "OnEndBufferContent",
            array('\Pwd\Offerchanger\Module', 'onEndBufferContentHandler')
        );
    }

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
        $only_index = (Option::get(self::$MODULE_ID, 'only_index') == 'Y') ? true : false;
        if ($only_index && !\CSite::InDir('/index.php')) {
            return false;
        }

        $doc = new \DOMDocument();
        $doc->loadHTML($content);

        $r_list = self::getOffers();
        if (!empty($r_list) && count($r_list)) {
            foreach ($r_list as $r_item) {
                if (!empty($r_item['ID']) && !empty($r_item['TEXT'])) {
                    $doc->getElementById($r_item['ID'])->textContent = $r_item['TEXT'];
                }
            }
        }

        $content = $doc->saveHTML();

        return true;
    }

    /**
     * Получает блоки замен из HL-блоков
     *
     * @return array
     */
    public static function getOffers()
    {

        $referrer_name = Option::get(self::$MODULE_ID, 'referrer_name');
        $referrer_name = $referrer_name !== '' ? $referrer_name : 'referrer';

        $arHeaders = HL::getInstance(self::$hl_code)->getHeaders($referrer_name);

        $res_list = [];
        foreach ($arHeaders as $arHeader) {

            if (!empty($arHeader)) {
                $res = array();

                $res['ID'] = $arHeader['UF_BLOCK_ID'];
                $res['TEXT'] = $arHeader['UF_BLOCK_TEXT'];

                $res['BANNER_TMP'] = \CFile::GetFileArray($arHeader['UF_BANNER']);
                if (!empty($res['BANNER_TMP'])) {
                    $res['BANNER'] = $res['BANNER_TMP']['SRC'];
                }
                unset($res['BANNER_TMP']);

                $res_list[] = $res;
            }
        }
        return $res_list;
    }


}