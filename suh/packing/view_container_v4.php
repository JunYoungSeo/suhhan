<?include_once("../include/common.php")?>

<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>

<link type="text/css" rel="stylesheet" href="view_container.css">

<?
	$idx = $_REQUEST['idx'];
	$page = $_REQUEST['page'];
	if(!$page){
		$page = 0;
	}
	
	if($idx){

		$db_name = "`packing__condition`";

		$QUERY = "SELECT * FROM $db_name WHERE idx = '$idx' ";
		$SQL = $pdo -> prepare($QUERY);
		$SQL -> execute();
		$SQL -> setFetchMode(PDO::FETCH_ASSOC);
		$ROW = $SQL->fetch();

		$container_fit = $ROW['fit'];
		$lm = $ROW['lm'];
		$h_margin = $ROW['h_margin'];
		$v_margin = $ROW['v_margin'];
		$height_margin = $ROW['height_margin'];

		
		$db_name = "`packing__full_container`";

		$QUERY = "SELECT count(*) as cnt FROM ( select count(page) from $db_name WHERE idx = '$idx' group by page ) as c";
		$SQL = $pdo -> prepare($QUERY);
		$SQL -> execute();
		$SQL -> setFetchMode(PDO::FETCH_ASSOC);
		$ROW = $SQL->fetch();
		
		$total_page = $ROW['cnt'];
		

		$db_name = "`packing__full_container`";

		$QUERY = "SELECT * FROM $db_name WHERE idx = '$idx' and page = '$page'";
		$SQL = $pdo -> prepare($QUERY);
		$SQL -> execute();
		$SQL -> setFetchMode(PDO::FETCH_ASSOC);
		
		$item_arr = array();
		$wide_arr = array();
		$height_arr = array();
		$length_arr = array();
		$quantity_arr = array();
		$cbm_arr = array();
		$weight_arr = array();
		$radius_arr = array();
		$deg_arr = array();

		for($i = 0;$ROW = $SQL->fetch();$i++){
			
			array_push($item_arr,$ROW['item']);
			array_push($wide_arr,$ROW['wide']);
			array_push($height_arr,$ROW['height']);
			array_push($length_arr,$ROW['length']);
			array_push($quantity_arr,$ROW['quantity']);
			array_push($cbm_arr,$ROW['cbm']);
			array_push($weight_arr,$ROW['total_weight']);
			array_push($radius_arr,$ROW['radius']);
			array_push($deg_arr,$ROW['deg']);

		}

	
	}
	
?>


<div id="main_div">
	<div id="header_div">
		<div id="left_header_div">
			<div id="common_condition_div">
				<table id="common_condition_table">
					<tr>
						<td>Container 사양</td>
						<td>적재방식</td>
						<td colspan=3>여유율</td>
					</tr>
					<tr>
						<td>
							<select style="width:100%;" name="container_fit">
								<option>-</option>
								<option value="20" <?if($container_fit == "20"){print "selected";}?>>20 Ft</option>
								<option value="40" <?if($container_fit == "40"){print "selected";}?>>40 Ft</option>
							</select>
						</td>
						<td>
							<select style="width:100%;" name="lm">
								<option>-</option>
								<option value="1" <?if($lm == "1"){print "selected";}?>>1단</option>
								<option value="2" <?if($lm == "2"){print "selected";}?>>2단</option>
							</select>
						</td>
						<td>
							<input type="text" style="width:100%;" <?if($h_margin){print "value=".$h_margin;}?> name="h_margin" placeholder="WIDE">
						</td>
						<td>
							<input type="text" style="width:100%;" <?if($v_margin){print "value=".$v_margin;}?> name="v_margin" placeholder="LENGTH">
						</td>
						<td>
							<input type="text" style="width:100%;" <?if($height_margin){print "value=".$height_margin;}?> name="height_margin" placeholder="HEIGHT">
						</td>
					</tr>
				</table>
				<br>
				<button id="calc_btn">Calc</button>
			</div>
		</div>
		<div id="right_header_div">
			<div id="result_div">
				<table id="result_table">
				
					<tr>
						<td colspan="2" width="70%;">빈 공간</td>
						<td>Page</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="text" style="width:100%;" name="container_reminder_length" disabled>
						</td>
					
						<td>
							<input type="text" style="width:100%;" value="<?=$page."/".$total_page?>" name="view_page">
						</td>
					</tr>
				</table>
			</div>
			
			<br>
				<a href="./download_packinglist.php?idx=<?=$idx?>"><img src="../images/excel.png" width="20px" height="20px" style="vertical-align: middle;">&nbsp;PACKING LIST DOWNLOAD</a>

		</div>
	</div>

	<div id="left_div">

		<?if($page > 1){?>
		<div id="left_sider_div">
			<button id="left_sider_btn">◀</button>
		</div>
		<?}?>
	</div>

	<div id="right_div">
		
		<?if($page != $total_page){?>
		<div id="right_sider_div">
			<button id="right_sider_btn">▶</button>
		</div>
		<?}?>
		

		<div id="list_div">
			<table id="list_table">
				<colgroup>
					<col width="10%">
					<col width="50%">
					<col width="15%">
					<col width="10%">
					<col width="15%">
				 </colgroup>
				<tr>
					<td>No</td>
					<td>사양</td>
					<td>수량</td>
					<td>CBM</td>
					<td>중량</td>
				</tr>
				<tr>
				<?
					$total_quantity = 0;
					$total_cbm = 0;
					$total_weight = 0;

					for($k=0;$k<count($item_arr);$k++){
				
					
					if($item_arr[$k] == "str"){
						$description = "STRAIGHT".";".$wide_arr[$k]."W;".$height_arr[$k]."H;".$length_arr[$k]."L";
					}else if($item_arr[$k] == "hor"){
						$description = $deg_arr[$k]."° HORI. ELBOW".";".$wide_arr[$k]."W;".$height_arr[$k]."H;".$radius_arr[$k]."R";
					}
					
					
					$total_quantity = $total_quantity + (int)$quantity_arr[$k];
					$total_cbm = $total_cbm + $cbm_arr[$k];
					$total_weight = $total_weight + $weight_arr[$k];
				?>
					<td><?=$k+1?></td>
					<td><?=$description?></td>
					<td><?=$quantity_arr[$k]?></td>
					<td><?=$cbm_arr[$k]?></td>
					<td><?=number_format($weight_arr[$k],1)?></td>
				</tr>
				<?}?>
				<tr>
					<td colspan="2">Total</td>
					<td><?=$total_quantity?></td>
					<td><?=$total_cbm?></td>
					<td><?=number_format($total_weight,1)?></td>
				</tr>
			</table>
		</div>
		

		
	</div>
