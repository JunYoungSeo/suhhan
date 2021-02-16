<?
	function cal_cbm($item,$wide,$height,$length,$flange,$elbow_x,$elbow_y,$elbow_rotate,$quantity){
		$fitting = 125;

		if($height == "100"){
			$max_height_ea = 15;
		}else if($height == "150"){
			$max_height_ea = 10;
		}

		// cbm 은 가로 x 세로 x 높이  단위는 m 이다;;	
		$_horizontal = 0;
		$_vertical = 0;
		$_height = 0;
		
		// max_product_ea = max_cp * max_height_ea * 2 ;; 최대1단적재갯수 * 최대높이적재갯수 * 2 ( 서한은 2개 겹침적재이기 때문에 )
		// quantity = max_cp * max_height_ea * 2 ==> max_cp = quantity / 2 / max_height_ea
		$max_cp =  $quantity / $max_height_ea / 2;

		if($item == "str"){

			// 가로길이 = 최대1단적재갯수 * (wide+flange) + 200 ( 200은 파레트여유공간)
			$_horizontal = $max_cp * ( $wide + $flange ) + 200;
			// 세로길이 = 제품 총 길이 + 200( 200은 파레트 여유공간)
			$_vertical = $length + 200;
			// 높이는 height * max_height_ea
			$_height =  $heigh * $max_height_ea;
			
		}else if($item == "hor"){

			if($elbow_rotate == "on"){
				$_horizontal = $elbow_x;
				$_vertical = $elbow_y;
			}else{
				$_horizontal = $elbow_y;
				$_vertical = $elbow_x;
			}

			
			$_height =  $height * $max_height_ea;


		}

		$cbm = ( $_horizontal * $_vertical * $_height ) / 1000000000;
		
		//return $wide;
		return number_format($cbm,2);
	}
?>