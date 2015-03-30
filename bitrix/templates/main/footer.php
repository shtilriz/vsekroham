			<?if ($APPLICATION->GetCurPage() != "/404.php"):?>
				</div>
			</section>
			<?endif;?>
			<!-- .left-column -->
			<aside class="left-column">
				<nav class="menu menu_type_leftmenu">
					<h3>Каталог товаров</h3>
					<?$APPLICATION->IncludeComponent("bitrix:menu", "catalog", Array(
						"ROOT_MENU_TYPE" => "left",	// Тип меню для первого уровня
							"MENU_CACHE_TYPE" => "A",	// Тип кеширования
							"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
							"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
							"MENU_CACHE_GET_VARS" => array(	// Значимые переменные запроса
								0 => "",
							),
							"MAX_LEVEL" => "2",	// Уровень вложенности меню
							"CHILD_MENU_TYPE" => "left",	// Тип меню для остальных уровней
							"USE_EXT" => "Y",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
							"DELAY" => "N",	// Откладывать выполнение шаблона меню
							"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
						),
						false
					);?>

					<?$APPLICATION->IncludeComponent("bitrix:menu", "brands", Array(
						"ROOT_MENU_TYPE" => "brands",	// Тип меню для первого уровня
							"MENU_CACHE_TYPE" => "A",	// Тип кеширования
							"MENU_CACHE_TIME" => "3600",	// Время кеширования (сек.)
							"MENU_CACHE_USE_GROUPS" => "Y",	// Учитывать права доступа
							"MENU_CACHE_GET_VARS" => "",	// Значимые переменные запроса
							"MAX_LEVEL" => "1",	// Уровень вложенности меню
							"CHILD_MENU_TYPE" => "",	// Тип меню для остальных уровней
							"USE_EXT" => "N",	// Подключать файлы с именами вида .тип_меню.menu_ext.php
							"DELAY" => "N",	// Откладывать выполнение шаблона меню
							"ALLOW_MULTI_SELECT" => "N",	// Разрешить несколько активных пунктов одновременно
						),
						false
					);?>
				</nav>
				<?/*
				<div class="banner">
					<?$APPLICATION->IncludeComponent("bitrix:main.include", "", array(
						"AREA_FILE_SHOW" => "file",
							"PATH" => "/bitrix/templates/main/page_templates/yandex-market.php",
							"EDIT_TEMPLATE" => ""
						),
						false,
						array(
						"ACTIVE_COMPONENT" => "Y"
						)
					);?>
				</div>
				*/?>
			</aside>
			<!-- !end .left-column -->
		</div><!-- !end #content -->

		<?$textdescription = $APPLICATION->GetPageProperty("textdescription");?>
		<?if($textdescription):?>
			<div class="content__bottom content__bottom_bgcolor_gray"><?=$textdescription;?></div>
		<?else:?>
			<div class="content__bottom"><?$APPLICATION->ShowViewContent("textdescription");?></div>
		<?endif?>

		<div class="forfooter"></div>
	</div><!-- !end .layout -->

	<!-- .footer -->
	<footer class="footer">
		<div class="footer__inner">
			<div class="footer__top">
				<div class="column column1">
					<div class="phones">
						<?$APPLICATION->IncludeComponent("bitrix:main.include","",
							array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => "/bitrix/templates/main/page_templates/footer/phone.php",
								"EDIT_TEMPLATE" => ""
							),
							false
						);?>
					</div>
				</div>
				<div class="column column2">
					<div class="shedule">
						<?$APPLICATION->IncludeComponent("bitrix:main.include","",
							array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => "/bitrix/templates/main/page_templates/footer/rate.php",
								"EDIT_TEMPLATE" => ""
							),
							false
						);?>
					</div>
				</div>
				<div class="column column3">
				  <div class="f-cards">
				    <strong>Принимаем к оплате:</strong>
				    <img class="f-cards-visa" src="/bitrix/templates/main/images/visa.png" height="27" width="84" alt="">
				    <img class="f-cards-master-card" src="/bitrix/templates/main/images/master-card.png" height="33" width="57" alt="">
				  </div>
				</div>
				<div class="column column4">
					<nav class="social-links">
						<?$APPLICATION->IncludeComponent("bitrix:main.include","",
							array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => "/bitrix/templates/main/page_templates/footer/social.php",
								"EDIT_TEMPLATE" => ""
							),
							false
						);?>
					</nav>
				</div>
			</div>
			<div class="footer__bottom">
				<div class="copyright">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/bitrix/templates/main/page_templates/footer/copy.php",
							"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</div>
			</div>
		</div>
	</footer>

</div>

<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/custom.forms.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/chosen.jquery.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/chosen.proto.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/multiselect.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/multiselect.filter.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/plugins.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/caroufredsel.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.touchSwipe.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/placeholders.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.jscrollpane.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.mousewheel.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/autosize.js"></script>
<?/*<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/fancybox.js"></script>*/?>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/jquery.validate.min.js"></script>
<?if ($APPLICATION->GetCurPage() == "/basket/order.php"):?>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/datepicker/bootstrap-datepicker.js"></script>
	<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/datepicker/locales/bootstrap-datepicker.ru.js"></script>
<?endif?>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/effects.js"></script>
<script type="text/javascript" src="<?=SITE_TEMPLATE_PATH?>/js/main.js"></script>

<!-- Yandex.Metrika counter -->
<?if (strlen($GLOBALS["YAPARAMS"])):?>
<script type="text/javascript">
	<?=$GLOBALS["YAPARAMS"];?>
</script>
<?endif;?>

<script type="text/javascript">
(function (d, w, c) {
    (w[c] = w[c] || []).push(function() {
        try {
            w.yaCounter15270730 = new Ya.Metrika({id:15270730,
                    webvisor:true,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    trackHash:true,params:window.yaParams||{ }});
        } catch(e) { }
    });

    var n = d.getElementsByTagName("script")[0],
        s = d.createElement("script"),
        f = function () { n.parentNode.insertBefore(s, n); };
    s.type = "text/javascript";
    s.async = true;
    s.src = (d.location.protocol == "https:" ? "https:" : "http:") + "//mc.yandex.ru/metrika/watch.js";

    if (w.opera == "[object Opera]") {
        d.addEventListener("DOMContentLoaded", f, false);
    } else { f(); }
})(document, window, "yandex_metrika_callbacks");
</script>
<noscript><div><img src="//mc.yandex.ru/watch/15270730" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<!-- google Analitycs -->
<script type="text/javascript">
var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-42061895-1']);
  _gaq.push(['_trackPageview']);

  (function() {
var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
</script>
<!-- /google Analitycs -->

</body>
</html>