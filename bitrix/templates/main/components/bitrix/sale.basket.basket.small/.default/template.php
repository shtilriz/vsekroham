<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?
$y=0;									//количество товаров
$summ = 0;								//общая сумма
$discount = 0;							//сумма скидки
$percent = 0;
foreach ($arResult["ITEMS"] as $v) {
	$y += $v["QUANTITY"];
	$summ += $v["PRICE"] * $v["QUANTITY"];
	$discount += $v["DISCOUNT_PRICE"] * $v["QUANTITY"];
}
if (intval($discount) > 0) {
	$percent = round(($discount * 100) / ($summ+$discount), 1);
}
?>

<?$frame = $this->createFrame("basket_small", false)->begin('');
$frame->setBrowserStorage(true);
//$frame->setAnimation(true);
?>
<a href="/basket/">
	<strong>ваша Корзина</strong>
	<p class="stuff">Товаров: <span><?=intval($y)?></span> шт.</p>
	<p class="summa">На сумму: <span><?=number_format($summ,0," "," ")?></span> р.</p>
	<?if ($percent):?>
		<p class="discount">Скидка: <span><?=$percent?>%</span></p>
	<?endif;?>
</a>
<?$frame->end();?>