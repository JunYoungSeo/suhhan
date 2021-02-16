<?include_once("../include/common.php")?>
<?include_once("./weight.php")?>
<?include_once("./calc_cutting.php")?>
<?include_once("./calc_cbm.php")?>


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
		//$cl = 12032;
		$cl = 11800;
	}

	$flange = 20;

?>

<?
	$length_arr = array();
?>

<?	


	$query = "select * from `PJ_ITEMLIST` where idx = '$idx' and  ITEMLIST = 'S' order by wide desc";
	$sql = $pdo -> prepare($query);
	$sql -> execute();
	$sql -> setFetchMode(PDO::FETCH_ASSOC);
	for($i=0;$row = $sql->fetch();$i++){
		
		$item = "str";

		
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


		$weight = weight($row['TYPE'],$item,$siderail_io,$wide,$height,$length,$radius,$deg,$siderail_thick,$rung_thick,$space,$quantity);
		
		
		if($height == "100"){
			$max_height_ea = 15;
		}else if($height == "150"){
			$max_height_ea = 10;
		}
		
		$_cw = $cw - $h_margin; // 컨테이너 wide 크기
		$pw = $wide + $flange;
		$max_cp = floor($_cw/$pw); // 컨테이너 wide 크기에서 product wide가 최대 몇개가 들어갈수 있을까 소수점 버림 ;; 이러면 1단의 최대적재 갯수 나옴
		
		

		$max_product_ea = $max_cp * $max_height_ea * 2; // 2를 곱하는 이유는 지금 서한이 겹쳐서 적재하기 때문..
		
		$full_package = floor($quantity / $max_product_ea );
		$reminder_ea = $quantity - ( $full_package * $max_product_ea );

		// $full_package = 완 box 갯수
		// reminder_ea = 제품 총 갯수 - ( 완box 갯수 * 제품 적재가능 최대 갯수 )
		
		for($i = 0; $i < $full_package ; $i++){
			
		
		$full_package_length = $length + 200;
		
		
		array_push($length_arr,$full_package_length."/".$item."/".$wide."/".$height."/".$length."/".$max_product_ea."/".$siderail_thick."/".$weight."/".$radius."/".$deg."/0/0");
		
		}
		

	}

	
	$query = "select * from `PJ_ITEMLIST` where idx = '$idx' and   ITEMLIST = 'H' order by wide desc,radius desc";
	
	$sql = $pdo -> prepare($query);
	$sql -> execute();
	$sql -> setFetchMode(PDO::FETCH_ASSOC);
	for($i=0;$row = $sql->fetch();$i++){
		
		$item = "hor";

		
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


		$query2 = "select * from `packing__elbowsize` where radius = '$radius' and wide = '$wide' and deg = '$deg' ";
		$sql2 = $pdo -> prepare($query2);
		$sql2 -> execute();
		$sql2 -> setFetchMode(PDO::FETCH_ASSOC);
		$row2 = $sql2->fetch();

		$elbow_x = $row2['x']*1;
		$elbow_y = $row2['y']*1;

		$weight = weight($row['TYPE'],$item,$siderail_io,$wide,$height,$length,$radius,$deg,$siderail_thick,$rung_thick,$space,$quantity);
		
			
		if($height == "100"){
			$max_height_ea = 15;
		}else if($height == "150"){
			$max_height_ea = 10;
		}
		
		$_cw = $cw - $h_margin; // 컨테이너 wide 크기
		
		$pw = $elbow_x + $flange;
		$max_cp = floor($_cw/$pw); // 컨테이너 wide 크기에서 product wide가 최대 몇개가 들어갈수 있을까 소수점 버림 ;; 이러면 1단의 최대적재 갯수 나옴


		$max_product_ea = $max_cp * $max_height_ea * 2; // 2를 곱하는 이유는 지금 서한이 겹쳐서 적재하기 때문..
		
		
		$full_package = floor($quantity / $max_product_ea );
		$reminder_ea = $quantity - ( $full_package * $max_product_ea );

		//$full_package = 완 box 갯수
		//reminder_ea = 제품 총 갯수 - ( 완box 갯수 * 제품 적재가능 최대 갯수 )
		
		
		for($i = 0; $i < $full_package ; $i++){
			

		$full_package_length = $elbow_y + 200;		
	

		array_push($length_arr,$full_package_length."/".$item."/".$wide."/".$height."/".$length."/".$max_product_ea."/".$siderail_thick."/".$weight."/".$radius."/".$deg."/".$elbow_x."/".$elbow_y);
		
		}
		

	}

	
	$cut_array = array();
	$counter = 0;
	$set = $cl - $v_margin;

	$cutting = cal_count($length_arr,$cut_array,$counter,$set);

	$need_full_package_container_ea = count($cutting); // 완box 에 대해 총 필요한 컨테이너 갯수
	
	for($j = 0;$j < $need_full_package_container_ea ; $j++){

		$container_no = $j + 1;

		for($k = 0; $k < count($cutting[$j]); $k++){
				
			$detail_container_list = $cutting[$j][$k];

			$data = explode("/",$detail_container_list);
			
			$item = $data[1];
			$wide = $data[2];
			$height = $data[3];
			$length = $data[4];
			$quantity = $data[5];
			$thick = $data[6];
			$weight = $data[7];
			$radius = $data[8];
			$deg = $data[9];
			$elbow_x = $data[10];
			$elbow_y = $data[11];

			$total_weight = $weight * $quantity;

			$cbm = cal_cbm($item,$wide,$height,$length,$radius,$deg,$flange,$thick);
			
			$query = "insert into `packing__full_container` set 
								idx = '$idx',
								page = '$container_no',
								item = '$item',
								wide = '$wide',
								height = '$height',
								length = '$length',
								radius = '$radius',
								deg = '$deg',
								quantity = '$quantity',
								weight = '$weight',
								total_weight = '$total_weight',
								cbm = '$cbm',
								elbow_x = '$elbow_x',
								elbow_y = '$elbow_y' ";

			$pdo->exec($query);

			


		}
	}

	
	
?>