</div>





<script>

var idx = "<?=$idx?>";
var page_no = "<?=$page?>";
	page_no = page_no * 1;


$(function(){
	
	if(page_no > 1){
		$("#left_sider_btn").click(function(){

			var page = page_no - 1;
			location.href="./view_container_v4.php?idx="+idx+"&page="+page;		
		});
	}

	$("#right_sider_btn").click(function(){

			var page = page_no + 1;
			location.href="./view_container_v4.php?idx="+idx+"&page="+page;
	});

	$("#calc_btn").click(function(){

		var container_fit = $("select[name=container_fit]").val();
			if(container_fit == "-"){
				return;
			}
		var lm = $("select[name=lm]").val();
		var h_margin = $("input[name=h_margin]").val();
		var v_margin = $("input[name=v_margin]").val();
		var height_margin = $("input[name=height_margin]").val();
		
		$.ajax({ 
				url: "./ajax_calcbtn_v1.php", 
				type: "post",
				data: {idx:idx,container_fit:container_fit,lm:lm,h_margin:h_margin,v_margin:v_margin,height_margin:height_margin}, 
				success: function(data) {
					//console.log(data)
					location.href="./view_container_v4.php?idx="+idx+"&page=1";
				} 
		});	 

	});
			
});
</script>



<script>
$(window).on('load', function(){
  setTimeout(function() {
	 $("canvas").css("display","block");
	 $("canvas").css("position","absolute");
	 $("canvas").css("position","absolute");
	 $("canvas").css("top","20%");
	 $("canvas").css("left","60px");
	 $("canvas").css("width","65%");
	 $("canvas").css("height","75%");
	 $("canvas").css("padding","1% 1% 0% 1%");

	
}, 500);
});


</script>



<script type="module">

import * as THREE from 'https://rawgit.com/mrdoob/three.js/master/build/three.module.js';
import { OrbitControls } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/controls/OrbitControls.js';

/*

import { DDSLoader } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/loaders/DDSLoader.js';
import { TGALoader } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/loaders/TGALoader.js';
import { MTLLoader } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/loaders/MTLLoader.js';
import { OBJLoader } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/loaders/OBJLoader.js';
import { Reflector } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/objects/Reflector.js';

*/

var scene, camera, renderer;
var maxWcount, maxHcount, maxLcount, Controls , sphereGroup, smallSphere;
var container_fit, conWidth, conHeight, conLength;
var fov;
var palette_count,palette_margin;

container_fit = "<?=$container_fit?>";

if(!container_fit){
	container_fit = 40;
}

if(container_fit == 20){
	conWidth = 2352 / 10;
	conHeight = 2393 / 10;
	conLength = 5898 / 10;
	fov = 30;
    
}else if(container_fit == 40){
	conWidth = 2352 / 10;
	conHeight = 2393 / 10;
	conLength = 12025 / 10;
	fov = 50;
}


