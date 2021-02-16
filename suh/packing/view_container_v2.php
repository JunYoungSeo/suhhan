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

		for($i = 0;$ROW = $SQL->fetch();$i++){
			
			array_push($item_arr,$ROW['item']);
			array_push($wide_arr,$ROW['wide']);
			array_push($height_arr,$ROW['height']);
			array_push($length_arr,$ROW['length']);
			array_push($quantity_arr,$ROW['quantity']);
			array_push($cbm_arr,$ROW['cbm']);
			array_push($weight_arr,$ROW['total_weight']);

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
						<td>결과</td>
						<td>수량</td>
						<td>Page</td>
					</tr>
					<tr>
						<td>
							<input type="text" style="width:100%;">
						</td>
						<td>
							<input type="text" style="width:100%;">
						</td>
						<td>
							<input type="text" style="width:100%;" value="<?=$page."/".$total_page?>">
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
				
					$description = $item_arr[$k].";".$wide_arr[$k].";".$height_arr[$k].";".$length_arr[$k];
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
			location.href="./view_container_v2.php?idx="+idx+"&page="+page;		
		});
	}

	$("#right_sider_btn").click(function(){

			var page = page_no + 1;
			location.href="./view_container_v2.php?idx="+idx+"&page="+page;
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
					location.href="./view_container_v2.php?idx="+idx+"&page=1";
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

import { DDSLoader } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/loaders/DDSLoader.js';
import { TGALoader } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/loaders/TGALoader.js';
import { MTLLoader } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/loaders/MTLLoader.js';
import { OBJLoader } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/loaders/OBJLoader.js';
import { OrbitControls } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/controls/OrbitControls.js';
import { Reflector } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/objects/Reflector.js';

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


for(var k=0 ; k < item_arr.length ; k++){

	var wide = wide_arr[k] * 1;
	var height = height_arr[k] * 1;
	var length = length_arr[k] * 1;
	var quantity = quantity_arr[k] * 1;

	var max_wide_stack = 2;
	var max_height_stack = 15;
	var max_length_stack = 1;


	// 3D 추가
	var flange = 20;
	var wide_margin = 160;
	var length_margin = 160;
	
	
	var palette_wide_size = (((wide + flange) * max_wide_stack) + wide_margin ) / 10;
	var palette_height_size = 120 / 10;
	var palette_length_size = ((length + length_margin) * max_length_stack) / 10;
	var far_end = (( conLength / 2 ) * -1) + ( palette_length_size / 2 ) + 160/10;
	var far_left = ((((wide + flange) * max_wide_stack ) / 2 ) / 10 * -1) + (((wide + flange)/2)/10) ; // 가장 왼쪽;

	var palette_sahpe = new THREE.BoxBufferGeometry(palette_wide_size,palette_height_size,palette_length_size); // 폭, 높이, 길이
	var palette_material = new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'wood.png' )} );

	var product_wide_size = (wide + flange)  / 10;
	var product_height_size = height / 10;
	var product_length_size = (length + length_margin) / 10;

	var product_shape = new THREE.BoxBufferGeometry(product_wide_size,product_height_size,product_length_size); // 폭, 높이, 길이
	var product_material = new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'str_front.png' )} );



	var t = new THREE.Mesh( palette_sahpe, palette_material);

	t.position.y = palette_height_size / 2;
	t.position.z = far_end + (k * ( palette_length_size + 10 ) );
	scene.add(t);


		
		for(var i = 0; i < max_height_stack ; i ++){ //  높이 갯수만큼 반복 

			for(var j = 0; j < max_wide_stack ; j ++){ //  와이드 갯수만큼 반복 ( 왼쪽부터 쌓는다..)
				
				var p = new THREE.Mesh( product_shape, product_material);


				p.position.x = far_left + ((((wide + flange)/2)/10) * j * 2);
					

				p.position.y = (palette_height_size + product_height_size) + (i * (product_height_size+1.6));
				p.position.z = far_end + (k * ( palette_length_size + 10 ) );

				scene.add(p);
			}
		}


}


// y = +는 윗쪽 - 아랫쪽 ;; y는 height 연관
// x = +는 오른쪽 - 왼쪽 ;; x는 wide 연관
// z = +는 앞쪽 -는 뒷쪽 ;; z는 length 연관







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