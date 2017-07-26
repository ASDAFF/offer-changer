<?
/**
 * Created by PhpStorm
 * User: Uriy Smirnov & Denis Denisov
 * p-w-d.ru
 * @ Pride Web Development
 */

namespace Pwd\Offerchanger;

use \Bitrix\Main\EventManager;
use  \Bitrix\Main\Config\Option;

/**
 * Основной класс модуля
 */
class Module
{


    protected static $strModuleId = 'pwd.offerchanger';

    /**
     * Обработчик начала отображения страницы
     *
     * @return void
     */
    public static function onPageStart()
    {

        $isActive = (Option::get(self::$strModuleId, 'active') == 'Y') ? true : false;


        if($isActive){
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
        $objEventManager = EventManager::getInstance();

        $objEventManager->addEventHandler(
            "",
            "OfferChangerOnAfterUpdate",
            array('\Pwd\Offerchanger\Hl', 'HeadersOnAfterUpdateHandler')
        );
        $objEventManager->addEventHandler(
            "",
            "OfferChangerOnAfterAdd",
            array('\Pwd\Offerchanger\Hl', 'HeadersOnAfterUpdateHandler')
        );

        if( ! \CSite::InDir('/bitrix/') ){

            $objEventManager->addEventHandler(
                "main",
                "OnEndBufferContent",
                array('\Pwd\Offerchanger\Offer', 'onEndBufferContentHandler')
            );

        }

    }
}