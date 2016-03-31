<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Покупка и оплата");
?><div class="main-column">
	<div class="b-top">
	</div>
 <br>
	<h1 class="bold no-transform">Как сделать заказ</h1>
	<p>
		 Сделать покупку в интернет-магазине «Все для Крохи» очень просто! Процесс занимает считанные минуты, и не доставит хлопот!
	</p>
	<div class="b-icons">
		<div class="b-icons__item">
			<div class="b-icons__icon">
 <img src="/bitrix/templates/main/images/b-icons/circle--ok.png">
			</div>
			<div class="b-icons__content">
				<div class="b-icons__title">
					 1.Пожалуйста, выберите товар!
				</div>
				<p>
					 Для поиска нужного продукта можно использовать каталог товаров по типу&nbsp;или по бренду.&nbsp;Раскрывающийся список с группами товара&nbsp;находится в левой части экрана.
				</p>
			</div>
		</div>
		<div class="b-icons__item">
			<div class="b-icons__icon">
 <img src="/bitrix/templates/main/images/b-icons/shopping-cart--add.png">
			</div>
			<div class="b-icons__content">
				<div class="b-icons__title">
					 2.Кладем товары в корзину!
				</div>
				<p>
					 После того, как вы нашли нужный товар, перейдите в карточку этого товара, выберите понравившийся вам цвет, размер (если нужно) и нажмите кнопку «В корзину». После этого на экране появится всплывающее окно, откуда вы сможете перейти на оформление заказа или продолжить покупки. В корзину можно добавлять неограниченное количество товаров.
				</p>
			</div>
		</div>
		<div class="b-icons__item">
			<div class="b-icons__icon">
 <img src="/bitrix/templates/main/images/b-icons/pen-3.png">
			</div>
			<div class="b-icons__content">
				<div class="b-icons__title">
					 3.Оформляем заказ!
				</div>
				<p>
					 В корзине вы можете изменить количество товара или удалить какую-либо позицию. В корзине отображается стоимость каждой позиции и итоговая цена всей покупки.&nbsp;После&nbsp;того как вы проверили товар в корзине, нажимайте на кнопку «Оформить заказ» и переходите к заполнению полей с&nbsp;ФИО, адресом&nbsp;доставки, контактным&nbsp;телефоном. Когда вы заполните все данные, нажмите на кнопку «Подтвердить покупку». Ожидайте звонка от менеджера!
				</p>
				<p>
 <br>
				</p>
			</div>
		</div>
		<div class="b-icons__item">
			<div class="b-icons__icon">
 <img src="/bitrix/templates/main/images/b-icons/megaphone-.png">
			</div>
			<div class="b-icons__content">
				<div class="b-icons__title">
					 4.Уточняем!
				</div>
				<p>
					 Если какая-либо информация из вашего заказа вам непонятна или требует уточнений, вы можете прописать комментарий в поле «Дополнительная информация» и наш менеджер свяжется с вами в удобное для вас время. Обратите внимание, что вы можете изменить свой заказ по телефону или отказаться от него. Делайте заказ, мы позвоним и проконсультируем!
				</p>
			</div>
		</div>
	</div>
	<a href="#" style="margin-top: 8px; padding: 7px 14px;" class="pull-right btn btn-green" data-target="pay">Оплатить покупку</a>
	<h2 class="no-transform bold">Способы оплаты</h2>
	<p>
		 Для удобства наших клиентов мы предлагаем 3 способа оплаты!
	</p>
	<div class="b-icons">
		<div class="b-icons__item">
			<div class="b-icons__icon">
 <img src="/bitrix/templates/main/images/b-icons/forma-1.png">
			</div>
			<div class="b-icons__content">
				<div class="b-icons__title">
					 1. Оплата наличными при получении
				</div>
				<p>
					 Наличный расчет возможен, как в случае доставки заказа курьером по Москве, так и при самостоятельном получении заказа в магазине. Оплата происходит по факту получения клиентом покупки и проверки комплектации, целостности товара, верного заполнения гарантийного талона.
				</p>
			</div>
		</div>
		<div class="b-icons__item">
			<div class="b-icons__icon">
 <img src="/bitrix/templates/main/images/b-icons/document--ok.png">
			</div>
			<div class="b-icons__content">
				<div class="b-icons__title">
					 2. Оплата по квитанции в банке
				</div>
				<p>
					 Данный способ оплаты подразумевает заполнение стандартного бланка с указанием реквизитов платежа в выбранном отделении банка. Деньги со счета клиента поступают на расчётный счет магазина. Это и является свидетельством покупки. Период осуществления перевода и комиссия&nbsp;регламентируется системой выбранного банка.
				</p>
			</div>
		</div>
		<div class="b-icons__item">
			<div class="b-icons__icon">
 <img src="/bitrix/templates/main/images/b-icons/credit-card.png">
			</div>
			<div class="b-icons__content">
				<div class="b-icons__title">
					 3. Оплата банковской картой
				</div>
				<p>
					 Оплата покупки банковой картой является наиболее простым и удобным способом, так как предполагает моментальное осуществление перевода с минимальными комиссиями или без них.
				</p>
			</div>
		</div>
	</div>
</div>
<div class="p-by popup" id="pay">
	<form name="payModal" action="#">
		<div class="popup__top">
 <a href="#" class="popup__close">X</a> <strong class="popup__title">Оплатите вашу покупку</strong>
		</div>
		<div class="popup__content">
			<div id="messageBoxPay">
			</div>
			<div class="form-field">
				<div class="form-field__inputtext">
 <span for="phone">Введите номер заказа</span> <input type="text" name="order" value="" class="inputtext">
					<div class="help-block">
 <i>Номер есть в почтовом уведомлении о заказе или уточните у менеджера.</i>
					</div>
				</div>
			</div>
		</div>
		<div class="popup__footer">
			<div class="form-field">
				<div class="form-field__button">
 <input type="submit" value="Оплатить" class="form-button button_type_submit">
				</div>
			</div>
		</div>
	</form>
</div>
 <br><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>