<?
$arUrlRewrite = array(
	array(
		"CONDITION" => "#^/product/([a-zA-Z0-9_-]*)/([a-zA-Z0-9_-]*)/.*#",
		"RULE" => "",
		"PATH" => "/404.php",
	),
	array(
		"CONDITION" => "#^/product/([a-zA-Z0-9_-]*)/.*#",
		"RULE" => "ELEMENT_CODE=\$1",
		"PATH" => "/product/index.php",
	),
	array(
		"CONDITION" => "#^/brands/([a-zA-Z0-9_-]*)/([a-zA-Z0-9_-]*)/.*#",
		"RULE" => "",
		"PATH" => "/404.php",
	),
	array(
		"CONDITION" => "#^/brands/([a-zA-Z0-9_-]*)/.*#",
		"RULE" => "ELEMENT_CODE=\$1",
		"PATH" => "/brands/index.php",
	),
	array(
		"CONDITION" => "#^/catalog/([a-zA-Z0-9_-]*)/([a-zA-Z0-9_-]*)/([a-zA-Z0-9_-]*)/.*#",
		"RULE" => "",
		"PATH" => "/404.php",
	),
	array(
		"CONDITION" => "#^/catalog/([a-zA-Z0-9_-]*)/([a-zA-Z0-9_-]*)/.*#",
		"RULE" => "SECTION_CODE=\$2",
		"PATH" => "/catalog/index.php",
	),
	array(
		"CONDITION" => "#^/catalog/([a-zA-Z0-9_-]*)/.*#",
		"RULE" => "SECTION_CODE=\$1",
		"PATH" => "/catalog/index.php",
	),
	array(
		"CONDITION" => "#^/makers/([a-zA-Z0-9_-]*)/([a-zA-Z0-9_-]*)/.*#",
		"RULE" => "",
		"PATH" => "/404.php",
	),
	array(
		"CONDITION" => "#^/makers/([a-zA-Z0-9_-]*)/.*#",
		"RULE" => "ELEMENT_CODE=\$1",
		"PATH" => "/makers/index.php",
	),
);

?>
