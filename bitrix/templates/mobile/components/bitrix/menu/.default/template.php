<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?if (!empty($arResult)):?>
<aside class="sidebar">
	<nav class="side-menu js-side-menu">
	<?
	function menu($a,$x=1,$y=1)
	{	global $count;

		$DEPTH_LEVEL = $a[$count]['DEPTH_LEVEL'];
		$s='<ul>';						//Открываем блок меню или подменю
		$c='';
		while(1)
		{	if($a[$count]['SELECTED'])$x=1;
			$s.='<li'.($a[$count]["IS_PARENT"]?' class="deeper"':'').'><a href="'.$a[$count]['LINK'].'"'.($a[$count]["PARAMS"]["CSS_ID"]?' id="'.$a[$count]["PARAMS"]["CSS_ID"].'"':'');		//Вспомогательня переменная для уменьшения размера кода
			$c='>'.($a[$count]["PARAMS"]["CSS_CLASS"]?'<span class="icon '.$a[$count]["PARAMS"]["CSS_CLASS"].'"></span>':'').$a[$count]['TEXT'].'</a>';
			$key=$a[$count]['SELECTED'];
			if(isset($a[++$count]))		//Если текущий элемент данного подменю не является последним или содержит подменю
			{	if($a[$count]['DEPTH_LEVEL']>$y)	//Если текущий элемент содержит подменю
				{	$arr=menu($a,$a[$count-1]['SELECTED']?1:0,$a[$count]['DEPTH_LEVEL']);	//рисуем подменю
					$c.=$arr[0];
					if($arr[1]){$x=1;$key=1;}
					if(!isset($a[$count]))break;	//Если текущий элемент данного подменю является последним, то надо заканчивать рисовать данный уровень вложенности меню
				}
				if($a[$count]['DEPTH_LEVEL']<$y)break;
				$s.=$c.'</li>';			//Закрывыаем элемент.
			}
			else break;
		}
		return array($s.$c.'</li></ul>',$x);			//Возвращаем код текущего подменю
	}
	global $count;
	$count=0;
	$arr=menu($arResult);
	echo $arr[0];
	?>
	</nav>
</aside>
<?endif;?>