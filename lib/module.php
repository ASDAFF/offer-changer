<?
/**
 *  module
 * 
 * @category	
 * @link		http://.ru
 * @revision	$Revision$
 * @date		$Date$
 */

namespace Pwd\Offer\Changer;

use Bitrix\Main\Context;


/**
 * Основной класс модуля
 */
class Module
{
	/**
	 * Обработчик начала отображения страницы
	 *
	 * @return void
	 */
	public static function onPageStart()
	{
		self::defineConstants();

        echo "<b>onPageStart();</b>";
        //die();
	}

	public static function onAfterEpilog(){

        echo "<b>onAfterEpilog();</b>";
        //die();

    }
	
	/**
	 *
	 * @return void
	 */
	protected static function defineConstants()
	{
        define('MyConstant','MyConstant_VALUE');

	}
    
}