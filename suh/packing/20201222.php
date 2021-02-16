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
20fit 는 5899(길이) x 2348(폭) x 2390(높이)<BR><BR>
inside경우<BR>
str 일 경우 겹쳐서 적재 하는데 wide 여유율이 + 20(20F일때),높이 여유율이 + 20<br>
hor 일 경우 겹처서 적재 하는데 wide 여유율이 + 20(20F일때),높이 여유율이 + 20<BR>
hor tee는 겹쳐서 적재 하는데 wide 여유율은 없고, 높이 여유율 + 20<BR>
hor cross 겹쳐서 적재 x wide 여유율은 x, 높이 x + 20<BR>


<?
	$container_length = 5899;
	$container_wide = 2348;
	$container_height = 2390;
?>

가상 DB 데이터 출력<BR>

<?
	$query = "select * from packing_test";
	$sql = $pdo -> prepare($query);
	$sql -> execute();
	$sql -> setFetchMode(PDO::FETCH_ASSOC);
	;
	for($i=0;$row = $sql->fetch();$i++){
		print_r($row);
		print "<br>";
	}
?>
<BR>
1. 같은 제품끼리 어떤 사양이 있는지 묶어줌 <BR>

<?
	$item_array = array();

	$item = "";
	$input_item = "";


	$query = "select * from packing_test";
	$sql = $pdo -> prepare($query);
	$sql -> execute();
	$sql -> setFetchMode(PDO::FETCH_ASSOC);
	
	for($i=0;$row = $sql->fetch();$i++){
		
		// strcmp 두 문자가 일치하는 경우 false 다를 경우 true
		$strcmp = strcmp($item,$row['item']);

		if($strcmp){
			$input_item = $row['item'];
			array_push($item_array,$row['item']);
			$item = $input_item;
		}

	
	}

	print_r($item_array); // 총 배열 !! 
	print "<br>";
?>
2. 각 각의 제품형태에 따라 내림차순으로 정렬 & 각 제품의 wide 정리<br>
<?
	for($j=0;$j<count($item_array);$j++){
		

		$type = $item_array[$j];
		${$type."_array"} = array();
		
		
		
		$query = "select * from packing_test where item = '$type' ";


		if($type == "str"){
			$query.= "order by wide desc";
		

		}else if($type == "hor"){
			$query.= "order by wide desc,radius desc";
			
		}
		
		
		$sql = $pdo -> prepare($query);
		$sql -> execute();
		$sql -> setFetchMode(PDO::FETCH_ASSOC);

		for($k=0;$row = $sql->fetch();$k++){
			print_r($row);
			print "<br>";
			array_push(${$type."_array"} ,$row['wide']);
		}
	}

	if($str_array){
		print "str 배열 = ";print_r($str_array);
		print "<br>";
	}
	
	if($hor_array){
		print "hor 배열 = ";print_r($hor_array);
		print "<br>";
	}
?>

<br><br>

3. 각 제품별 wide가 정리되었다면 wide로 최적화 1단계 시작 // 먼저 str 부터 큰 wide 순 적재 시작<br>

