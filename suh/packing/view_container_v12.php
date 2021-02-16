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

		$QUERY = "SELECT * FROM $db_name WHERE idx = '$idx' and page = '$page' order by FIELD(item,'str','hor') ASC,cbm desc,elbow_rotate asc";
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
		$elbow_x_arr = array();
		$elbow_y_arr = array();
		$elbow_rotate_arr = array();

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
			array_push($elbow_x_arr,$ROW['elbow_x']);
			array_push($elbow_y_arr,$ROW['elbow_y']);
			array_push($elbow_rotate_arr,$ROW['elbow_rotate']);

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
			location.href="./view_container_v12.php?idx="+idx+"&page="+page;		
		});
	}

	$("#right_sider_btn").click(function(){

			var page = page_no + 1;
			location.href="./view_container_v12.php?idx="+idx+"&page="+page;
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
				url: "./ajax_calcbtn_v2.php", 
				type: "post",
				data: {idx:idx,container_fit:container_fit,lm:lm,h_margin:h_margin,v_margin:v_margin,height_margin:height_margin}, 
				success: function(data) {
					//console.log(data)
					location.href="./view_container_v12.php?idx="+idx+"&page=1";
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
import {GLTFLoader} from 'https://threejsfundamentals.org/threejs/resources/threejs/r122/examples/jsm/loaders/GLTFLoader.js';



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



var item_arr = new Array("<?=implode("\",\"",$item_arr);?>");
var wide_arr = new Array("<?=implode("\",\"",$wide_arr);?>");
var height_arr = new Array("<?=implode("\",\"",$height_arr);?>");
var length_arr = new Array("<?=implode("\",\"",$length_arr);?>");
var quantity_arr = new Array("<?=implode("\",\"",$quantity_arr);?>");
var radius_arr = new Array("<?=implode("\",\"",$radius_arr);?>");
var elbow_x_arr = new Array("<?=implode("\",\"",$elbow_x_arr);?>");
var elbow_y_arr = new Array("<?=implode("\",\"",$elbow_y_arr);?>");
var elbow_rotate_arr = new Array("<?=implode("\",\"",$elbow_rotate_arr);?>");



var flange = 20 / 10; // 실제크기의 10분의 1
var fitting_size = 125 / 10; // 실제크기의 10분의 1
var wide_margin = $("input[name=h_margin]").val() / 10; // 실제크기의 10분의 1
var length_margin = $("input[name=v_margin]").val() / 10; // 실제크기의 10분의 1
var height_margin = $("input[name=height_margin]").val() /10; // 실제크기의 10분의 1
	


var total_product_length = 0;
var total_product_wide = 0;

var product_interval = 10;
let locations = 0;
let locations2 = 0;
let locations3 = 0;

var arrlength = new Array();







var loader = new GLTFLoader();



for(let k=0 ; k < item_arr.length ; k++){
	
	var item = item_arr[k];
	let wide = wide_arr[k] / 10; // 실제크기의 10분의 1
	let height = height_arr[k] /10; // 실제크기의 10분의 1
	var length = length_arr[k] / 10; // 실제크기의 10분의 1
	var quantity = quantity_arr[k] * 1;
	var radius = radius_arr[k] / 10; // 실제크기의 10분의 1
	var elbow_x = elbow_x_arr[k] / 10;  // 실제크기의 10분의 1
	var elbow_y = elbow_y_arr[k] / 10;  // 실제크기의 10분의 1
	let elbow_rotate = elbow_rotate_arr[k];

	if(height == 10){

		var max_height_stack = 15;

		if(item_length == "600"){
			max_height_stack = max_height_stack + 2; // 5단 적재 시 1개 추가 ( 고정목 )
		}

	}else if(height == 15){

		var max_height_stack = 10;

		if(item_length == "600"){
			max_height_stack = max_height_stack + 1; // 5단 적재 시 1개 추가 ( 고정목 )
		}
	}
	
	if(item == "str"){

		var item_wide = wide;
		var item_length = length;

	}else if(item == "hor"){

		if(elbow_rotate == "on"){

		var item_wide = elbow_y;
		var item_length = elbow_x;

		}else{

		var item_wide = elbow_x;
		var item_length = elbow_y;
		
		}

	}


	var cw = conWidth - wide_margin; // 컨테이너 wide 크기
	cw = 200;
	var pw = item_wide + flange;
	var max_cp = Math.floor(cw/ pw); // 컨테이너 wide 크기에서 product wide가 최대 몇개가 들어갈수 있을까 소수점 버림 ;; 이러면 1단의 최대적재 갯수 나옴

	
	 /***************
	** palette 부분 **
	****************/
	

	
	var palette_wide_size = (max_cp * item_wide)+20; 
	var palette_height_size = 10;
	let palette_length_size = item_length+20;
	
	console.log(palette_length_size)

	arrlength[k] = palette_length_size;


	var palette_shape = new THREE.BoxBufferGeometry(palette_wide_size,palette_height_size,palette_length_size); // 폭, 높이, 길이
	var palette_material = new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'wood.png' )} );

	var t = new THREE.Mesh( palette_shape, palette_material);
	
	var palette_left_location = -(conWidth/2) + palette_wide_size/2 + wide_margin;
	var pallette_end_location = -conLength/2 + palette_length_size/2;

	if(item == "str"){
	t.position.x = palette_left_location;
	}else if(item == "hor"){
	t.position.x = 0;
	}
	t.position.y = palette_height_size / 2;
	t.position.z = pallette_end_location + locations;

	locations = locations + palette_length_size;
	
	scene.add(t);


	 /***************
	** product 부분 **
	****************/

	var product_wide_size = item_wide + flange;
	var product_height_size = height-2;
	var product_length_size = item_length


	if(item == "str"){

	

	var product_shape = new THREE.BoxBufferGeometry(product_wide_size,product_height_size,product_length_size); // 폭, 높이, 길이
	var product_material = new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( './image/product_material.PNG' )},{color: 0xffffff} );
	
	var product_left_location = -(conWidth/2) + product_wide_size/2 + wide_margin
	var product_end_location = -conLength/2 + product_length_size/2;

	locations2 = locations2 + 10;
	
	var middle_location = palette_wide_size - (product_wide_size * max_cp);
	
	
	for(let z=0; z < max_height_stack ; z ++){

		for(let x=0; x < max_cp ; x++){
				
				var p = new THREE.Mesh( product_shape, product_material);

				p.position.x = product_left_location + (product_wide_size * x ) + middle_location/2;
				p.position.y = ( palette_height_size ) + (product_height_size/2) + (product_height_size + 3)*z;
				p.position.z = product_end_location + locations2;

				
				if(item_length == "600"){

					if(z == 5 || z == 11){
							
						crutch(product_wide_size,product_length_size,p.position.x,p.position.y,p.position.z);

					}else{
						scene.add(p);
					}

				}else if(item_length == "300"){
					
					scene.add(p);
					
				}
		
		}
	}

	locations2 = locations2 + product_length_size + 10;

	
	}else if(item == "hor"){
		
		let hor_locations = locations;
		

		for(let z=0; z < max_height_stack ; z ++){

			for(let x=0; x < max_cp ; x++){


				

				loader.load("./image/elbow90.glb", function (gltf) {
				
				let model = gltf.scene;
				
				var textureLoader = new THREE.TextureLoader();
				var texture = textureLoader.load( './image/product_material.PNG' );
				texture.flipY = false;

				gltf.scene.traverse( function ( child ) {

                    if ( child.isMesh ) {

						child.material.map = texture;
				
                    }

                } );
				
				
				
				if(wide == 90){
				model.scale.set(2,1,2)
				}else if(wide == 60){
				model.scale.set(1.5,1,1.5)
				}

				
				if(elbow_rotate == "on"){
				
				model.rotation.y = 4;
				model.position.y = ( palette_height_size ) + (product_height_size/2) +  (11*z);
				model.position.z = pallette_end_location + hor_locations - palette_length_size + item_length;
				
				console.log(palette_length_size)

				}else{

				model.rotation.y = 40;
				model.position.y = ( palette_height_size ) + (product_height_size/2) +  (11*z);
				model.position.z = pallette_end_location + hor_locations - palette_length_size;



				}
				scene.add(model);

				

				}, undefined, function (error) {
				console.error(error);
				})

					
			


				

			
			}
		}

			




	}



	

	
}






	function crutch(wide,length,x,y,z){
		
		/***************
		** 버팀목 부분   **
		****************/
		
		var crutch_wide_size = wide;
		var crutch_height_size = 15;
		var crutch_length_size = 15;

		var crutch_shape = new THREE.BoxBufferGeometry(crutch_wide_size,crutch_height_size,crutch_length_size); // 폭, 높이, 길이
		var crutch_material = new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'wood.png' )} );

		
		var c = new THREE.Mesh( crutch_shape, crutch_material);
			
		c.position.x = x; 
		c.position.y = y;
		c.position.z = z + -length/3 ;


		var c2 = new THREE.Mesh( crutch_shape, crutch_material);
			
		c2.position.x = x; 
		c2.position.y = y;
		c2.position.z = z + length/3 ;
		
		

		scene.add(c);
		scene.add(c2);

		

	}











	 /***************
	** light   부분 **
	****************/

