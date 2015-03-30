<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

//delayed function must return a string
if(empty($arResult))
	return "";
	
$strReturn = '<div class="content__top"><nav class="breadcrumbs"><ul><li><a href="/">Главная страница</a></li>';

$num_items = count($arResult);
for($index = 0, $itemSize = $num_items; $index < $itemSize; $index++)
{
	$title = htmlspecialcharsex($arResult[$index]["TITLE"]);
	
	if (($index+1) == $num_items) {
		$strReturn .= '<li><span>'.$title.'</span></li>';
	}
	else {
		if($arResult[$index]["LINK"] <> "" && $index != $itemSize-1)
			$strReturn .= '<li><a href="'.$arResult[$index]["LINK"].'">'.$title.'</a></li>';
		else
			$strReturn .= '<li><span>'.$title.'</span></li>';
	}
}

$strReturn .= '</ul></nav></div>';

return $strReturn;
?>