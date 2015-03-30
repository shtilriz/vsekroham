<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Логи");
?>

<?
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST["order"]))
		$_SESSION["LOG_SERVICE"]["order"] = trim($_POST["order"]);
	if (isset($_POST["by"]))
		$_SESSION["LOG_SERVICE"]["by"] = trim($_POST["by"]);
	if (isset($_POST["event"]))
		$_SESSION["LOG_SERVICE"]["event"] = trim($_POST["event"]);
	if (isset($_POST["user"]))
		$_SESSION["LOG_SERVICE"]["user"] = $_POST["user"];
	if (isset($_POST["IBLOCK_ID"]))
		$_SESSION["LOG_SERVICE"]["IBLOCK_ID"] = $_POST["IBLOCK_ID"];
	if (isset($_POST["PRODUCT_ID"]))
		$_SESSION["LOG_SERVICE"]["PRODUCT_ID"] = $_POST["PRODUCT_ID"];
	if (isset($_POST["limit"]))
		$_SESSION["LOG_SERVICE"]["limit"] = (int)$_POST["limit"];
}
?>

<form class="form-inline" method="POST">
	<div class="form-group">
		<label>Событие</label>
		<select name="event" class="form-control">
			<option></option>
			<option<?=($_SESSION["LOG_SERVICE"]["event"]=="Изменение элемента инфоблока"?' selected':'')?>>Изменение элемента инфоблока</option>
			<option<?=($_SESSION["LOG_SERVICE"]["event"]=="Добавление элемента инфоблока"?' selected':'')?>>Добавление элемента инфоблока</option>
			<option<?=($_SESSION["LOG_SERVICE"]["event"]=="Удаление элемента инфоблока"?' selected':'')?>>Удаление элемента инфоблока</option>
		</select>
	</div>
	<div class="form-group">
		<label>ID пользователя</label>
		<input type="text" class="form-control" name="user" value="<?=$_SESSION["LOG_SERVICE"]["user"];?>">
	</div>
	<div class="form-group">
		<label>ID инфоблока</label>
		<input type="text" class="form-control" name="IBLOCK_ID" value="<?=$_SESSION["LOG_SERVICE"]["IBLOCK_ID"];?>">
	</div>
	<div class="form-group">
		<label>ID товара</label>
		<input type="text" class="form-control" name="PRODUCT_ID" value="<?=$_SESSION["LOG_SERVICE"]["PRODUCT_ID"];?>">
	</div>
	<div class="form-group">
		<label>Сортировать по</label>
		<select name="order" class="form-control">
			<option value="datetime"<?=($_SESSION["LOG_SERVICE"]["order"]=="datetime"?' selected':'')?>>Дате</option>
			<option value="id"<?=($_SESSION["LOG_SERVICE"]["order"]=="id"?' selected':'')?>>ID</option>
		</select>
	</div>
	<div class="form-group">
		<label>Направление сортировки</label>
		<select name="by" class="form-control">
			<option value="DESC"<?=($_SESSION["LOG_SERVICE"]["by"]=="DESC"?' selected':'')?>>по убыванию</option>
			<option value="ASC"<?=($_SESSION["LOG_SERVICE"]["by"]=="ASC"?' selected':'')?>>по возрастанию</option>
		</select>
	</div>
	<div class="form-group">
		<label>Выводить по</label>
		<select name="limit" class="form-control">
			<?for ($i=10; $i <= 100; $i+=10) {
				echo '<option'.($i==$_SESSION["LOG_SERVICE"]["limit"]?' selected':'').'>'.$i.'</option>';
			}?>
		</select>
	</div>
	<button type="submit" class="btn btn-default">Показать</button>
</form>

<?
if (!isset($_SESSION["LOG_SERVICE"]["order"]))
	$_SESSION["LOG_SERVICE"]["order"] = "datetime";
if (!isset($_SESSION["LOG_SERVICE"]["by"]))
	$_SESSION["LOG_SERVICE"]["by"] = "DESC";
if (!isset($_SESSION["LOG_SERVICE"]["limit"]))
	$_SESSION["LOG_SERVICE"]["limit"] = 10;
$iNumPage = 1;
if (isset($_GET["page"]) && (int)$_GET["page"] > 0)
	$iNumPage = (int)$_GET["page"];

$ib_logs = new LogsDB;
$arFilter = array();
if (isset($_SESSION["LOG_SERVICE"]["event"]) && strlen($_SESSION["LOG_SERVICE"]["event"]))
	$arFilter["event"] = $_SESSION["LOG_SERVICE"]["event"];
if (isset($_SESSION["LOG_SERVICE"]["user"]) && $_SESSION["LOG_SERVICE"]["user"] > 0)
	$arFilter["user"] = $_SESSION["LOG_SERVICE"]["user"];
if (isset($_SESSION["LOG_SERVICE"]["IBLOCK_ID"]) && $_SESSION["LOG_SERVICE"]["IBLOCK_ID"] > 0)
	$arFilter["IBLOCK_ID"] = $_SESSION["LOG_SERVICE"]["IBLOCK_ID"];
if (isset($_SESSION["LOG_SERVICE"]["PRODUCT_ID"]) && $_SESSION["LOG_SERVICE"]["PRODUCT_ID"] > 0)
	$arFilter["PRODUCT_ID"] = $_SESSION["LOG_SERVICE"]["PRODUCT_ID"];

