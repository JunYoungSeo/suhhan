<?
function cal_count($new_array,$cut_array,$counter,$set){

	if(count($new_array) == 0){
		return $cut_array;
	}else{
		
		//set standard
		$standard = (int)$set;
		// The remaining arrays
		$remainder_array = array();
		
		// 1. 기준6000에서 new_array에 첫번쨰 요소값을 빼준다.
		$remainder = $standard - (int)$new_array[0];
		// 2. 만약 기준6000에서 선택된 요소값을 빼주었는데 0으로 깔끔하게 떨어질때;
		if($remainder == 0){
			//2-1. 컷팅갯수 증감
			$counter++;
			array_push($cut_array,array($new_array[0]));
			unset($new_array[0]); // 첫번째요소 삭제하고 마무리
		}else{
			$counter++;
			array_push($cut_array,array($new_array[0])); // 컷팅배열에 넣고
			unset($new_array[0]); // 삭제해주고
			$new_array = array_values($new_array); // 배열정리해주고
			
			// 3. 0으로 안떨어질떄
			// 3-1. new_array 배열안에서 나머지값 하고 같은값이 있는지 검색한다.
			if(in_array($remainder,$new_array)){

				// 있으면 컷팅배열에 넣어준다.
				$key = array_search($remainder,$new_array);
				array_push($cut_array[$counter-1],$new_array[$key]);
				unset($new_array[$key]);
				
			}else{
				
				// 3-2. 만약 없다면
				// while문으로 돌린다 sum이 6000이 안넘을떄까지
				$sum = array_sum($cut_array[$counter-1]);
				$flag = 0; 
				$check_sum = 0; // 배열값 체크
				$check2_sum = 0; // 배열값 체크
				while(1){
					$flag++;
					
					$remainder = $standard - (int)$sum;
					for($i=0;$i<count($new_array);$i++){
						
						$tmp = $new_array[$i] - $remainder;
						if($tmp <= 0){
							// 처음나온 음수값이 가장 근사치임;;
							array_push($cut_array[$counter-1],$new_array[$i]);
							unset($new_array[$i]);
							$new_array = array_values($new_array);
							
							break;
						}	
					}
					
					$sum = array_sum($cut_array[$counter-1]);
					
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
		
		
		$new_array = array_values($new_array);
		
		
		
		return cal_count($new_array,$cut_array,$counter,$set);
	}
}
?>