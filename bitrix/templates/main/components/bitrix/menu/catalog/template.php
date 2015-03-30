<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$this->setFrameMode(true);?>

<?if (!empty($arResult)):?>
	<?
	function menu($a,$x=1,$y=1)
	{	global $count;

		$DEPTH_LEVEL = $a[$count]['DEPTH_LEVEL'];
		$s='<ul class="'.($DEPTH_LEVEL==1?'menu__list':'submenu').'">';						//Открываем блок меню или подменю
		$c='';
		$params = '';
		while(1)
		{	if($a[$count]['SELECTED'])$x=1;
			if($a[$count]["PARAMS"]["target"])$params=' target="blank"';
			$s.='<li class="'.($DEPTH_LEVEL==1?'menu__item':'submenu__item').'"><a href="'.$a[$count]['LINK'].'"'.$params;		//Вспомогательня переменная для уменьшения размера кода
			$c='>'.$a[$count]['TEXT'].'</a>';
			$params = '';
			$key=$a[$count]['SELECTED'];
			if(isset($a[++$count]))		//Если текущий элемент данного подменю не является последним или содержит подменю
			{	if($a[$count]['DEPTH_LEVEL']>$y)	//Если текущий элемент содержит подменю
				{	$arr=menu($a,$a[$count-1]['SELECTED']?1:0,$a[$count]['DEPTH_LEVEL']);	//рисуем подменю
					$c.=$arr[0];
					if($arr[1]){$x=1;$key=1;}
					if(!isset($a[$count]))break;	//Если текущий элемент данного подменю является последним, то надо заканчивать рисовать данный уровень вложенности меню
				}
				if($a[$count]['DEPTH_LEVEL']<$y)break;
				$s.='class="'.($DEPTH_LEVEL==1?'menu__link':'submenu__link').($key?' active':'').'"'.$c.'</li>';			//Закрывыаем элемент.
			}
			else break;
		}
		return array($s.'class="'.($DEPTH_LEVEL==1?'menu__link':'submenu__link').($key?' active':'').'"'.$c.'</li></ul>',$x);			//Возвращаем код текущего подменю
	}
	global $count;
	$count=0;
	$arr=menu($arResult);
	echo $arr[0];
	?>
<?endif;?>