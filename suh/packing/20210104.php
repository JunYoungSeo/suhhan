포장사이즈 적용 기준<br>
L/T TYPE<BR>
100H = 15단(30EA)<BR>
150H = 10단(20EA)<BR>

<br><br>

20FT 내부 사이즈  W : 2,352 H : 2,390 L : 5,898<BR>
40FT 내부 사이즈  W : 2,352 H : 2,390 L : 12,032<BR>
4000kg 이하로 법이 되어있음.. <br>
<BR><BR>
<?
try{
  // MySQL PDO 객체 생성
  // mysql을 다른 DB로 변경하면 다른 DB도 사용 가능
  $pdo = new PDO('mysql:host=localhost;dbname=sh1920_godohosting_com;charset=utf8', 'sh1920', 'suhhani1920!');
  // 에러 출력
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch(Exception $e) {
  echo$e->getMessage();
}
?>


<?
	$cft = "20"; // 컨테이너 ft
	
	if($cft == "20"){
		$cw = 2352;
		$ch = 2390;
		$cl = 5898;
	}else if($cft == "40"){
		$cw = 2352;
		$ch = 2390;
		$cl = 12032;
	}

?>



<?
	$query = "select * from `PJ_COST_ANALYSIS` where idx = '21010515512500' and ITEMLIST = 'str' order by wide desc";
	$sql = $pdo -> prepare($query);
	$sql -> execute();
	$sql -> setFetchMode(PDO::FETCH_ASSOC);
	for($i=0;$row = $sql->fetch();$i++){
		
		print calc($row['QUANTITY'],$cw,$ch,$cl,$row['ITEMLIST'],$row['WIDE'],$row['HEIGHT'],$row['LENGTH'],20,$row['RADIUS'],$row['DEG']);
		print "<br>";
		
	}
?>




<?
	function calc($quantity,$cw,$ch,$cl,$tray_type,$tray_w,$tray_h,$tray_l,$tray_f,$tray_r,$tray_d){
		global $pdo;

		$product_wm = 160; // 제품 외형 wide 마진
		$product_hm = 300; // 제품 외형 height 마진
		$product_lm = 160; // 제품 외형 length 마진

		/**
		*	quantity = 제품 총 갯수
		*	cw = 컨테이너 wide
		*	ch = 컨테이너 height
		*	cl = 컨테이너 length
		*	tray_type = 트레이 종류(str,hor,hortee,horcro
		*	tray_w = tray wide
		*	tray_h = tray height
		*	tray_f = tray flange
		*	tray_r = tray radius
		*	tray_d = tray degree
		*
		**/
		
		if($tray_type == "str"){
			$add_query = " and length = '$tray_l' ";
		}else if($tray_type == "hor"){
			$add_query = " and radius = '$tray_r' and deg = '$tray_d' ";
		}
		
		$db_name = "`packing__db`";
		$query = "select * from $db_name where tray = '$tray_type' and wide = '$tray_w' and height = '$tray_h' and flange = '$tray_f' ";
		$query .= $add_query;
		$sql = $pdo -> prepare($query);
		$sql -> execute();
		$row = $sql->fetch();

		$max_wide_stack = $row['max_wide_stack'] * 1; // 컨테이너 wide 에서 최대로 들어 갈 수 있는 갯수;
		$max_height_stack = $row['max_height_stack'] * 1; // 컨테이너 wide 에서 최대로 들어 갈 수 있는 갯수;
		$max_length_stack = $row['max_length_stack'] * 1; // 컨테이너 wide 에서 최대로 들어 갈 수 있는 갯수;

		$max_total_ea = $max_wide_stack * 2 * $max_height_stack; // 해당 wide별 컨테이너의 최대적재수량
		$full_container_ea = floor($quantity / $max_total_ea); // 소수점 버림으로 가득 찬 컨테이너 갯수만 구함;
		$reminder_product_ea = $quantity - ( $full_container_ea * $max_total_ea); // 나머지 제품 갯수
		
		return $cw."/".$ch."/".$cl."/".$tray_type."/".$tray_w."/".$tray_h."/".$max_wide_stack."/".$max_height_stack."/".$max_length_stack."/".$max_total_ea."/".$full_container_ea."/".$reminder_product_ea; 
	}
?>