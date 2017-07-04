<?
/**
 *  module
 * 
 * @category	
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Pwd\Offerchanger;

use Bitrix\Main\Context,
    \Pwd\Offerchanger\Utils,
    \Pwd\Offerchanger\HL,
    \Bitrix\Main\Config\Option;


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
	public static function onPrologHandler()
	{


        //echo "<b>onPageStart();</b>";

        //global $APPLICATION;
        //$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/bitrix/themes/.default/webdebug.ruble.css" />', true);


	}

	public static function onAfterEpilogHandler(){

        //echo "<b>onPageEnd();</b>";

        self::setOffers();

    }



    protected static function onEndBufferContentHandler(&$content){

        $content = str_replace('<h1 id="pagetitle">Мебельная компания</h1>', '<h1 id="pagetitle">Мебельная компания - Текущее время</h1>', $content);

    }



    protected static function setOffers(){

        $only_index = (Option::get(self::$MODULE_ID , 'only_index') == 'Y') ? true : false ;

        if( $only_index && !\CSite::InDir('/index.php') ){
            return false;
        }


        $r = self::getOffers();
        if(!empty($r)){
            Utils::vardump($r);
        }


    }

    protected static function getOffers(){

        $referrer_name = Option::get(self::$MODULE_ID , 'referrer_name');
        $referrer_name = $referrer_name !== '' ? $referrer_name : 'referrer' ;

        $res = [];
        $arHeaders = HL::getInstance( self::$hl_code )->getHeaders($referrer_name);

        if(!empty($arHeaders)){
            $res['OFFER'] = $arHeaders['UF_OFFER'];
            $res['BANNER_TMP'] = \CFile::GetFileArray($arHeaders['UF_BANNER']);

            if(!empty($res['BANNER_TMP'])){
                $res['BANNER'] = $res['BANNER_TMP']['SRC'];
            }
            unset($res['BANNER_TMP']);

            $res['OFFER_TEXT'] = $arHeaders['UF_OFFER_TEXT'];
        }

        return $res;
    }


    
}