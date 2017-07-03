<?php
/**
 * Created by PhpStorm
 * User: Sergey Pokoev
 * www.pokoev.ru
 * @ Академия 1С-Битрикс - 2015
 * @ academy.1c-bitrix.ru
 */

namespace Academy\D7;

use \Bitrix\Main\Entity;
use \Bitrix\Main\Type;

class AuthorTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'author_d7';
    }

    public static function getMap()
    {
        return array(
            //ID
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            //Название
            new Entity\StringField('NAME', array(
                'required' => true,
            )),
            //Фамилия
            new Entity\StringField('LAST_NAME')
        );
    }
}