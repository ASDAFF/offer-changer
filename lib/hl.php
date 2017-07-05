<?

namespace Pwd\Offerchanger;

use Bitrix\Main\Context;
use Bitrix\Main\Data\Cache;
use Bitrix\Main\Entity;
use Bitrix\Main\Loader;
use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\Application;

if (!Loader::includeModule('highloadblock')) {
	throw new Main\Exception("Highloadblock module is't installed.");
}

class HL extends Entity\DataManager
{
	/**
	 * Flag that constants are defined
	 *
	 * @var bool
	 */
	public static $constantsDefined = false;

	/**
	 * HL block instances
	 *
	 * @var array
	 */
	protected static $arInstances = array();

	/**
	 * Current HLBlock id
	 *
	 * @var int
	 */
	protected $id = 0;


	/**
	 * HL constructor.
	 *
	 * @param int $id
	 */
	public function __construct($id = 0)
	{
		if( empty($id) ){
		    throw new \Exception('Invalid HLBlock id');
		}
		else{
			$this->id = $id;
		}
	}


	/**
	 * Geting instance of HLBlock class
	 *
	 * @param $code
	 *
	 * @return mixed
	 */
	public static function getInstance($code)
	{
		self::defineConstants();
		$const = __CLASS__ . '\ID_' . $code;
		$hlId = constant($const);
		if( empty(self::$arInstances[$hlId]) ){
			$className = '\\' . __CLASS__;
			self::$arInstances[$hlId] = new $className($hlId);
		}

		return self::$arInstances[$hlId];
	}


	/**
	 * Compile HLBlock class
	 *
	 * @param $hlId - HLBlock Id
	 *
	 * @return Entity\DataManager
	 * @throws \Bitrix\Main\SystemException
	 */
	public function compileEntity($hlId)
	{
		Loader::includeModule('highloadblock');
		$hlblock = HighloadBlockTable::getById($hlId)->fetch();
		$entity = HighloadBlockTable::compileEntity( $hlblock ); //генерация класса
		$entityClass = $entity->getDataClass();

		return $entityClass;
	}

	/**
	 * Определяет константы вида Site\HL\ID_{CODE} и Site\Main\HL\CODE_{ID} для всех highload-блоков
	 *
	 * @param integer $cacheTime Время кэширования
	 * @return void
	 */
	public static function defineConstants($cacheTime = 3600)
	{
		if(self::$constantsDefined)
			return;

		$obCache = Cache::createInstance();
		if( $obCache->initCache($cacheTime, substr(md5(serialize(array(__METHOD__, __CLASS__))), 0, 5), '/PwdOfferchangerHl/') ) {
			$data = $obCache->getVars();
		}
		elseif( $obCache->startDataCache() ){
			$arHblocks = HighloadBlockTable::getList(
				array(
					"select" => array(
						"ID", "NAME"
					)
				)
			);
			while($arHblock = $arHblocks->fetch()) {
				$data[] = array(
					"ID" => $arHblock["ID"],
					"CODE" => $arHblock["NAME"]
				);
			}

			$obCache->endDataCache($data);
		}

		foreach ($data as $arHblock) {
			$arHblock['CODE'] = trim($arHblock['CODE']);
			if ($arHblock['CODE']) {
				$const = __CLASS__ . '\ID_' . $arHblock['CODE'];
				if (!defined($const)) {
					/**
					 * @ignore
					 */
					define($const, $arHblock['ID']);
				}
			}

			$const = __CLASS__ . '\CODE_' . $arHblock['ID'];
			if (!defined($const)) {
				/**
				 * @ignore
				 */
				define($const, $arHblock['CODE']);
			}
		}

		self::$constantsDefined = true;
	}


	/**
	 * Getting campaign headers
	 *
	 * @return mixed
	 * @throws \Bitrix\Main\ArgumentException
	 */
	public function getHeaders($referrer_name = 'referrer' , $cacheTime = 86400)
	{
		$arReq = Context::getCurrent()->getRequest()->toArray();
		$campaign = ( empty($arReq[$referrer_name]) ) ? false : $arReq[$referrer_name];


		$obCacheTag = Application::getInstance()->getTaggedCache();
		$tagName = !empty($campaign) ? 'campaign_offer_' . $campaign : 'campaign_offer';

		$obCache = Cache::createInstance();
		if( $obCache->initCache($cacheTime, substr(md5(serialize(array(__METHOD__, __CLASS__, $campaign))), 0, 5), '/PwdOfferchangerHl/' . $tagName) ) {
			$arOffer = $obCache->getVars();
		}
		elseif( $obCache->startDataCache() ){
			$obCacheTag->startTagCache('/PwdOfferchangerHl/' . $tagName);
			$obEntity = $this->compileEntity($this->id);
			$arOffer = $obEntity::getList(array(
				'filter' => array(
					'UF_PARAMETER' => $campaign
				),
				'limit' => 20
			))->fetchAll();

			$obCacheTag->registerTag($tagName);
			$obCacheTag->endTagCache();

			$obCache->endDataCache($arOffer);
		}

		return $arOffer;
	}


	/**
	 * Handling of updating the element of Headers HLBlock
	 *
	 * @param Entity\Event $event
	 */
	public function HeadersOnAfterUpdateHandler(\Bitrix\Main\Entity\Event $event)
	{

		$arParams = $event->getParameters();

		$param = !empty($arParams['fields']['UF_PARAMETER']) ? 'campaign_offer_' . $arParams['fields']['UF_PARAMETER'] : 'campaign_offer';
		$obCacheTag = Application::getInstance()->getTaggedCache();
		$obCacheTag->clearByTag($param);
	}
}