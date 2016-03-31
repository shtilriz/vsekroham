
			</div>
		</div>
		<div class="block-contacts">
			<div class="b-footer"><span class="icon-wr"><span class="sprite-phone-xs"></span></span>
				<div class="b-footer__title">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/bitrix/templates/mobile/page_templates/phone_title.php",
							"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</div>
				<div class="b-footer__content">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/bitrix/templates/mobile/page_templates/phone.php",
							"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</div>
			</div>
			<div class="b-footer"><span class="icon-wr"><span class="sprite-alarm"></span></span>
				<div class="b-footer__title">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/bitrix/templates/mobile/page_templates/rate_title.php",
							"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</div>
				<div class="b-footer__content">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/bitrix/templates/mobile/page_templates/rate.php",
							"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</div>
			</div>
			<div class="b-footer"><span class="icon-wr"><span class="sprite-credit-card"></span></span>
				<div class="b-footer__title">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/bitrix/templates/mobile/page_templates/pay_title.php",
							"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</div>
				<div class="b-footer__content">
					<div class="b-footer__cards">
						<?$APPLICATION->IncludeComponent("bitrix:main.include","",
							array(
								"AREA_FILE_SHOW" => "file",
								"PATH" => "/bitrix/templates/mobile/page_templates/pay_icons.php",
								"EDIT_TEMPLATE" => ""
							),
							false
						);?>
					</div>
				</div>
			</div>
			<div class="b-footer"><span class="icon-wr"><span class="sprite-globe-sm"></span></span>
				<div class="b-footer__title">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/bitrix/templates/mobile/page_templates/social_title.php",
							"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</div>
				<div class="b-footer__content">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/bitrix/templates/mobile/page_templates/social.php",
							"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</div>
			</div>
		</div>
		<div class="root-footer"></div>
		<a class="hide-sidebar-link"></a>
	</div>
	<footer class="page-footer page-footer_sticky">
		<div class="page-footer-top">
			<div class="page-footer-top__inner">
				<p class="page-footer-top__copyright">
					<?$APPLICATION->IncludeComponent("bitrix:main.include","",
						array(
							"AREA_FILE_SHOW" => "file",
							"PATH" => "/bitrix/templates/mobile/page_templates/copy.php",
							"EDIT_TEMPLATE" => ""
						),
						false
					);?>
				</p>
			</div>
		</div>
		<ul class="b-footer-nav">
			<li class="b-footer-nav__item">
				<?$APPLICATION->IncludeComponent("bitrix:main.include","",
					array(
						"AREA_FILE_SHOW" => "file",
						"PATH" => "/bitrix/templates/mobile/page_templates/call.php",
						"EDIT_TEMPLATE" => ""
					),
					false
				);?>
			</li>
		</ul>
	</footer>
	<?$APPLICATION->IncludeFile("/m_include/modal/info-modal.php");?>

	<script src="<?=SITE_TEMPLATE_PATH?>/lib-bower/slick.js/slick/slick.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/lib-bower/bootstrap-sass/assets/javascripts/bootstrap/modal.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/lib-bower/bootstrap-sass/assets/javascripts/bootstrap/dropdown.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/lib-bower/bootstrap-sass/assets/javascripts/bootstrap/tab.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/lib-bower/bootstrap-sass/assets/javascripts/bootstrap/collapse.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/lib-bower/nouislider/distribute/jquery.nouislider.all.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/jquery.validate.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/0.6.10/js/min/perfect-scrollbar.jquery.min.js"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/effects.js?t=<?=filemtime($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/js/effects.js')?>"></script>
	<script src="<?=SITE_TEMPLATE_PATH?>/js/main.js?t=<?=filemtime($_SERVER["DOCUMENT_ROOT"].SITE_TEMPLATE_PATH.'/js/main.js')?>"></script>

	<!-- Yandex.Metrika counter -->
	<script type="text/javascript">
		(function(d, w, c) {
			(w[c] = w[c] || []).push(function() {
				try {
					w.yaCounter15270730 = new Ya.Metrika({
						id: 15270730,
						clickmap: true,
						trackLinks: true,
						accurateTrackBounce: true,
						webvisor: true,
						trackHash: true,
						ecommerce: "dataLayer"
					});
				} catch (e) {}
			});

			var n = d.getElementsByTagName("script")[0],
				s = d.createElement("script"),
				f = function() { n.parentNode.insertBefore(s, n); };
			s.type = "text/javascript";
			s.async = true;
			s.src = "https://mc.yandex.ru/metrika/watch.js";

			if (w.opera == "[object Opera]") {
				d.addEventListener("DOMContentLoaded", f, false);
			} else { f(); }
		})(document, window, "yandex_metrika_callbacks");
	</script>
	<noscript><div><img src="https://mc.yandex.ru/watch/15270730" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
	<!-- /Yandex.Metrika counter -->

	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');

		ga('create', 'UA-42061895-1', 'auto');
		ga('send', 'pageview');
		<?=$GLOBALS["GAPARAMS"];?>
	</script>
</body>
</html>