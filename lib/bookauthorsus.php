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

class BookAuthorsUsTable extends Entity\DataManager
{
    public static function getTableName()
    {
        return 'bookauthorsus_d7';
    }

    public static function getMap()
    {
        return array(
            new Entity\IntegerField('ID', array(
                'primary' => true,
                'autocomplete' => true
            )),
            new Entity\IntegerField('BOOK_ID'),
            new Entity\ReferenceField(
                'BOOK',
                '\Academy\D7\Book2Table',
                array('=this.BOOK_ID' => 'ref.ID')
            ),
            new Entity\IntegerField('AUTHOR_ID'),
            new Entity\ReferenceField(
                'AUTHOR',
                '\Academy\D7\AuthorTable',
                array('=this.AUTHOR_ID' => 'ref.ID')
            )
        );
    }
}