// scene size
var WIDTH = window.innerWidth;
var HEIGHT = window.innerHeight;

WIDTH = WIDTH * 0.75;
HEIGHT = HEIGHT-150;

scene = new THREE.Scene();
scene.background = new THREE.Color('white');


// camera
/**
**	PerspectiveCamera( fov : Number, aspect : Number, near : Number, far : Number )
**	fov — Camera frustum vertical field of view.
**	aspect — Camera frustum aspect ratio.
**	near — Camera frustum near plane.
**	far — Camera frustum far plane.
**/
camera = new THREE.PerspectiveCamera( fov, WIDTH/HEIGHT, 0.1, 2000 );
camera.position.set(0,0,2000);


// renderer
renderer = new THREE.WebGLRenderer( { antialias: true } );
renderer.setSize( WIDTH, HEIGHT );
var cube = document.createElement( 'main' );
document.body.appendChild( cube );
cube.appendChild( renderer.domElement );

window.addEventListener('resize', () => {
	renderer.setSzie(WIDTH, HEIGHT);
	camera.aspect = WIDTH/HEIGHT;

	camera.updateProjectMatrix();
});


// container walls setting
// PlaneBufferGeometry :: PlaneBufferGeometry(width : Float, height : Float )
var container_wall1 = new THREE.PlaneBufferGeometry( conWidth + 0.1, conHeight + 0.1 );
var container_wall2 = new THREE.PlaneBufferGeometry( conLength + 0.1, conHeight + 0.1 );
var container_wall3 = new THREE.PlaneBufferGeometry( conWidth + 0.1, conLength + 0.1 );
var container_geometry = new THREE.PlaneBufferGeometry( 100, 100 );
var container_material = new THREE.MeshBasicMaterial( { color: 'white' } );

var sphereCap = new THREE.Mesh( container_geometry, container_material );
sphereCap.position.y = - 15 * Math.sin( Math.PI / 180 * 30 ) - 0.05;
sphereCap.rotateX( - Math.PI );


var container_wall_top = new THREE.Mesh( container_wall3, new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'ContT.png' ) } ) );
container_wall_top.position.y = conHeight;
container_wall_top.rotateX( Math.PI / 2 );

var container_wall_bottom = new THREE.Mesh( container_wall3, new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'ContT.png' ) } ) );
container_wall_bottom.position.y = 0;
container_wall_bottom.rotateX( - Math.PI / 2 );

var container_wall_front = new THREE.Mesh( container_wall1, new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'ContF.png' ) } ) );
container_wall_front.position.z = conLength / 2;
container_wall_front.position.y = conHeight / 2;
container_wall_front.rotateY( Math.PI );

var container_wall_rear = new THREE.Mesh( container_wall1, new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'ContF.png' ) } ) );
container_wall_rear.position.z = ( conLength / 2 ) * -1;
container_wall_rear.position.y = conHeight / 2;
container_wall_rear.rotateY(  Math.PI * 2 );

var container_wall_rside = new THREE.Mesh( container_wall2, new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'ContS.png' ) } ) );
container_wall_rside.position.x = conWidth / 2;
container_wall_rside.position.y = conHeight / 2;
container_wall_rside.rotateY( - Math.PI / 2 );

var container_wall_lside = new THREE.Mesh( container_wall2, new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'ContS.png' ) } ) );
container_wall_lside.position.x = ( conWidth / 2 ) * -1;
container_wall_lside.position.y = conHeight / 2;
container_wall_lside.rotateY( Math.PI / 2 );

// container wall scene add
scene.add( container_wall_top );
scene.add( container_wall_bottom );
scene.add( container_wall_front );
scene.add( container_wall_rear );
scene.add( container_wall_rside );
scene.add( container_wall_lside );


// light setting 
// AmbientLight :: This light globally illuminates all objects in the scene equally.
// AmbientLight( color : Integer, intensity : Float )
scene.add(new THREE.AmbientLight( 'white' , 5));






var item_arr = new Array("<?=implode("\",\"",$item_arr);?>");
var wide_arr = new Array("<?=implode("\",\"",$wide_arr);?>");
var height_arr = new Array("<?=implode("\",\"",$height_arr);?>");
var length_arr = new Array("<?=implode("\",\"",$length_arr);?>");
var quantity_arr = new Array("<?=implode("\",\"",$quantity_arr);?>");
var radius_arr = new Array("<?=implode("\",\"",$radius_arr);?>");



var flange = 20;
var wide_margin = ($("input[name=h_margin]").val() * 1) / 10;
var length_margin = $("input[name=v_margin]").val() * 1 / 10;
var height_margin = $("input[name=height_margin]").val() * 1;
	



