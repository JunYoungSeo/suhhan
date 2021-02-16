<?
	function weight($TYPE,$ITEMLIST,$SIDERAIL_INOUT,$WIDE,$HEIGHT,$STR_LENGTH,$RADIUS,$DEG,$SIDERAIL_THICK,$RUNG_THICK,$SPACE,$QUANTITY){

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


		$SIDERAIL_WEIGHT = side_weight($ITEMLIST,$SIDERAIL_THICK,$WIDE,$SPACE,$STR_LENGTH,$RADIUS,"",$DEG,$HEIGHT,$F1,$F2,$RF);
		$OTHER_WEIGHT = rung_weight($ITEMLIST,$RUNG_TYPE,$RUNG_THICK,$WIDE,$SPACE,$STR_LENGTH,$RADIUS,$reducer_wide,$DEG,$HEIGHT);
		
		$WEIGHT = ( $SIDERAIL_WEIGHT + $OTHER_WEIGHT ) * $VIJUNG;

		$WEIGHT = round($WEIGHT,2);

		return $WEIGHT;


	}
?>



<?

function arc($R, $deg){
		return 2 * 3.14 * $R * ($deg/360);
	}


function side_weight($shape, $siderail_thickness, $wide, $space, $length, $radius, $reducer_wide, $deg, $height, $f1, $f2, $rf){
		//print $shape."<br>";
		//print $siderail_thickness."<br>";
		//print $wide."<br>";
		//print $space."<br>";
		//print $length."<br>";
		//print $radius."<br>";
		//print $reducer_wide."<br>";
		//print $deg."<br>";
		//print $height."<br>";
		//print $f1."<br>";
		//print $f2."<br>";
		//print $rf."<br>";
		$x = 1;//접힌횟수
		
		if($shape == 'str'){
			if($f1 != 0){ $x = $x+1; }
			if($f2 != 0){ $x = $x+1; }
			if($rf != 0){ $x = $x+1; }
			$side_H = $siderail_thickness * (( ($f1*2) + $f2 + $rf + $height ) - ($siderail_thickness * $x));
		}else{
			if($f1 != 0){ $x = $x+1; }
			$side_H = $siderail_thickness * (( ($f1*2) + $height ) - ($siderail_thickness * $x));
		}

		if($shape == 'str'){
			$side_weight = $side_H * $length * 2;
		}else if($shape == 'hor'){
			$side1 = arc($radius,$deg) + $space;        // 1. 안쪽 사이드레일
			$side2 = arc($radius+$wide,$deg) + $space;  // 2. 바깥쪽 사이드레일
			$side_weight = $side_H * ($side1+$side2);
		}else if($shape == 'ver_in' || $shape == 'ver_out'){
			$side1 = sector_area($radius+$height,$deg) - sector_area($radius,$deg);   // 1. 스트레이트부분 빼고 넓이 구하기
			$side2 = $space * $height;                                                // 2. 스트레이트 부분 넓이 구하기
			$side3 = $f1 * (arc($radius+$height,$deg) + $space);                      // 3. 긴쪽 띠 구하기
			$side4 = $f1 * (arc($radius,$deg) + $space);                              // 4. 짧은쪽 띠 구하기
			$side_weight = $siderail_thickness * ($side1+$side2+$side3+$side4) * 2;
		}else if($shape == 'hortee'){
			$side1 = ($radius + $wide + $radius + $space);      // 1. 직선 사이드레일 중량 구하기
			$side2 = (arc($radius,90)+$space) * 2;            // 2. 엘보 사이드레일 중량 구하기
			$side_weight = $side_H * ($side1+$side2);
		}else if($shape == 'horcro'){
			$side_weight = $side_H * (arc($radius,90)+$space) * 4;
		}else if($shape == 'vertee_up' || $shape == 'vertee_down'){
			$side1 = ( $radius + $height + $radius ) * ( $radius + $height ) - sector_area($radius,90); // 1. 스트레이트 부분을 제외한 사이드레일 중량 구하기
			$side2 = ( $space * 0.5 ) * $height * 3;                                                      // 2. 스트레이트 부분 사이드레일 중량 구하기
			$side3 = $f1 * ($radius + $height + $radius + $space);                                        // 3. 띠 부분 구하기 ( 직선 띠 )
			$side4 = $f1 * (arc($radius,90)+$space) * 2;                                                // 4. 띠 부분 구하기 (엘보 띠 )
			$side_weight = $siderail_thickness * ($side1+$side2+$side3+$side4) * 2;
		}else if($shape == 'reducer-str'){
			$side1 = ($space * 0.5);
			$side2 = sqrt(pow($space,2) + pow(($wide-$reducer_wide) / 2,2));
			$side3 = ($space * 0.5);
			$side_weight = $side_H * ($side1+$side2+$side3) * 2;
		}else{
			$side1 = ($space * 0.5 + $space + $space * 0.5);                    // 1. 직선 사이드레일 ( 125 + space + 125 = 직선 사이드레일 길이)
			$side2 = ($space * 0.5);                                            // 2. 꺽힌 사이드레일
			$side3 = sqrt(pow($space,2) + pow(($wide-$reducer_wide) / 2,2));
			$side4 = ($space * 0.5);
			$side_weight = $side_H * ($side1+$side2+$side3+$side4);
		}
		
		return $side_weight;
	}



	function rung_weight($shape, $rshape, $rung_thickness, $wide, $space, $length, $radius, $reducer_wide, $deg, $height){
		
		if($rshape == 'A'){
			$rung_H = $rung_thickness * ((28+16+16+8+8) - ($rung_thickness*4));
		}else if($rshape == 'B'){
			$rung_H = $rung_thickness * ((40+18+18+8+8) - ($rung_thickness*4));
		}else if($rshape == 'C'){
			$rung_H = $rung_thickness * ((40+20+20) - ($rung_thickness*2));
		}	
		 

		if($shape == 'str'){
			$rung_length = $length;
			$rung_weight = $wide * ((($rung_length - $space)/$space) + 1);
		}else if($shape == 'hor'){
			$rung_length = arc($radius + $wide, $deg) + $space;
			$rung_weight = $wide * ((($rung_length - $space)/$space) + 1);
		}else if($shape == 'ver_in'){
			$rung_length = arc($radius + $height, $deg) + $space;
			$rung_weight = $wide * ((($rung_length - $space)/$space) + 1);
		}else if($shape == 'ver_out'){
			$rung_length = arc($radius, $deg) + $space;
			$rung_weight = $wide * ((($rung_length - $space)/$space) + 1);
		}else if($shape == 'hortee'){
			$rung1 = ($radius + $wide + $radius);
			$rung2 = $wide * (((( $radius + $wide + $radius + $space ) - $space)/$space) + 1);
			$rung3 = (( $radius + $wide + $radius + $wide ) / 2 ) * ((($radius- $space) / $space ) + 1);
			$rung_weight = $rung1 + $rung2 + $rung3;
		}else if($shape == 'horcro'){
			$rung1 = ($radius + $wide + $radius) * 2;
			$rung2 = $wide * (((( $radius + $wide + $radius + $space ) - $space) / $space ) + 1);
			$rung3 = (( $radius + $wide + $radius + $wide ) / 2 ) * ((($radius - $space) / $space ) + 1) * 2;
			$rung_weight = $rung1 + $rung2 + $rung3;
		}else if($shape == 'vertee_up'){
			$rung1 = ((($radius + $height + $radius + $space) - $space) / $space ) + 1;
			$rung2 = ((arc($radius, $deg) + $space - $space) / $space ) + 1;
			$rung_weight = $wide * ($rung1 + $rung2);
		}else if($shape == 'vertee_down'){
			$rung_weight = $wide * (((arc($radius, $deg) + $space - $space) / $space ) + 1);
		}else{
			$rung_weight = $wide + $reducer_wide;

		}
		
		return $rung_weight * $rung_H;
		
	}

?>