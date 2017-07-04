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
    \Pwd\Offerchanger\HL;

/**
 * Основной класс модуля
 */
class Module
{


    protected static $hl_code = 'OfferChanger';

	/**
	 * Обработчик начала отображения страницы
	 *
	 * @return void
	 */
	public static function onPageStart()
	{
        //echo "<b>onPageStart();</b>";
	}

	public static function onPageEnd(){

        //echo "<b>onPageEnd();</b>";


        self::setOffers();
    }





    protected static function setOffers(){

        if( \CSite::InDir('/index.php') ){

            $r = self::getOffers();
            if(!empty($r)){

                Utils::vardump($r);

            }
        }

    }

    protected static function getOffers(){

        $res = [];

        $arHeaders = HL::getInstance( self::$hl_code )->getHeaders('referrer_2');

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