var total_product_length = 0;
var total_product_wide = 0;

var product_interval = 10;
var arrlength = new Array();

for(var k=0 ; k < item_arr.length ; k++){

	var item = item_arr[k];
	var wide = wide_arr[k] * 1;
	var height = height_arr[k] * 1;
	var length = (length_arr[k] * 1) / 10;
	var quantity = quantity_arr[k] * 1;
	var radius = radius_arr[k] * 1;

	var max_height_stack = 0;
	
	if(height == 100){
		max_height_stack = 15;
	}else if(height == 150){
		max_height_stack = 10;
	}
	

	if(item == "str"){

		var item_wide = wide;
		var item_length = length;

	}else if(item == "hor"){

		var item_wide = wide + radius + 125;
		var item_length = ( wide + radius + 125 ) / 10;
	}

	
	
	
	var container_end_location = -conLength/2 + item_length /2 + length_margin;
	var each_wide_margin = item_wide / 20; // 2. 컨테이너 왼쪽벽 위치 계산 ;; conwidth 나누기 4  ;;  나누기 4 하는 이유는 컨테이너 중간위치 2 , 그리고 제품 중앙위치가 컨테이너 왼쪽벽에 걸려서 2
	var container_left_location = -(conWidth/2) + each_wide_margin + wide_margin;

	var cw = conWidth - wide_margin; // 컨테이너 wide 크기
	var pw = (item_wide + flange) / 10; // product wide 크기의 십분의 1
	var max_cp = Math.floor(cw/ pw); // 컨테이너 wide 크기에서 product wide가 최대 몇개가 들어갈수 있을까 소수점 버림 ;; 이러면 1단의 최대적재 갯수 나옴

	
	var product_wide_size = (item_wide + flange)  / 10;
	var product_height_size = height / 10;
	var product_length_size = item_length;
	
	arrlength[k-1] = product_length_size;

	var xxx = arrlength.reduce(function add(sum, currValue) {
		return sum + currValue;
	}, 0);

	console.log(xxx)
	var product_shape = new THREE.BoxBufferGeometry(product_wide_size,product_height_size,product_length_size); // 폭, 높이, 길이
	var product_material = new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'str_front.png' )} );

	var palette_material = new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'wood.png' )} );

	
	//1. 갯수만큼 반복 시킨다! but 여기서는 갯수 /2 를 해야함. 왜냐면 겹침적재를 하니까
	for(var z=0; z < max_height_stack ; z ++){
		
		//2. 1단부터 적재 시킨다 ..  가장 왼쪽부터 모든것은 실제크기의 1/10 수준
		//2-1. 1단갯수만큼 반복 시작
		for(var x=0; x < max_cp ; x++){
			
			if(z == 0){

			var t = new THREE.Mesh( product_shape, palette_material);
	
			t.position.x = (product_wide_size * x) + container_left_location;
			t.position.y = 10;
			t.position.z = container_end_location + xxx;
			
			
			scene.add(t);

			}

			
			var p = new THREE.Mesh( product_shape, product_material);
			
			
			p.position.x = (product_wide_size * x) + container_left_location;
			p.position.y = 20 + ((product_height_size+1.6) * z);
			p.position.z = container_end_location + xxx;
			
			scene.add(p);

			

		}

	}

	
}






var total_product_length = arrlength.reduce(function add(sum, currValue) {
  return sum + currValue;
}, 0);


// y = +는 윗쪽 - 아랫쪽 ;; y는 height 연관
// x = +는 오른쪽 - 왼쪽 ;; x는 wide 연관
// z = +는 앞쪽 -는 뒷쪽 ;; z는 length 연관


var container_reminder_wide = ( conWidth ) * 10;
var container_reminder_height = ( conHeight ) * 10;
var container_reminder_length =  (conLength*10) - (total_product_length*10) - (length_margin*10) - ((arrlength.length-1) * 100) ;



$("input[name=container_reminder_length]").val("  가로 :   "+container_reminder_wide.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+"   세로 :   "+container_reminder_length.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")+"   높이 :   "+container_reminder_height.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
$("input[name=container_reminder_length]").css("text-align","center").css("background-color","yellow").css("font-weight","bold");
$("input[name=view_page]").css("text-align","center");





// Control setting
Controls = new OrbitControls( camera, renderer.domElement );
Controls.target.set( 0, 100, 0 );
Controls.maxDistance = 1000;
Controls.minDistance = 100;
Controls.maxPolarAngle = Math.PI / 2;
Controls.update();


function animate() {

requestAnimationFrame( animate );

renderer.render( scene, camera );

}

$("canvas").css("display","none");

animate();


</script>