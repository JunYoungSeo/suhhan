<?php
include_once("xlsxwriter.class.php");

session_start();

$idx = $_REQUEST['idx'];
$filename = $idx.".xlsx";

header('Content-disposition: attachment; filename="'.XLSXWriter::sanitize_filename($filename).'"');
header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
header('Content-Transfer-Encoding: binary');
header('Cache-Control: must-revalidate');
header('Pragma: public');

/////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////
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


//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
$header1 = array('C.No'=>'string','Item'=>'string','SIZE'=>'string','QUANTITY'=>'string','WEIGHT'=>'string','TOTAL WEIGHT'=>'string','CBM'=>'string');

$Sheet1 = array();

$QUERY = "SELECT * FROM `packing__full_container` WHERE idx = '$idx'";
$SQL = $pdo -> prepare($QUERY);
$SQL -> execute();
$SQL -> setFetchMode(PDO::FETCH_ASSOC);

$page = "1";
$total_quantity = 0;
$total_weight = 0;
$total_cbm = 0;

while($ROW = $SQL->fetch()){
    
	if($ROW['item'] == "str"){
		$size = $ROW['wide']."W x ".$ROW['height']."H x ".$ROW['length']."L";
	}else if($ROW['item'] == "hor"){
		$size = $ROW['wide']."W x ".$ROW['height']."H x ".$ROW['radius']."R - ".$ROW['deg'];
	}


	if($ROW['page'] != $page){

		$Sheet1_1_array = array("Total","","",$total_quantity,"",NUMBER_FORMAT($total_weight,1),$total_cbm);
		
		$Sheet1_array = array("","","","","","","");
		
		array_push($Sheet1,$Sheet1_1_array);

		array_push($Sheet1,$Sheet1_array);
		$page = $ROW['page'];

		$total_quantity = 0;
		$total_weight = 0;
		$total_cbm = 0;

		$total_quantity = $total_quantity + $ROW['quantity'];
		$total_weight = $total_weight + $ROW['total_weight'];
		$total_cbm = $total_cbm + $ROW['cbm'];
	
	}else{
		
		$total_quantity = $total_quantity + $ROW['quantity'];
		$total_weight = $total_weight + $ROW['total_weight'];
		$total_cbm = $total_cbm + $ROW['cbm'];

	}

	$Sheet1_array = array($ROW['page'],$ROW['item'],$size,$ROW['quantity'],$ROW['weight'],NUMBER_FORMAT($ROW['total_weight'],1),$ROW['cbm']);


	array_push($Sheet1,$Sheet1_array);
}

// 마지막열 합계
$Sheet1_1_array = array("Total","","",$total_quantity,"",NUMBER_FORMAT($total_weight,1),$total_cbm);
array_push($Sheet1,$Sheet1_1_array);


$writer = new XLSXWriter();
$writer->writeSheetHeader('Sheet1', $header1,['widths' => [10,10,40,20,16,26,10], 'font-size' => 15, 'valign'=>'center', 'halign'=>'center','font'=>'맑은 고딕'] );

$writer->setAuthor('Some Author');

foreach($Sheet1 as $row)
	$writer->writeSheetRow('Sheet1', $row,['widths' => [10,10,40,20,16,26,10], 'font-size' => 15, 'valign'=>'center', 'halign'=>'center','font'=>'맑은 고딕']);


$writer->writeToStdOut();

write_log("DOWNLOAD/".$filename." download");
exit(0);
?>



<?
	function write_log($type=null){
		
		if($type == null){
			$type = "Access";
		}

		$file_postion = str_replace("/www/sh1920.godohosting.com","",$_SERVER['SCRIPT_FILENAME']);
		$log_txt = $_SESSION['id']." | ".$_SERVER[REMOTE_ADDR]." | ".date("Y-m-d H:i:s")." | ".$file_postion." | ".$type; 
		  
		$log_file = fopen("/www/sh1920.godohosting.com/suh/log/".date("Ymd").".txt", "a");  
		fwrite($log_file, $log_txt."\r\n");  
		fclose($log_file);  

	}
?>