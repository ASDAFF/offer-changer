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

class Book2Table extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'book_d7_2';
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
            //Год выхода
            new Entity\IntegerField('RELEASED', array(
                'required' => true,
            )),
            //ISBN
            new Entity\StringField('ISBN', array(
                'required' => true,
                'column_name' => 'ISBNCODE',
            )),

            //Дата и время поступления книги в магазин
            new Entity\DatetimeField('TIME_ARRIVAL', array(
                'default_value' => new Type\DateTime
            )),
            //Описание книги
            new Entity\TextField('DESCRIPTION'),
            //Сколько лет книге
            new Entity\ExpressionField('AGE_YEAR',
                'YEAR(CURDATE())-%s', array('RELEASED')
            ),

            /* Не используем в третьей части урока 23: связь многие ко многим*/
            new Entity\IntegerField('AUTHOR_ID'),

            new Entity\ReferenceField(
                'AUTHOR',
                '\Academy\D7\AuthorTable',
                array('=this.AUTHOR_ID' => 'ref.ID')
            )
            /*END Не используем в третьей части урока 23: связь многие ко многим END*/
        );
    }
}