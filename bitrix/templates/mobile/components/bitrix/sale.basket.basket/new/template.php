<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<div class="b-content__header b-header">
	<a id="back_page" href="#" class="b-header__back b-back"></a>
	<div class="b-header__title"><span>Корзина</span></div>
</div>

<div class="b-buttons-double">
	<a href="/catalog/kolyaski/" class="b-buttons-double__button">Вернуться в каталог</a>
	<?if ($arResult["ITEMS"]["AnDelCanBuy"]):?>
	<a href="#" class="b-buttons-double__button" id="clearBasket">Очистить корзину</a>
	<?endif;?>
</div>

<div id="basket-list">
	<?if($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') $APPLICATION->RestartBuffer();?>
	<?if (strlen($arResult["ERROR_MESSAGE"]) <= 0) {
		?>
		<div id="warning_message">
			<?
			if (is_array($arResult["WARNING_MESSAGE"]) && !empty($arResult["WARNING_MESSAGE"])) {
				foreach ($arResult["WARNING_MESSAGE"] as $v)
					echo ShowError($v);
			}
			?>
		</div>

		<?$normalCount = count($arResult["ITEMS"]["AnDelCanBuy"]);?>
		<div class="cart">
			<form method="post" action="<?=POST_FORM_ACTION_URI?>" name="basket_form" id="basket_form">
				<?
				//include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_delivery.php");
				include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items.php");
				//include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_delayed.php");
				//include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_subscribed.php");
				//include($_SERVER["DOCUMENT_ROOT"].$templateFolder."/basket_items_not_available.php");
				?>
				<input type="hidden" name="BasketOrder" value="BasketOrder" />
				<input name="BasketRefresh" type="hidden" value="Y" />
				<!-- <input type="hidden" name="ajax_post" id="ajax_post" value="Y"> -->
			</form>
		</div>
		<?
	}
	else {
		ShowError($arResult["ERROR_MESSAGE"]);
	}
	?>
	<?if($_SERVER['HTTP_X_REQUESTED_WITH']=='XMLHttpRequest') die();?>
</div>