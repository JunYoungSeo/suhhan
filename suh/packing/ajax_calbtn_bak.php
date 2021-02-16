<?include_once("../include/common.php")?>


<?	
	$idx = $_REQUEST['idx']; 
	$fit = $_REQUEST['container_fit'];
	$lm = $_REQUEST['lm'];
	$h_margin = $_REQUEST['h_margin'];
	$v_margin = $_REQUEST['v_margin'];
	$height_margin = $_REQUEST['height_margin'];
	
	$db_name = "`packing__condition`";
	

	$QUERY = "DELETE FROM $db_name WHERE idx = '$idx'";
	$SQL = $pdo->prepare($QUERY);
	$SQL -> execute();


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
	$query = "select * from `PJ_COST_ANALYSIS` as A join `PJ_ITEMLIST` as B on A.idx = B.idx where A.idx = '21010515512500' and A.ITEMLIST = 'str' order by A.wide desc";
	$sql = $pdo -> prepare($query);
	$sql -> execute();
	$sql -> setFetchMode(PDO::FETCH_ASSOC);
	for($i=0;$row = $sql->fetch();$i++){
		
		
		$weight = weight($row['TYPE'],$row['ITEMLIST'],$row['SIDERAIL_INOUT'],$row['WIDE'],$row['HEIGHT'],$row['STR_LENGTH'],$row['RADIUS'],$row['DEG'],$row['SIDERAIL_THICK'],$row['RUNG_THICK'],$row['SPACE'],$row['QUANTITY']);
		print $weight;
		//$data = calc($row['QUANTITY'],$cw,$ch,$cl,$row['ITEMLIST'],$row['WIDE'],$row['HEIGHT'],$row['LENGTH'],20,$row['RADIUS'],$row['DEG']);
		//print $data;
		//print "<br>";
	}
?>


<?
	function weight($TYPE,$ITEMLIST,$SIDERAIL_INOUT,$WIDE,$HEIGHT,$STR_LENGTH,$RADIUS,$DEG,$SIDERAIL_THICK,$RUNG_THICK,$SPACE,$QUANTITY){
		global $pdo,$idx,$SEQ;
		/**************************************************************************************************************************************************
			***************************************************************************************************************************************************
			                               1. 사이드레일 타입을 통한 렁 타입,F1,F2,RF,재질,비중 가져오기
			***************************************************************************************************************************************************
			***************************************************************************************************************************************************/
			
			$F1 = 32;
			$F2 = 13;
			$RF = 0;
			
			$MATERIAL = "steel";
			
			switch($MATERIAL){
				case "steel" : $VIJUNG = 7.85 / 1000000; break;
				case "sus" : $VIJUNG = 7.98 / 1000000; break;
				case "al" : $VIJUNG = 2.69 / 1000000; break;
				case "pl" : $VIJUNG = 1.5 / 1000000; break;
				default: $VIJUNG = 1;break;
			}
			
			//print $VIJUNG."<BR>";
			// A,B,C 로 나뉘어져야 하고.. STEEL+IN = A 이고, SUS+IN = B 이고, STEEL,SUS+OUT = C 이다.	
			$SIDERAIL_INOUT = strtoupper($SIDERAIL_INOUT);
			
			IF($SIDERAIL_INOUT == "IN"){
				
				IF($MATERIAL == "steel"){

					$RUNG_TYPE = "A";

				}ELSE IF($MATERIAL == "sus"){
					
					$RUNG_TYPE = "B";
				
				}

			}ELSE IF($SIDERAIL_INOUT == "OUT"){
				
				$RUNG_TYPE = "C";

			}


		
			/**************************************************************************************************************************************************
			***************************************************************************************************************************************************
			                               2. 중량 구하기
			***************************************************************************************************************************************************
			***************************************************************************************************************************************************/
			
			$SIDERAIL_WEIGHT = side_weight($ITEMLIST,$SIDERAIL_THICK,$WIDE,$SPACE,$STR_LENGTH,$RADIUS,$reducer_wide,$DEG,$HEIGHT,$F1,$F2,$RF);
			
			if($TYPE == "LADDER"){
				$OTHER_WEIGHT = rung_weight($ITEMLIST,$RUNG_TYPE,$RUNG_THICK,$WIDE,$SPACE,$STR_LENGTH,$RADIUS,$reducer_wide,$DEG,$HEIGHT);
			}else{
				$OTHER_WEIGHT = bottom_weight($ITEMLIST,$SIDERAIL_THICK,$WIDE,$SPACE,$STR_LENGTH,$RADIUS,$reducer_wide,$DEG,$F1);
			}

			$WEIGHT = ( $SIDERAIL_WEIGHT + $OTHER_WEIGHT ) * $VIJUNG;

			$WEIGHT = round($WEIGHT,2);
			$ALL_WEIGHT = round($WEIGHT * $QUANTITY,2);

			return $WEIGHT;

?>



<?
	function calc($quantity,$cw,$ch,$cl,$tray_type,$tray_w,$tray_h,$tray_l,$tray_f,$tray_r,$tray_d){
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
		
		return $cw."/".$ch."/".$cl."/".$tray_type."/".$tray_w."/".$tray_h."/".$max_wide_stack."/".$max_height_stack."/".$max_length_stack."/".$max_total_ea."/".$full_container_ea."/".$reminder_product_ea; 
	}

	
?>