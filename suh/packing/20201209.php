<?include_once("../include/common.php");?>
<?include_once("./header.php");?>

<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
<link type="text/css" rel="stylesheet" href="threemain.css">

<?
	$idx = $_REQUEST['idx'];
?>
		
<div id="main">
	<div id="labels" class="menu">
		<button class="fit_btn" onclick="move(<?=$idx?>,20);">20 FIT</button> 
		<button class="fit_btn" onclick="move(<?=$idx?>,40);">40 FIT</button> 
	</div>


</div>


<script>
$(window).on('load', function(){
  setTimeout(function() {
	 $("canvas").css("display","block");
}, 500);
});

function move(idx,fit){
	location.href="./Threejs.php?idx="+idx+"&fit="+fit;
}
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

container_fit = "<?=$_REQUEST['fit']?>";

if(!container_fit){
	container_fit = 20;
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


//wood_palette setting

var wood_palette_block_width = 200 / 10;
var wood_palette_block_height = 120 / 10;
var wood_palette_block_depth = conLength;

// BoxBufferGeometry : 육면체 ( width,height,depth )
var wood_palette_top_shape = new THREE.BoxBufferGeometry( conWidth, 2, conLength );
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

var wood_palette_bottom1_location_x = ( ( ( conWidth - ( wood_palette_block_width  * 3 ) ) / 2 ) + wood_palette_block_width ) * -1;
var wood_palette_bottom3_location_x = ( ( conWidth - ( wood_palette_block_width  * 3 ) ) / 2 ) + wood_palette_block_width ;

wood_palette_bottom1.position.set( wood_palette_bottom1_location_x, 10, 0 );
wood_palette_bottom2.position.set( 0, 10 , 0 );
wood_palette_bottom3.position.set( wood_palette_bottom3_location_x, 10 , 0 );

console.log(wood_palette_bottom1_location_x)
// wood palette scene add
scene.add(wood_palette_top);
scene.add(wood_palette_bottom1);
scene.add(wood_palette_bottom2);
scene.add(wood_palette_bottom3);


// Control setting
Controls = new OrbitControls( camera, renderer.domElement );
Controls.target.set( 0, 100, 0 );
Controls.maxDistance = 1000;
Controls.minDistance = 100;
Controls.maxPolarAngle = Math.PI / 2;
Controls.update();


//ladder_str 
function str(){
	var wide = 300/10;//$("#wide").val() / 10;
	var height = 150/10;//$("#height").val() / 10;
	var length = 3000/10;//$("#length").val() / 10;
	var radius = 300//$("#radius").val() / 10;

	var margin = 2;       //여유공간
	var thickness = 1.6;    //두께
	maxWcount = 4;//Math.floor(conWidth / (wide+margin));     //폭 최대 개수
	maxHcount = 2;//Math.floor((conHeight-30) / (height+margin));  //높이 최대 개수
	maxLcount = 2;//Math.floor(conLength / length);  //길이 최대 개수
	var conMaxC = 500;//maxWcount * maxHcount * maxLcount; //한 컨테이너 최대 개수
	var conCount = 1;
	conCount = 1;//Math.ceil(quantity / conMaxC);        //총 컨테이너 개수
	var thisQ = 5;//quantity - (conMaxC * (conCount-1)); //현재 컨테이너의 트레이 개수
	var conMargin = 10;//conWidth - (maxWcount * (wide+margin)); //컨테이너 남은 넓이
	var wc = 0, hc = 0, lc = 0;

	//console.log(maxWcount, maxHcount, maxLcount, conMaxC, conCount, thisQ , conMargin);

	var boxGroup = new THREE.Object3D();
				scene.add( boxGroup );
	
				var Trayside = new THREE.BoxBufferGeometry( thickness, height, length-0.1);
				var Traybottom = new THREE.BoxBufferGeometry( wide, thickness, length-0.1);
				var TrayMaterial = new THREE.MeshPhysicalMaterial( {clearcoat:1, clearcoatRoughness:0.5, reflectivity:0, color:"gray"});
					
				for(var i = 0; i < thisQ; i++){
					var Tside1 = new THREE.Mesh( Trayside, TrayMaterial);
					var Tside2 = new THREE.Mesh( Trayside, TrayMaterial);
					var Tbottom = new THREE.Mesh( Traybottom, TrayMaterial);
					Tside1.position.x = (wide - thickness) / 2;
					Tside1.position.y = height/2;
					Tside2.position.x = (-wide + thickness) / 2;
					Tside2.position.y = height/2;
					
					Tbottom.position.set(  -100, ((height+margin) * hc) + 22 , (conWidth + 100 )/ 2 * -1  );
					

					Tbottom.add( Tside1 );
					Tbottom.add( Tside2 );
					boxGroup.add( Tbottom );

					wc++;
					if( wc == maxWcount ){ wc = 0; hc++ }
					if( hc == maxHcount ){ hc = 0; lc++ }
				}
}

str();

function animate() {

requestAnimationFrame( animate );

renderer.render( scene, camera );

}

$("canvas").css("display","none");

animate();


</script>
	
