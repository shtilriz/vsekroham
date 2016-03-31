<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("description", "[aR301xp4412]");
$APPLICATION->SetTitle("Сборка мебели");
?><h1><?$APPLICATION->ShowTitle();?></h1>
<div class="assembling">
	<p>
		 Магазин товаров для детей «Все для Крохи» не только предлагает огромный каталог товаров от лучших мировых производителей, но и широкий перечень сопутствующих услуг.
	</p>
	<p>
		 Так, при покупке мебели в магазине, вы можете заказать ее профессиональную сборку. Опытные монтажники на качественно высоком уровне выполнят весь спектр работ, обеспечив мебели желаемую прочность и надежность.
	</p>
	<h2>Стоимость услуги:</h2> <br>
	<p style="color:#ff0000">
		Если заявленная стоимость сборки отличается от фактической, напишите жалобу на pr@vsekroham.ru
	</p>
	<div class="assembling__discount">
		<div class="discount discount_type_15">
 <span> 20%</span>
			<p>
				 от стоимости мебели, но не менее 1500 руб. <br>
				 Кроватки трансформеры, комоды, шкафы.
			</p>

		</div>
		<div class="discount discount_type_10">
			 <span> 15%</span>
			<p>
				 от стоимости мебели, но не менее 1200 руб. <br>
				 Кроватки качалки, кроватки маятники.
			</p>
		</div>
	</div>
	 <?$APPLICATION->IncludeFile('/mebel/form.php');?>
	<p>
		 Монтаж и установка мебели любой сложности производится с использованием профессионального оборудования в предельно сжатые сроки в удобное для клиента время.
	</p>
	<p>
		 По факту выполнения работ, магазин «Все для Крохи» дает гарантию на выполненную работу.
	</p>
</div>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>