$arLog = $ib_logs->getList(
	array($_SESSION["LOG_SERVICE"]["order"]=>$_SESSION["LOG_SERVICE"]["by"]),
	$arFilter,
	array(),
	array(
		"iNumPage" => $iNumPage,
		"nPageSize" => $_SESSION["LOG_SERVICE"]["limit"]
	)
);
foreach ($arLog["ITEMS"] as $key => $arItem) {
	$arLog["ITEMS"][$key]['date'] = date('d.m.Y H:i:s', $arItem['datetime']);
	$rsUser = CUser::GetByID($arItem['user']);
	$arUser = $rsUser->Fetch();
	$arLog["ITEMS"][$key]['LOGIN'] = $arUser["LOGIN"];
	$arLog["ITEMS"][$key]['PREVIEW_PICTURE'] = CFile::GetPath($arItem["PREVIEW_PICTURE"]);
	$arLog["ITEMS"][$key]['DETAIL_PICTURE'] = CFile::GetPath($arItem["DETAIL_PICTURE"]);
	$arLog["ITEMS"][$key]["PROPERTY_VALUES"] = unserialize($arItem["PROPERTY_VALUES"]);
}
?>

<br/><br/>
<table class="table table-bordered">
	<tr><th>#</th><th>Дата</th><th>Событие</th><th>IP</th><th>Пользователь</th><th>ID товара</th><th>Название товара</th><th></th></tr>
	<?foreach ($arLog["ITEMS"] as $id => $arItem) {
		echo <<<TR
		<tr>
			<td>$arItem[id]</td>
			<td>$arItem[date]</td>
			<td>$arItem[event]</td>
			<td>$arItem[ip]</td>
			<td>$arItem[LOGIN] ($arItem[user])</td>
			<td>$arItem[PRODUCT_ID]</td>
			<td><a href="/product/$arItem[CODE]/" target="_blank">$arItem[NAME]</a></td>
			<td><button type="button" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#logModal" data-id='$id'>Просмотр</button></td>
		</tr>
TR;
	}?>
</table>

<?
$navStr = '';
$countRec = (int)$arLog["COUNT"][0];
$navPageCount = ceil($countRec/$_SESSION["LOG_SERVICE"]["limit"]);
$k = $iNumPage+3;
if ($navPageCount < $k)
	$k = $navPageCount;
$m = $iNumPage-3;
if ($m <= 0)
	$m = 1;
if ($m > 1)
	$navStr = '<li><a href="?page=1">1</a></li><li class="disabled"><a href="#">...</a></li>';
for ($i = $m; $i <= $k; $i++) {
	$navStr .= '<li'.($i==$iNumPage?' class="active"':'').'><a href="?page='.$i.'">'.$i.'</a></li>';
}
if ($k < $navPageCount) {
	$navStr .= '<li class="disabled"><a href="#">...</a></li><li><a href="?page='.$navPageCount.'">'.$navPageCount.'</a></li>';
}
?>
<nav>
	<ul class="pagination">
		<?=$navStr?>
	</ul>
</nav>


<script type="text/javascript">
var LOG = {};
LOG = <?=json_encode($arLog["ITEMS"]);?>;
console.log(LOG);
function appendTab(name, data, tabID) {
	return '<div class="panel panel-default"><div class="panel-heading" role="tab" id="headingOne"><h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#collapse'+tabID+'" aria-expanded="true" aria-controls="collapseOne">'+name+'</a></h4></div><div id="collapse'+tabID+'" class="panel-collapse collapse'+(tabID==1?' in':'')+'" role="tabpanel" aria-labelledby="headingOne"><div class="panel-body">'+data+'</div></div></div>';
}
$(function() {
	$('#logModal').on('show.bs.modal', function (e) {
		$('#logModal .modal-body .panel-group').empty();
		var id = e.relatedTarget.dataset.id;
		$('#logModal .modal-body #pr_id').text(LOG[id].PRODUCT_ID);
		$('#logModal .modal-body #pr_name').text(LOG[id].NAME);
		$('#logModal .modal-body #pr_code').text(LOG[id].CODE);
		$('#logModal .modal-body .panel-group').append(appendTab('Текст анонса', LOG[id].PREVIEW_TEXT, 2));
		$('#logModal .modal-body .panel-group').append(appendTab('Детальное описание', LOG[id].DETAIL_TEXT, 3));
		if (LOG[id].PREVIEW_PICTURE)
			$('#logModal .modal-body .panel-group').append(appendTab('Картинка анонса', '<img src="'+LOG[id].PREVIEW_PICTURE+'" alt="">', 4));
		if (LOG[id].DETAIL_PICTURE)
			$('#logModal .modal-body .panel-group').append(appendTab('Детальная картинка', '<img src="'+LOG[id].DETAIL_PICTURE+'" alt="">', 5));
	})
});
</script>
<div class="modal fade" id="logModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="width: 1000px;">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Просмотр записи лога</h4>
			</div>
			<div class="modal-body">
				<table class="table table-bordered">
					<tr>
						<th>ID товара</th>
						<td id="pr_id"></td>
					</tr>
					<tr>
						<th>Название товара</th>
						<td id="pr_name"></td>
					</tr>
					<tr>
						<th>Символьные код</th>
						<td id="pr_code"></td>
					</tr>
				</table>
				<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
			</div>
		</div>
	</div>
</div>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>