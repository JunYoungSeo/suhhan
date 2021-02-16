1. 가로 기준으로 먼저 정렬<br>
2. 세로 기준으로 정렬<br>
3. 높이??<br>
4. cbm을 계산해서 최적인 것을 선택??<br>
5. 20fit 는 5899(길이) x 2348(폭) x 2390(높이)<BR>
6. 길이가 5899 이상인 ex) ladder str 6L 들어오면 STR만 40FIT로 가고 나머진 20FIT 인가 아니면 전부 40FIT로 고정해야 하는가
<br><br>
"600x3000","600x3000","300x500","300x700","200x400","400x300"
<br><br>
<?
	$Pallet_array = array("600x3000","600x3000","300x500","300x700","200x400","400x300");
	$Load_array = array();
	$counter = 0;
	$set = 5899;
	
	$all_count = count(Logistics_optimization($Pallet_array,$Load_array,$counter,$set));
	$values = Logistics_optimization($Pallet_array,$Load_array,$counter,$set);
	print_r(Logistics_optimization($Pallet_array,$Load_array,$counter,$set));

	 
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

<br><br><br>


<?for($i=0;$i<$all_count;$i++){?>
<div style="height:234.8px;width:589.9px;border:1px solid black;padding:10px 10px;">
<?for($j=0;$j<count($values[$i]);$j++){?>
<div style="height:10px;width:<?=$values[$i][$j]/10?>px;background-color:black;float:left;margin-right:2px;"></div>
<?}?>
</div>
<br>
<?}?>
