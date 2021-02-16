1. 가로 기준으로 먼저 정렬<br>
2. 세로 기준으로 정렬<br>
3. 높이??<br>
4. cbm을 계산해서 최적인 것을 선택??<br>
5. 20fit 는 5899(길이) x 2348(폭) x 2390(높이)<BR>
6. 길이가 5899 이상인 ex) ladder str 6L 들어오면 STR만 40FIT로 가고 나머진 20FIT 인가 아니면 전부 40FIT로 고정해야 하는가<br><br>
7. wide 가 같은거 끼리 먼저 묶을까?<br>
- 여기서 세로 ,가로는  컨테이너를 옆면으로 보았을 때 !!<br>
8. str = 세로 x 가로 ( wide x length )<br>
9. hori elbow = 가로 x 세로 ( wide + radius x radius + wide )<br>
10. hori tee = 가로 x 세로 ( radius + wide + radius x radius + wide )<br>
11. hori cross = 가로 x 세로 ( radius + wide + radius x radius + wide + radius )<br>
<br><br>
str = 300x3000,600x3000,900x3000<br>
hori elbow = 600(wide(300)+radius(300)) x 600, 900(wide(600)+radius(300)) x 900, 1050(wide(450)+radius(600)) x 1050<br>
hori tee = 900(radius(300)+wide(300)+radius(300)) x 600(radius(300) + wide(300), 1200(radius(300)+wide(600)+radius(300)) x 900(radius(300) + wide(600)<br>
hori cross = 900(radius(300)+wide(300)+radius(300)) x 900(radius(300)+wide(300)+radius(300))<br>
= "300x3000","600x3000","900x3000","600x600","900x900","1050x1050","900x600","1200x900","900x900"<br>
<br><br>
<?
	$Pallet_array = array("310x3010","610x3010","910x3010","610x610","910x910","1060x1060","910x610","1210x910","910x910");
	$Pallet_h_array = array();
	$Pallet_v_array = array();

	for($i=0;$i<count($Pallet_array);$i++){
		$temp = explode("x",$Pallet_array[$i]);
		array_push($Pallet_h_array,$temp[1]);
		array_push($Pallet_v_array,$temp[0]);
	}

	print "가로 = ";print_r($Pallet_h_array);print "<br>";
	print "세로 = ";print_r($Pallet_v_array);print "<br>";
	
	$unique_Pallet_v_array = array_unique($Pallet_v_array);
	print "세로 중복 제거 = ";print_r($unique_Pallet_v_array);print "<br>";


	
	
	
	

	

	 
?>

12. 세로 중복 제거 후 최적의 wide 값 먼저 찾는다.<br>

<?
	$Load_array = array();
	$counter = 0;
	$set = 2348;
	$all_count = count(Logistics_optimization($unique_Pallet_v_array,$Load_array,$counter,$set));
	$values = Logistics_optimization($unique_Pallet_v_array,$Load_array,$counter,$set);
	print_r($values);print "<br>";

	
	

?>


<?for($i=0;$i<$all_count;$i++){?>
<div style="height:234.8px;width:589.9px;border:1px solid black;padding:10px 10px;">
<?for($j=0;$j<count($values[$i]);$j++){?>
<div style="height:<?=$values[$i][$j]/10?>px;width:<?=$values[$i][$j]/10?>px;background-color:black;margin-bottom:5px;"></div>
<?}?>
</div>
<br>
<?}?>

13. 최적의 wide 값을 찾았으면 해당 wide 끼리 묶는다.<br>

<?
	$level_1_Logistics_optimization_result = Logistics_optimization($unique_Pallet_v_array,$Load_array,$counter,$set);

	for($j=0;$j<count($level_1_Logistics_optimization_result);$j++){
		$temp = $level_1_Logistics_optimization_result[$j];
		for($k=0;$k<count($temp);$k++){
			print $temp[$k]."<br>";
			$finder_key = (string)$temp[$k];
			print_r(array_keys($Pallet_v_array,$finder_key));
			
		}
	}
?>
<br><br><br>

14. wide 묶은 것을 기준으로 컨테이너 길이 만큼 최적화를 구해준다.<br>

<a href="https://m.blog.naver.com/tradlinx0522/221807488358" target="_black"> 적재를 cbm 으로 계산


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


