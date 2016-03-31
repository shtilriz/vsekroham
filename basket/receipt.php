<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$ORDER_ID = abs((int)$_GET["order"]);
if ($ORDER_ID && CModule::IncludeModule("sale")) {
    $arResult = array();
    $rsOrder = CSaleOrder::GetList(array(), Array("ID" => $ORDER_ID));
    if ($arOrder = $rsOrder->Fetch()) {
        $arOrder["PRICE"] = (int)$arOrder["PRICE"];
        $arOrder["NDS"] = round($arOrder["PRICE"]/110*10);
        $arResult = $arOrder;
    }
}?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <style>
        body {font-size: 12px;color: #000;}
        table {width: 100%; border-collapse: collapse;}
        table td {padding: 0; vertical-align: top; text-align: left;}

        table.numbers {}
        table.numbers td {border: 1px solid #000; padding: 0 2px; text-align: center; font-weight: bold;}
        .u {border-bottom: 1px solid #000;}
        .help-text {font-size: 8px; text-align: center; padding-top: 0;}
        table.container {width: 685px;}
        table.container > tbody > tr > td {padding: 5px 19px 5px 5px; border: 1px solid #000; vertical-align: top;}
        table.container > tbody > tr > td:first-child {padding-right: 5px;}
        table.f {table-layout: fixed;}
        .i {font-style: italic;}
        .c {text-align: center;}
        .b {font-weight: bold;}

        .w50 {width: 50%;}
        .w48 {width: 48%;}
        .w100 {width: 100%;}

        .ib {display: inline-block; vertical-align: middle; position: relative; top: 1px;}

        .nowrap {white-space: nowrap;}

        .block2 .r-table > tbody > tr > td {padding-bottom: 1px;}
        .add-offset > td {padding-bottom: 6px !important;}
    </style>

</head>
<body>
    <table class="container">
        <tbody>
            <tr>
                <td class="b" style="font-size: 14px; text-align: center;" width="157">
                    <br>
                    И З В Е Щ Е Н И Е
                    <div style="height: 200px;"></div>
                    Кассир
                </td>
                <td>
                    <table>
                        <tbody>
                            <tr>
                                <td class="b i" style="text-align: right; font-size: 10px; padding-top: 2px; padding-bottom: 9px;">Форма № ПД-4</td>
                            </tr>
                            <tr>
                                <td class="u c" style="font-family: arial, sans-serif; font-size: 16px; line-height: 16px;">ООО «ВАВИЛОН»</td>
                            </tr>
                            <tr>
                                <td class="help-text">(наименование получателя платежа)</td>
                            </tr>
                            <tr>
                                <td style="">
                                    <table>
                                        <tr>
                                            <td>
                                                <table class="numbers">
                                                    <tr>
                                                        <td>7</td>
                                                        <td>7</td>
                                                        <td>2</td>
                                                        <td>3</td>
                                                        <td>8</td>
                                                        <td>8</td>
                                                        <td>1</td>
                                                        <td>2</td>
                                                        <td>1</td>
                                                        <td>9</td>
                                                    </tr>
                                                </table>
                                                <div class="help-text">(ИНН получателя платежа)</div>
                                            </td>
                                            <td width="40"></td>
                                            <td>
                                                <table class="numbers">
                                                    <tr>
                                                        <td>4</td>
                                                        <td>0</td>
                                                        <td>7</td>
                                                        <td>0</td>
                                                        <td>2</td>
                                                        <td>8</td>
                                                        <td>1</td>
                                                        <td>0</td>
                                                        <td>1</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>8</td>
                                                        <td>2</td>
                                                        <td>1</td>
                                                        <td>6</td>
                                                        <td>4</td>
                                                    </tr>
                                                </table>
                                                <div class="help-text">(номер счета получателя платежа)</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="">
                                    <table>
                                        <tr>
                                            <td><b>в </b></td>
                                            <td>
                                                <table>
                                                    <tr>
                                                        <td class="u c">ВТБ24 (ЗАО) г. Москва</td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="help-text">(наименование банка получателя платежа)</div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td style="text-align: center; padding-top: 2px;">БИК</td>
                                            <td>
                                                <table class="numbers">
                                                    <tr>
                                                        <td>0</td>
                                                        <td>4</td>
                                                        <td>4</td>
                                                        <td>5</td>
                                                        <td>2</td>
                                                        <td>5</td>
                                                        <td>7</td>
                                                        <td>1</td>
                                                        <td>6</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="">
                                    <table>
                                        <tr>
                                            <td>Номер кор./сч. банка получателя платежа</td>
                                            <td>
                                                <table class="numbers">
                                                    <tr>
                                                        <td>3</td>
                                                        <td>0</td>
                                                        <td>1</td>
                                                        <td>0</td>
                                                        <td>1</td>
                                                        <td>8</td>
                                                        <td>1</td>
                                                        <td>0</td>
                                                        <td>1</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>7</td>
                                                        <td>1</td>
                                                        <td>6</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="">
                                    <table>
                                        <tr>
                                            <td class="u w48">Оплата заказа № <?=$arResult["ID"]?></td>
                                            <td width="40"></td>
                                            <td class="u w48"></td>
                                        </tr>
                                        <tr>
                                            <td class="help-text">(наименование платежа)</td>
                                            <td width="40"></td>
                                            <td class="help-text">(номер лицевого счета (код) плательщика)</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="">
                                    <table>
                                        <tr>
                                            <td class="nowrap">Ф. И. О. плательщика</td>
                                            <td class="u w100">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="">
                                    <table>
                                        <tr>
                                            <td class="nowrap">Адрес плательщика</td>
                                            <td class="u w100">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="">
                                    <table>
                                        <tr>
                                            <td>
                                                <table>
                                                    <tr>
                                                        <td class="nowrap">Сумма платежа</td>
                                                        <td width="70" class="u b i c"><?=$arResult["PRICE"]?></td>
                                                        <td>руб.</td>
                                                        <td width="30" class="u b i c">00</td>
                                                        <td>коп.</td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="30"></td>
                                            <td width="250">
                                                <table>
                                                    <tr>
                                                        <td style="text-align: left;">включая НДС 10% (<?=$arOrder["NDS"]?> руб.)</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="">
                                    <table>
                                        <tr>
                                            <td>
                                                <table>
                                                    <tr>
                                                        <td>Итого</td>
                                                        <td width="70" class="u b i c"><?=$arResult["PRICE"]?></td>
                                                        <td>руб.</td>
                                                        <td width="30" class="u b i c">00</td>
                                                        <td>коп.</td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="30"></td>
                                            <td width="250">
                                                <table style="text-align: right;">
                                                    <tr>
                                                        <td width="170" style="text-align: right;">
                                                            <div class="ib">
                                                                <table style="width: auto;">
                                                                    <tr>
                                                                        <td>&laquo;</td>
                                                                        <td width="20" class="u"></td>
                                                                        <td>&raquo;</td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </td>
                                                        <td width="95" class="u b i c">&nbsp;</td>
                                                        <td style="text-align: right;">20</td>
                                                        <td width="30" class="u b i c">&nbsp;</td>
                                                        <td>г.</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px;">
                                    С условиями приема указанной в платежном документе суммы, в т.ч. с суммой взимаемой платы за услуги банка, ознакомлен и согласен.
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <table class="f">
                                        <tr>
                                            <td></td>
                                            <td>
                                                <table class="f">
                                                    <tr>
                                                        <td class="b nowrap">Подпись плательщика</td>
                                                        <td class="u"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr class="block2">
                <td class="b" style="font-size: 14px; text-align: center;" width="157">
                    <br>
                    <br>
                    <div style="height: 211px;"></div>
                    Квитанция <br>
                    Кассир
                </td>
                <td>
                    <table class="r-table">
                        <tbody>
                            <tr>
                                <td class="u c" style="font-family: arial, sans-serif; font-size: 16px; line-height: 16px;">ООО «ВАВИЛОН»</td>
                            </tr>
                            <tr>
                                <td class="help-text">(наименование получателя платежа)</td>
                            </tr>
                            <tr>
                                <td style="">
                                    <table>
                                        <tr>
                                            <td>
                                                <table class="numbers">
                                                    <tr>
                                                        <td>7</td>
                                                        <td>7</td>
                                                        <td>2</td>
                                                        <td>3</td>
                                                        <td>8</td>
                                                        <td>8</td>
                                                        <td>1</td>
                                                        <td>2</td>
                                                        <td>1</td>
                                                        <td>9</td>
                                                    </tr>
                                                </table>
                                                <div class="help-text">(ИНН получателя платежа)</div>
                                            </td>
                                            <td width="40"></td>
                                            <td>
                                                <table class="numbers">
                                                    <tr>
                                                        <td>4</td>
                                                        <td>0</td>
                                                        <td>7</td>
                                                        <td>0</td>
                                                        <td>2</td>
                                                        <td>8</td>
                                                        <td>1</td>
                                                        <td>0</td>
                                                        <td>1</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>8</td>
                                                        <td>2</td>
                                                        <td>1</td>
                                                        <td>6</td>
                                                        <td>4</td>
                                                    </tr>
                                                </table>
                                                <div class="help-text">(номер счета получателя платежа)</div>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="">
                                    <table>
                                        <tr>
                                            <td><b>в </b></td>
                                            <td>
                                                <table>
                                                    <tr>
                                                        <td class="u c">ВТБ24 (ЗАО) г. Москва</td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="help-text">(наименование банка получателя платежа)</div>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td style="text-align: center; padding-top: 2px;">БИК</td>
                                            <td>
                                                <table class="numbers">
                                                    <tr>
                                                        <td>0</td>
                                                        <td>4</td>
                                                        <td>4</td>
                                                        <td>5</td>
                                                        <td>2</td>
                                                        <td>5</td>
                                                        <td>7</td>
                                                        <td>1</td>
                                                        <td>6</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr class="add-offset">
                                <td style="">
                                    <table>
                                        <tr>
                                            <td>Номер кор./сч. банка получателя платежа</td>
                                            <td>
                                                <table class="numbers">
                                                    <tr>
                                                        <td>3</td>
                                                        <td>0</td>
                                                        <td>1</td>
                                                        <td>0</td>
                                                        <td>1</td>
                                                        <td>8</td>
                                                        <td>1</td>
                                                        <td>0</td>
                                                        <td>1</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>0</td>
                                                        <td>7</td>
                                                        <td>1</td>
                                                        <td>6</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr class="add-offset">
                                <td style="">
                                    <table>
                                        <tr>
                                            <td class="u w48">Оплата заказа № <?=$arResult["ID"]?></td>
                                            <td width="40"></td>
                                            <td class="u w48"></td>
                                        </tr>
                                        <tr>
                                            <td class="help-text">(наименование платежа)</td>
                                            <td width="40"></td>
                                            <td class="help-text">(номер лицевого счета (код) плательщика)</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr class="add-offset">
                                <td style="">
                                    <table>
                                        <tr>
                                            <td class="nowrap">Ф. И. О. плательщика</td>
                                            <td class="u w100"></td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr class="add-offset">
                                <td style="">
                                    <table>
                                        <tr>
                                            <td class="nowrap">Адрес плательщика</td>
                                            <td class="u w100">&nbsp;</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr class="add-offset">
                                <td style="">
                                    <table>
                                        <tr>
                                            <td>
                                                <table>
                                                    <tr>
                                                        <td class="nowrap">Сумма платежа</td>
                                                        <td width="70" class="u b i c"><?=$arResult["PRICE"]?></td>
                                                        <td>руб.</td>
                                                        <td width="30" class="u b i c">00</td>
                                                        <td>коп.</td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="30"></td>
                                            <td width="250">
                                                <table>
                                                    <tr>
                                                        <td style="text-align: left;">включая НДС 10% (<?=$arOrder["NDS"]?> руб.)</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr class="add-offset">
                                <td style="">
                                    <table>
                                        <tr>
                                            <td>
                                                <table>
                                                    <tr>
                                                        <td>Итого</td>
                                                        <td width="70" class="u b i c"><?=$arResult["PRICE"]?></td>
                                                        <td>руб.</td>
                                                        <td width="30" class="u b i c">00</td>
                                                        <td>коп.</td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td width="30"></td>
                                            <td width="250">
                                                <table style="text-align: right;">
                                                    <tr>
                                                        <td width="170" style="text-align: right;">
                                                            <div class="ib">
                                                                <table style="width: auto;">
                                                                    <tr>
                                                                        <td>&laquo;</td>
                                                                        <td width="20" class="u"></td>
                                                                        <td>&raquo;</td>
                                                                    </tr>
                                                                </table>
                                                            </div>
                                                        </td>
                                                        <td width="95" class="u b i c">&nbsp;</td>
                                                        <td style="text-align: right;">20</td>
                                                        <td width="30" class="u b i c">&nbsp;</td>
                                                        <td>г.</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-size: 11px; padding-top: 9px;">
                                    С условиями приема указанной в платежном документе суммы, в т.ч. с суммой взимаемой платы за услуги банка, ознакомлен и согласен.
                                </td>
                            </tr>
                            <tr class="add-offset">
                                <td>
                                    <table class="">
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td style="width: 60%;">
                                                <table class="f">
                                                    <tr>
                                                        <td class="b nowrap">Подпись плательщика</td>
                                                        <td class="u"></td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    <script type="text/javascript" src="/bitrix/templates/main/js/libs/jquery.min.js"></script>
    <script type="text/javascript" src="/bitrix/templates/main/js/html2canvas.js"></script>
    <br/>
    <br/>
    <br/>
    <a id="downloadImgLink" download="Заказ №4463.png" href="#" target="_blank" style="font-size:16px; display: inline-block; border: 1px solid #ccc; color: #000; padding: 3px 8px; text-decoration: none;">Скачать квитанцию</a>
    <script type="text/javascript">
        html2canvas(
            document.body,
            {
                onrendered: function(canvas) {
                    $('#downloadImgLink').attr('href', canvas.toDataURL());
                },
                width: 700,
                height: 585
            }
        );
    </script>
</body>
</html>