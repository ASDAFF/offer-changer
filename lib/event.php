<?php
/**
 * Created by PhpStorm
 * User: Sergey Pokoev
 * www.pokoev.ru
 * @ Академия 1С-Битрикс - 2015
 * @ academy.1c-bitrix.ru
 *
 * файл event.php
 */

namespace Academy\D7;

class event
{
    public function eventHandler(\Bitrix\Main\Entity\Event $event)
    {
        //die();
        $result = new \Bitrix\Main\Entity\EventResult;

        echo'Тело события<br>';

        //$result = 'Сообщение вернул обработчик'; //Не правильно

        $result->modifyFields(array('result' => 'Сообщение вернул обработчик'));

        return $result;
    }
}