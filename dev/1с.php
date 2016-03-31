<h3>Обновить остатки</h3>
<form method="post" action="http://www.vsekroham.ru/api1c.php?action=updrest" enctype="multipart/form-data">
	<input type="file" name="file">
	<input type="submit" value="Отправить">
</form>
<hr/>

<h3>Активировать товары</h3>
<form method="post" action="http://www.vsekroham.ru/api1c.php?action=setactive" enctype="multipart/form-data">
	<input type="file" name="file">
	<input type="submit" value="Отправить">
</form>
<hr/>

<h3>Деактивировать товары</h3>
<form method="post" action="http://www.vsekroham.ru/api1c.php?action=setunactive" enctype="multipart/form-data">
	<input type="file" name="file">
	<input type="submit" value="Отправить">
</form>
<hr/>

<h3>Добавить новые товары из XML файла</h3>
<form method="post" action="http://www.vsekroham.ru/api1c.php?action=newoffer" enctype="multipart/form-data">
	<input type="file" name="file">
	<input type="submit" value="Отправить">
</form>