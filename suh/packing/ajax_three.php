<?
	include_once("./include/common.php");
?>

<?
	$idx = $_REQUEST[idx];
	$name = $_REQUEST[name];
	$wide = $_REQUEST[wide] * 10;
	$height = $_REQUEST[height] * 10;
	$length = $_REQUEST[length] * 10;
	$radius = $_REQUEST[radius] * 10;

	$query = "select * from `PJ_ITEMLIST` where idx='$idx' and wide='$wide' and height='$height'";
	if($name == "str"){ $query .= " and length='$length'"; }
	else{ $query .= " and radius='$radius'"; }

	$sql = $pdo -> prepare($query);
	$sql -> execute();
	$sql -> setFetchMode(PDO::FETCH_ASSOC);
	$row = $sql->fetch();

	
	print json_encode(array("quantity"=>$row[quantity],"weight"=>$row[weight]));
?>