<?	
	
	

	$str_v_margin = 20;// wide 마진
	$str_h_margin = 160;// length 마진
	$str_height_margin = 20;

	if($str_array){

		$str_Pallet_array = array();

		for($i=0;$i<count($str_array);$i++){
			
			$search_wide = $str_array[$i];
			$query = "select * from packing_test where item = 'str' and wide = '$search_wide' ";
			$sql = $pdo -> prepare($query);
			$sql -> execute();
			$sql -> setFetchMode(PDO::FETCH_ASSOC); 
			$row = $sql->fetch();

			$item = "str";
			$wide = $row['wide'];
			$height = $row['height'];
			$quantity = $row['quantity'];
			$str_length = $row['length'];

			print log_calc($container_length,$container_wide,$container_height,$item,$wide,$height,$quantity,$str_length,"",$str_h_margin,$str_v_margin,$str_height_margin)."<br>"; // 로그용 삭제해도 무관..
			
			$calc_value = calc($container_length,$container_wide,$container_height,$item,$wide,$height,$quantity,$str_length,"",$str_h_margin,$str_v_margin,$str_height_margin);
			
			$calc_split = explode("/",$calc_value);
			
			for($j=0;$j<$calc_split[1];$j++){
				array_push($str_Pallet_array,$calc_split[0]);
			}
		}

	}
	
	print "<br>";
	print_r($str_Pallet_array);

	

	$hor_v_margin = 160;
	$hor_h_margin = 160;
	$hor_height_margin = 300; 

	/*
	if($hor_array){

		$hor_Pallet_array = array();

		for($i=0;$i<count($hor_array);$i++){
			
			$search_wide = $hor_array[$i];
			$query = "select * from packing_test where item = 'hor' and wide = '$search_wide' ";
			$sql = $pdo -> prepare($query);
			$sql -> execute();
			$sql -> setFetchMode(PDO::FETCH_ASSOC); 
			$row = $sql->fetch();

			$item = "hor";
			$wide = $row['wide'];
			$height = $row['height'];
			$quantity = $row['quantity'];
			$raidus = $row['raidus'];

			print log_calc($container_length,$container_wide,$container_height,$item,$wide,$height,$quantity,"",$raidus,$hor_h_margin,$hor_v_margin,$hor_height_margin)."<br>"; // 로그용 삭제해도 무관..
			
			$calc_value = calc($container_length,$container_wide,$container_height,$item,$wide,$height,$quantity,"",$raidus,$hor_h_margin,$hor_v_margin,$hor_height_margin);
			
			$calc_split = explode("/",$calc_value);
			
			for($j=0;$j<$calc_split[1];$j++){
				array_push($hor_Pallet_array,$calc_split[0]);
			}
		}

	}
	
	print "<br>";
	print_r($hor_Pallet_array);*/
?>

<br><br>
Container 적재 최적화
<br><br>
<?
	
	
	
		$Pallet_array = $str_Pallet_array;
		$Load_array = array();
		$counter = 0;
		$set = $container_wide;

		$wide_opt = Logistics_optimization($Pallet_array,$Load_array,$counter,$set);
		//print_r($wide_opt);
		
		for($i=0;$i<count($wide_opt);$i++){
			print_r($wide_opt[$i]);
			print "<br>";
		}
	

	 
?>


<?
function calc($cl,$cw,$ch,$item,$wide,$height,$quantity,$length,$radius,$h_margin,$v_margin,$height_margin){

	// 1번. 최대 몇개 까지 쌓을 수 있는지 계산;;
	// 2번. wide를 얼마나 차지할 지 계산;;

	if($item == "str"){
	    //str 은 wide 는 세로 , length 를 가로 로 본다.
		
		$item_h = (int)$length;//가로
		$item_h_margin = (int)$h_margin; // 가로 마진

		$item_v = (int)$wide;//세로
		$item_v_margin = (int)$v_margin; // 세로 마진

		$item_height = (int)$height;//높이
		$item_height_margin = (int)$height_margin; // 높이 마진

		$item_quantity = (int)$quantity; // 수량

		// 1 :: ( 컨테이너 높이 - 높이마진 ) / 높이
		$max_height_count = floor(($ch - 300) / ($item_height + $item_height_margin)); 
		
		// 2 :: 수량 / $max_height_count ( 최대적재높이갯수 )
		$total_wide_count = floor($item_quantity / $max_height_count);
		$total_wide_count_remainder_ea = $item_quantity - ($max_height_count * $total_wide_count); // 총 갯수 - (최대적재갯수 * 총 차지하는 갯수) = 나머지 갯수 
		

		//return "기준 wide = ".$item_v." / 아이템 총 갯수 = ".$item_quantity." / 최대적재높이갯수 = ".$max_height_count." / 총 wide 차지갯수 = ".$total_wide_count." / 총 wide 차지갯수 제외 나머지 갯수 = ".$total_wide_count_remainder_ea;
		return $wide+$item_v_margin."/".$total_wide_count;

	}else if($item == "hor"){
		
		

	}

}

?>