var skyColor = 0xffffff; 
var groundColor = 0xa0a0a0;   
var intensity = 3;
var light = new THREE.HemisphereLight(skyColor, groundColor,intensity);
scene.add(light);


/*



for (let i = 0; i < 1; i++) {

	for(let k =0; k<2;k++){

    loader.load("./image/ladder_str.glb", function (gltf) {
        
		var model = gltf.scene;
		
		model.scale.set(1,1,1);

	
		model.position.x = product_left_location + (95 * k );
		model.position.y = ( palette_height_size +10) + (product_height_size/2) + (product_height_size + 1.6)*i
		model.position.z = -(conLength/2)+ 34;

		model.rotation.x = Math.PI;
		scene.add(model);

    }, undefined, function (error) {
        console.error(error);
    })

	}
}

*/





var total_product_length = arrlength.reduce(function add(sum, currValue) {
  return sum + currValue;
}, 0);


// y = +는 윗쪽 - 아랫쪽 ;; y는 height 연관
// x = +는 오른쪽 - 왼쪽 ;; x는 wide 연관
// z = +는 앞쪽 -는 뒷쪽 ;; z는 length 연관


var container_reminder_wide = ( conWidth ) * 10;
var container_reminder_height = ( conHeight ) * 10;
var container_reminder_length =  (conLength*10) - (total_product_length*10).toFixed(1) ;




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

