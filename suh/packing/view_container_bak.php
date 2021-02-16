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

		$QUERY = "SELECT count(*) as cnt FROM $db_name WHERE idx = '$idx'";
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
		$ROW = $SQL->fetch();

		$item = $ROW['item'];
		$wide = $ROW['wide'];
		$height = $ROW['height'];
		$length = $ROW['length'];
		$radius = $ROW['radius'];
		$deg = $ROW['deg'];
		$quantity = $ROW['quantity'];
		$total_weight = $ROW['total_weight'];
		$cbm = $ROW['cbm'];
	
		
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
						if($item == "str"){$description = $item.";".$wide.";".$height.";".$length;}
						else if($item == "hor"){$description = $item.";".$wide.";".$height.";".$radius.";".$deg;}
						
					?>
					<td><?=$page?></td>
					<td><?=$description?></td>
					<td><?=$quantity?></td>
					<td><?=$cbm?></td>
					<td><?=$total_weight?></td>
				</tr>
				<tr>
					<td colspan="2">Total</td>
					<td></td>
					<td></td>
					<td></td>
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
			location.href="./view_container.php?idx="+idx+"&page="+page;		
		});
	}

	$("#right_sider_btn").click(function(){

			var page = page_no + 1;
			location.href="./view_container.php?idx="+idx+"&page="+page;
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
				url: "./ajax_calcbtn.php", 
				type: "post",
				data: {idx:idx,container_fit:container_fit,lm:lm,h_margin:h_margin,v_margin:v_margin,height_margin:height_margin}, 
				success: function(data) {
					//console.log(data)
					location.href="./view_container.php?idx="+idx+"&page=1";
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

container_fit = "<?=$_REQUEST['fit']?>";

if(!container_fit){
	container_fit = 40;
}

if(container_fit == 20){
	conWidth = 2352 / 10;
	conHeight = 2393 / 10;
	conLength = 5898 / 10;
	fov = 30;
    palette_count = 11;
    palette_margin = 6;
}else if(container_fit == 40){
	conWidth = 2352 / 10;
	conHeight = 2393 / 10;
	conLength = 12025 / 10;
	fov = 50;
    palette_count = 21;
    palette_margin = 9;
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


var description = "<?=$description?>";



/*
//test
var palette_size = 1100 / 10;
var palette_sahpe = new THREE.BoxBufferGeometry( palette_size, 120 / 10, palette_size );


var palette_material = new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'wood.png' )} );

var counta = -1;
var countb = -2;
var count = 0;
var count2 = 0;
for(var i = 1; i < palette_count ; i ++){
var t = new THREE.Mesh( palette_sahpe, palette_material);

if(i % 2 == 1){ // 홀수
    
    count = i + counta;
    counta--;

    t.position.x = ( conWidth / 2 / 2 ) * -1 ;
    t.position.y = 10;
    t.position.z = ( conLength  / 2 ) * -1 + palette_size / 2 + palette_margin + (palette_size + palette_margin) * count;
}else if(i % 2 == 0){ // 짝수
    
    count2 = i + countb;
    countb--; 

    t.position.x = conWidth / 2 / 2;
    t.position.y = 10;
    t.position.z = ( conLength  / 2 ) * -1  + palette_size / 2 + palette_margin + (palette_size + palette_margin) * count2;
}


scene.add(t);


}

*/

/*
//wood_palette setting

var wood_palette_block_width = 200 / 10;
var wood_palette_block_height = 120 / 10;
var wood_palette_block_depth = 1100 / 10;

// BoxBufferGeometry : 육면체 ( width,height,depth )
var wood_palette_top_shape = new THREE.BoxBufferGeometry( 1100 / 10, 2, 1100 / 10 );
var wood_palette_bottom_shape = new THREE.BoxBufferGeometry( wood_palette_block_width , wood_palette_block_height , wood_palette_block_depth);

// wood palette image load
var wood_palette_material = new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'wood.png' )} );

// wood palette top
var wood_palette_top = new THREE.Mesh( wood_palette_top_shape, wood_palette_material);
wood_palette_top.position.set( 0, 20, 0 );

// wood palette bottom 
var wood_palette_bottom1 = new THREE.Mesh( wood_palette_bottom_shape, wood_palette_material);
var wood_palette_bottom2 = new THREE.Mesh( wood_palette_bottom_shape, wood_palette_material);
var wood_palette_bottom3 = new THREE.Mesh( wood_palette_bottom_shape, wood_palette_material);

var wood_palette_bottom1_location_x = ( ( ( 1100 / 10 - ( wood_palette_block_width  * 3 ) ) / 2 ) + wood_palette_block_width ) * -1;
var wood_palette_bottom3_location_x = ( ( 1100 / 10 - ( wood_palette_block_width  * 3 ) ) / 2 ) + wood_palette_block_width ;

wood_palette_bottom1.position.set( wood_palette_bottom1_location_x, 10, 0 );
wood_palette_bottom2.position.set( 0, 10 , 0 );
wood_palette_bottom3.position.set( wood_palette_bottom3_location_x, 10 , 0 );


// wood palette scene add
//scene.add(wood_palette_top);
//scene.add(wood_palette_bottom1);
//scene.add(wood_palette_bottom2);
//scene.add(wood_palette_bottom3);
*/

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