<?
function log_calc($cl,$cw,$ch,$item,$wide,$height,$quantity,$length,$radius,$h_margin,$v_margin,$height_margin){

	// 1번. 최대 몇개 까지 쌓을 수 있는지 계산;;
	// 2번. wide를 얼마나 차지할 지 계산;;

	if($item == "str"){
	    //str 은 wide 는 세로 , length 를 가로 로 본다.
		
		$item_h = (int)$length;//가로
		$item_h_margin = (int)$h_margin; // 가로 마진

		$item_v = (int)$wide;//세로
		$item_v_margin = (int)$v_margin; // 세로 마진

		$item_height = (int)$height;//높이
		$item_height_margin = (int)$height_margin; // 높이 마진

		$item_quantity = (int)$quantity; // 수량

		// 1 :: ( 컨테이너 높이 - 높이마진 ) / 높이
		$max_height_count = floor(($ch - $item_height_margin) / $item_height); 
		
		// 2 :: 수량 / $max_height_count ( 최대적재높이갯수 )
		$total_wide_count = floor($item_quantity / $max_height_count);
		$total_wide_count_remainder_ea = $item_quantity - ($max_height_count * $total_wide_count); // 총 갯수 - (최대적재갯수 * 총 차지하는 갯수) = 나머지 갯수 
		
		return "기준 wide = ".$item_v." / 아이템 총 갯수 = ".$item_quantity." / 최대적재높이갯수 = ".$max_height_count." / 총 wide 차지갯수 = ".$total_wide_count." / 총 wide 차지갯수 제외 나머지 갯수 = ".$total_wide_count_remainder_ea;

	}else if($item == "hor"){
		

	}

}

?>


<?
function Logistics_optimization($Pallet_array,$Load_array,$counter,$set){

	if(count($Pallet_array) == 0){
		return $Load_array;
	}else{
		
		//set Container_length
		$Container_length = (int)$set;
		// The remaining arrays
		$remainder_array = array();
		// 1. 컨테이너 길이에서 Pallet_array에 첫번쨰 요소값을 빼준다.
		
		$remainder = $Container_length - (int)$Pallet_array[0];
		
		// 2. 만약 컨테이너 길이에서 선택된 요소값을 빼주었는데 0으로 깔끔하게 떨어질때;
		if($remainder == 0){
			//2-1. 컷팅갯수 증감
			$counter++;
			array_push($Load_array,array($Pallet_array[0]));
			unset($Pallet_array[0]); // 첫번째요소 삭제하고 마무리
		
		}else{
			//2-1. 컷팅갯수 증감
			$counter++;
			array_push($Load_array,array($Pallet_array[0])); // 적재배열에 넣고
			unset($Pallet_array[0]); // 삭제해주고
			$Pallet_array = array_values($Pallet_array); // 배열정리해주고
			
			// 3. 0으로 안떨어질떄
			// 3-1. Pallet_array 배열안에서 나머지값 하고 같은값이 있는지 검색한다.
			if(in_array($remainder,$Pallet_array)){

				// 있으면 적재배열에 넣어준다.
				$key = array_search($remainder,$Pallet_array);
				array_push($Load_array[$counter-1],$Pallet_array[$key]);
				unset($Pallet_array[$key]);
				
			}else{
				
				// 3-2. 만약 없다면
				// while문으로 돌린다 sum이 Container_length 안넘을떄까지
				$sum = array_sum($Load_array[$counter-1]);
				$flag = 0; 
				$check_sum = 0; // 배열값 체크
				$check2_sum = 0; // 배열값 체크
				while(1){
					$flag++;
					
					$remainder = $Container_length - (int)$sum;
					for($i=0;$i<count($Pallet_array);$i++){
						
						$tmp = $Pallet_array[$i] - $remainder;
						if($tmp <= 0){
							// 처음나온 음수값이 가장 근사치임;;
							array_push($Load_array[$counter-1],$Pallet_array[$i]);
							unset($Pallet_array[$i]);
							$Pallet_array = array_values($Pallet_array);
							
							break;
						}	
					}
					
					$sum = array_sum($Load_array[$counter-1]);
					
					if($flag == 1){ // flag 가 첫번쨰때 배열의 합
						$check_sum = $sum;
					}else{ // flag 가 첫번째 이외 일때 배열의합
						$check2_sum = $sum;
					}
					
					
					// 첫번째 배열의합과 두번쨰 배열의합이 같다는 것은 더이상 근사치를 찾을수 없다는뜻..
					if($check_sum == $check2_sum){ 
						break; // while stop;
					}else{
					// 첫번째 배열의합과 두번째 배열의합이 같지 않을때는 위쪽 for문 다시 실행시켜주고, check_sum의 값을 바꿔준다.
						$check_sum = $check2_sum;
					}
				}
				
			}

		}
		
		
		$Pallet_array = array_values($Pallet_array);
		
		
		return Logistics_optimization($Pallet_array,$Load_array,$counter,$set);
	}
}
?>


