<?include_once("../include/common.php")?>
<?include_once("./weight.php")?>


<?	
	$idx = $_REQUEST['idx']; 
	$fit = $_REQUEST['container_fit'];
	$lm = $_REQUEST['lm'];
	$h_margin = $_REQUEST['h_margin'];
	$v_margin = $_REQUEST['v_margin'];
	$height_margin = $_REQUEST['height_margin'];
	
	$cleardb = array("`packing__condition`","`packing__full_container`");
	
	for($j=0;$j<count($cleardb);$j++){

	$del_ = "DELETE FROM $cleardb[$j] WHERE idx = '$idx' ";
	$pdo->exec($del_);
			
			
	}
	
	$db_name = "`packing__condition`";

	$QUERY = "INSERT INTO $db_name SET ";
	
	$QUERY .= "  idx = '$idx'
				,fit = '$fit'
				,lm = '$lm'
				,h_margin = '$h_margin'
				,v_margin = '$v_margin'
				,height_margin = '$height_margin' ";
				
	$SQL = $pdo->prepare($QUERY);
	$SQL -> execute();

?>



<?
	
	if($fit == "20"){
		$cw = 2352;
		$ch = 2390;
		$cl = 5898;
	}else if($fit == "40"){
		$cw = 2352;
		$ch = 2390;
		$cl = 12032;
	}

?>



<?	

	$page = 0;

	$query = "select * from `PJ_ITEMLIST` where idx = '$idx' and ITEMLIST = 'S' order by wide desc";
	$sql = $pdo -> prepare($query);
	$sql -> execute();
	$sql -> setFetchMode(PDO::FETCH_ASSOC);
	for($i=0;$row = $sql->fetch();$i++){
		
		switch($row['ITEMLIST']){
			case "S" : $item = "str";break;
		}

		
		$siderail_io = $row['SIDERAIL_INOUT'];
		$wide = $row['WIDE'];
		$height = $row['HEIGHT'];
		$length = $row['STR_LENGTH'];
		$radius = $row['RADIUS'];
		$deg = $row['DEG'];
		$siderail_thick = $row['SIDERAIL_THICK'];
		$rung_thick = $row['RUNG_THICK'];
		$space = $row['SPACE'];
		$quantity = $row['QUANTITY'];

		$cbm = 0;

		$weight = weight($row['TYPE'],$item,$siderail_io,$wide,$height,$length,$radius,$deg,$siderail_thick,$rung_thick,$space,$quantity);
		
		
		$calc = calc($row['QUANTITY'],$cw,$ch,$cl,$item,$wide,$height,$length,20,$radius,$deg,$weight);
		
		
		$calc = explode("/",$calc);
		
		$calc_max_total_ea = $calc[0];
		$calc_full_container_ea = $calc[1];
		$calc_reminder_product_ea = $calc[2];

		$total_weight = $calc_max_total_ea * $weight;
		
		if($calc_full_container_ea > 0){
			
			$db_name = "`packing__full_container`";
			
			for($i=0;$i<$calc_full_container_ea;$i++){
				
				$page++;

				

				$query = "insert into $db_name set 
								idx = '$idx',
								page = '$page',
								item = '$item',
								wide = '$wide',
								height = '$height',
								length = '$length',
								radius = '$radius',
								deg = '$deg',
								quantity = '$calc_max_total_ea',
								weight = '$weight',
								total_weight = '$total_weight',
								cbm = '$cbm' ";

				$pdo->exec($query);

			}

		}

		
		
	}
?>



<?
	function calc($quantity,$cw,$ch,$cl,$tray_type,$tray_w,$tray_h,$tray_l,$tray_f,$tray_r,$tray_d,$weight){
		global $pdo,$h_margin,$v_margin,$height_margin;

		$product_wm = $h_margin; // 제품 외형 wide 마진
		$product_hm = $height_margin; // 제품 외형 height 마진
		$product_lm = $v_margin; // 제품 외형 length 마진

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
		
		return $max_total_ea."/".$full_container_ea."/".$reminder_product_ea;
	}

	
?>