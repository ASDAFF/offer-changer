<?php
/**
 * Created by PhpStorm
 * User: Sergey Pokoev
 * www.pokoev.ru
 * @ Академия 1С-Битрикс - 2015
 * @ academy.1c-bitrix.ru
 *
 * файл division.php
 */

namespace Academy\D7;

class Division
{
    public static function divided($parameters1 = 0, $parameters2 = 0)
    {
        if($parameters2===0)
            throw new DivisionError('Деление на ноль',$parameters1,$parameters2);

        $result = $parameters1/$parameters2;
        return $result;
    }
}
