<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$title = "offer.changer (Компонент подмены оффера)";
$APPLICATION->SetTitle($title); ?>

    <style>
        table {
            font-size: 14px;
            border: 1px solid #333333;
        }
        table .center {
            border-left: 1px solid #333333;
            border-right: 1px solid #333333;
        }
        table td {
            padding: 10px;
        }

        table tr {
            border-bottom: 1px solid #333333;
        }
    </style>


    <h3>Описание</h3>
    <p>Компонент служит для обеспечения подмены оффера, фоновой картинки, текста формы захвата</p>
    <hr/><h3>Пример вызова</h3>
    <pre>
    $APPLICATION->IncludeComponent(
        "offer.changer", "", Array()
    );
    </pre>
    <hr/><h3>Описание параметров</h3>

    <table>
        <tr class="header">
            <td align="center">
                <b>Поле</b>
            </td>
            <td align="center" class="center">
                <b>Параметр</b>
            </td>
            <td align="center">
                <b>Описание</b>
            </td>
        </tr>
        <tr>
            <td>Тип кеширования</td>
            <td class="center">
                <b>CACHE_TYPE</b>
            </td>
            <td>Тип кеширования:
                <ul>
                    <li>
                        <b>A</b>
                        - Авто + Управляемое: автоматически обновляет кеш компонентов в течение заданного времени или при изменении данных;
                    </li>
                    <li>
                        <b>Y</b>
                        - Кешировать: для кеширования необходимо определить время кеширования;
                    </li>
                    <li>
                        <b>N</b>
                        - Не кешировать: кеширования нет в любом случае.
                    </li>
                </ul>
            </td>
        </tr>
        <tr>
            <td>Время кеширования (сек.)</td>
            <td align="center" class="center">
                <b>CACHE_TIME</b>
            </td>
            <td>Время кеширования, указанное в секундах.</td>
        </tr>
    </table>
    <hr>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>