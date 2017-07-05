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
    \Pwd\Offerchanger\Hl as HL,
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

        self::setupEventHandlers();

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
            array('\Pwd\Offerchanger\Hl', 'HeadersOnAfterUpdateHandler')
        );
        $eventManager->addEventHandler(
            "",
            "OfferChangerOnAfterAdd",
            array('\Pwd\Offerchanger\Hl', 'HeadersOnAfterUpdateHandler')
        );

        if( ! \CSite::InDir('/bitrix/') ){

            $eventManager->addEventHandler(
                "main",
                "OnEndBufferContent",
                array('\Pwd\Offerchanger\Module', 'onEndBufferContentHandler')
            );

        }



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



                if(empty($r_item['ID'])){
                    continue;
                }

                $dom_element = $doc->getElementById($r_item['ID']);

                if( empty($dom_element) || !is_object( $dom_element )){
                    continue;
                }


                switch ($r_item['TYPE']){
                    case 'BG':
                        //background-image:

                        if (!empty($r_item['BANNER']) && strlen($r_item['BANNER'])) {

                            $new_styles = 'background-image: url("'.$r_item['BANNER'].'");';

                            $old_styles = $dom_element->getAttribute('style');
                            $dom_element->setAttribute( 'style',$old_styles." ".$new_styles );

                        }

                        break;
                    case 'IMG':
                        //Изображения
                        if (!empty($r_item['BANNER']) && strlen($r_item['BANNER'])) {

                            $new_img = $r_item['BANNER'];

                            $old_img = $dom_element->getAttribute('src');
                            $dom_element->setAttribute( 'src', $new_img);
                            $dom_element->setAttribute( 'data-old-src', $old_img);

                        }
                        break;
                    case 'STRING':
                    default:
                        //строка

                        if (!empty($r_item['TEXT'])) {
                            $dom_element->textContent = $r_item['TEXT'];
                        }

                    break;
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


                if($arHeader['UF_TYPE']>0){
                    $rsGender = \CUserFieldEnum::GetList(array(), array("ID" => $arHeader['UF_TYPE']));

                    if($arGender = $rsGender->GetNext()){
                        $res['TYPE'] = $arGender['XML_ID'];
                    }

                }else{
                    $res['TYPE'] = 'STRING';
                }


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