<?include_once("../include/common.php");?>
<?include_once("../include/header.php");?>

<?
		$idx = $_REQUEST[idx];
		$type = array('ladder','solid','perfor','cover');
		$itemlist = array('str','hor','hor','hor','hor','hortee','horcro','ver_in','ver_in','ver_in','ver_in','ver_out','ver_out','ver_out','ver_out','vertee_up','vertee_down','wye','redstr','redleft','redright');
		$itemlist2 = array('STRAIGHT TRAY','90° HORIZONTAL ELBOW','60° HORIZONTAL ELBOW','45° HORIZONTAL ELBOW','30° HORIZONTAL ELBOW','HORIZONTAL TEE','HORIZONTAL CROSS','90° VERTICAL ELBOW INSIDE','60° VERTICAL ELBOW INSIDE','45° VERTICAL ELBOW INSIDE','30° VERTICAL ELBOW INSIDE','90° VERTICAL ELBOW OUTSIDE','60° VERTICAL ELBOW OUTSIDE','45° VERTICAL ELBOW OUTSIDE','30° VERTICAL ELBOW OUTSIDE','VERTICAL TEE UP TYPE','VERTICAL TEE DOWN TYPE','WYE','STRAIGHT REDUCER','LEFT HAND REDUCER','RIGHT HAND REDUCER');


		$Ntype = array();
		$Nitemlist = array();
		$wide = array();$w = array();
		$height = array();$h = array();
		$length = array();$l = array();
		$radius = array();$r = array();
		$deg = array();$d = array();
		$quantity = array();$q = array();

		for($a=0;$a<count($type);$a++){

		$DBcntA = "select count(*) as cnt from `PJ_ITEMLIST` where idx = '$idx' and type = '$type[$a]'";
		$DBcntsqlA = $pdo->query($DBcntA);
		$totalcntA = $DBcntsqlA->fetchColumn();

			if($totalcntA != 0){
				
				array_push($Ntype, strtoupper($type[$a]));
				
				for($b=0;$b<count($itemlist);$b++){
					
					$DBcntB = "select count(*) as cnt from `PJ_ITEMLIST` where idx = '$idx' and type = '$type[$a]' and itemlist = '$itemlist[$b]'";
					if($b == 1 || $b == 7 || $b == 11){$DBcntB .= " and deg = '90'";}
					else if($b == 2 || $b == 8 || $b == 12){$DBcntB .= " and deg = '60'";}
					else if($b == 3 || $b == 9 || $b == 13){$DBcntB .= " and deg = '45'";}
					else if($b == 4 || $b == 10 || $b == 14){$DBcntB .= " and deg = '30'";}
					$DBcntsqlB = $pdo->query($DBcntB);
					$totalcntB = $DBcntsqlB->fetchColumn();

					if($totalcntB != 0){
						
						array_push($Nitemlist, $itemlist2[$b]);
						

						$DBdata = "select * from `PJ_ITEMLIST` where idx = '$idx' and type = '$type[$a]' and itemlist = '$itemlist[$b]'";
						if($b == 1 || $b == 7 || $b == 11){$DBdata .= " and deg = '90'";}
						else if($b == 2 || $b == 8 || $b == 12){$DBdata .= " and deg = '60'";}
						else if($b == 3 || $b == 9 || $b == 13){$DBdata .= " and deg = '45'";}
						else if($b == 4 || $b == 10 || $b == 14){$DBdata .= " and deg = '30'";}
						$DBdatasql = $pdo -> prepare($DBdata);
						$DBdatasql -> execute();
						$DBdatasql -> setFetchMode(PDO::FETCH_ASSOC);
						$DBdatarow = $DBdatasql->fetchAll();
						
						//print_r($DBdatarow);

						for($c = 0 ; $c < count($DBdatarow) ; $c++){
							array_push($w,$DBdatarow[$c]['WIDE']);
							array_push($h,$DBdatarow[$c]['HEIGHT']);
							array_push($l,$DBdatarow[$c]['STR_LENGTH']);
							array_push($r,$DBdatarow[$c]['RADIUS']);
							array_push($d,$DBdatarow[$c]['DEG']);
							array_push($q,$DBdatarow[$c]['QUANTITY']);
							
						}

						array_push($wide,$w);$w = array();
						array_push($height,$h);$h = array();
						array_push($length,$l);$l = array();
						array_push($radius,$r);$r = array();
						array_push($deg,$d);$d = array();
						array_push($quantity,$q);$q = array();
						

					}
					


				}

			}
		}
		
		
		/*print_r($Ntype);
		print "<br><br>";
		print_r($Nitemlist);
		print "<br><br>";
		print_r($wide);
		print "<br><br>";
		print_r($height);
		print "<br><br>";
		print_r($length);
		print "<br><br>";
		print_r($radius);
		print "<br><br>";
		print_r($deg);
		print "<br><br>";*/



?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<link type="text/css" rel="stylesheet" href="threemain.css">
		<style>
			body {
				color: #000;
				text-align: center;
				margin: 0 auto;
				font-family: arial;
				
			}
			canvas{ position:absolute; left:0; top:15%;}
			.menu { position:relative; color: #fff; font-weight: bold; font-size: 20px; left:100%; z-index: 100; width: 40%; padding: 16px;}
			.menu tr, td { font-size:20px; color: white; }
			#labels { background: rgba(0,0,0,0.75); text-align:left;}
			
		</style>
	</head>
	<body>

		<div id="main">
			
			<div id="labels" class="menu">
				<table id="infoTable" style="text-align:center;">
					<tr><td>Type</td>
						<td>
							<select id="type" class="TrayValue">
								<?for($a=0; $a < count($Ntype); $a++){?>
								<option value="<?=$Ntype[$a]?>"><?=$Ntype[$a]?></option>
								<?}?>
							</select>
						</td>
					<tr><td>Name</td>
						<td>
							<select id="name" class="TrayValue">
								<?for($a=0; $a < count($Nitemlist); $a++){?>
								<option value="<?=$Nitemlist[$a]?>"><?=$Nitemlist[$a]?></option>
								<?}?>
							</select>
						</td>
					</tr>
					<tr><td>Wide</td>
						<td>
							<select id="wide" class="TrayValue">
								<?for($a=0; $a < count($wide[0]); $a++){?>
								<option value="<?=$wide[0][$a]?>"><?=$wide[0][$a]?></option>
								<?}?>
								<!--<option value=300>300</option>-->
							</select>
						</td>
					</tr>
					<tr><td>Height</td>
						<td>
							<select id="height" class="TrayValue">
								<?for($a=0; $a < count($height[0]); $a++){?>
								<option value="<?=$height[0][$a]?>"><?=$height[0][$a]?></option>
								<?}?> 
								<!--<option value=100>100</option>-->
							</select>
						</td>
					</tr>
					<tr><td>Legnth</td>
						<td>
							<select id="length" class="TrayValue">
								 <?for($a=0; $a < count($length[0]); $a++){?>
								<option value="<?=$length[0][$a]?>"><?=$length[0][$a]?></option>
								<?}?>
								<!--<option value=3000>3000</option> -->
							</select>
						</td>
					</tr>
					<tr><td>Radius</td>
						<td>
							<select id="radius" class="TrayValue">
								<?for($a=0; $a < count($radius[0]); $a++){?>
								<option value="<?=$radius[0][$a]?>"><?=$radius[0][$a]?></option>
								<?}?> 
								<!-- <option value=300>300</option> -->
							</select>
						</td>
					</tr>
					
						<tr><td>Weight</td>
							<td>
								<p id="weight">
									
								</p>
							</td>
						</tr>
						<tr><td>Quantity</td>
							<td>
								<p id="quantity"><?=$quantity[0][0]?></p>
							</td>
						</tr>
					</table>
				</div>
			</div>


		

		<script type="module">

			import * as THREE from 'https://rawgit.com/mrdoob/three.js/master/build/three.module.js';
			
			import { DDSLoader } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/loaders/DDSLoader.js';
			import { TGALoader } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/loaders/TGALoader.js';
			import { MTLLoader } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/loaders/MTLLoader.js';
			import { OBJLoader } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/loaders/OBJLoader.js';
			//import { OBJLoader2 } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/loaders/OBJLoader2.js';
			import { OrbitControls } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/controls/OrbitControls.js';
			import { Reflector } from 'https://rawgit.com/mrdoob/three.js/master/examples/jsm/objects/Reflector.js';


			//////////////////////////////////////// DB정보값 /////////////////////////////////////////////////////////////////////////
			
			var DBname = <?php echo json_encode($Nitemlist)?>;
			var DBwide = <?php echo json_encode($wide)?>; 
			var DBheight = <?php echo json_encode($height)?>; 
			var DBlength = <?php echo json_encode($length)?>;
			var DBradius = <?php echo json_encode($radius)?>; 
			//var DBdeg = <?php echo json_encode($deg)?>; 
			var DBquantity = <?php echo json_encode($quantity)?>; 

			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			var type = $("#type").val();
			var name = $("#name").val();
			var wide = $("#wide").val() / 10;
			var height = $("#height").val() / 10;
			var length = $("#length").val() / 10;
			var radius = $("#radius").val() / 10;
			//var angle = 0;//$("#deg").val();

			var weight = weight;
			var quantity = DBquantity[0];
			var conWidth = 245;
			var conHeight = 265;
			var conLength = 600;
			var conCount = 1;

			var maxWcount, maxHcount, maxLcount;

			// scene size
			var WIDTH = window.innerWidth;
			var HEIGHT = window.innerHeight-200;

			// camera
			var VIEW_ANGLE = 45;
			var ASPECT = WIDTH / HEIGHT;
			var NEAR = 1;
			var FAR = 5000;

			var camera, scene, renderer;

			var cameraControls;

			var sphereGroup, smallSphere;

			init();
			animate();

			function init() {
				var container = document.createElement( 'main' );
				document.body.appendChild( container );

				// renderer
				renderer = new THREE.WebGLRenderer( { antialias: true } );
				renderer.setPixelRatio( window.devicePixelRatio );
				renderer.setSize( WIDTH, HEIGHT );
				container.appendChild( renderer.domElement );

				
				
				// scene
				scene = new THREE.Scene();
				scene.background = new THREE.Color( 0xcce0ff );

				// camera
				camera = new THREE.PerspectiveCamera( VIEW_ANGLE, ASPECT, NEAR, FAR );
				camera.position.set( 0, 75, 5000 );

				cameraControls = new OrbitControls( camera, renderer.domElement );
				cameraControls.target.set( 0, 100, 0 );
				cameraControls.maxDistance = 700;
				cameraControls.minDistance = 100;
				cameraControls.maxPolarAngle = Math.PI / 2;
				cameraControls.update();
				
				/////////////////////////////////////////////////////////////////////////////////////////////
				////////////                       Container Box                  /////////////

				var planeGeo1 = new THREE.PlaneBufferGeometry( conWidth + 0.1, conHeight + 0.1 );
				var planeGeo2 = new THREE.PlaneBufferGeometry( conLength + 0.1, conHeight + 0.1 );
				var planeGeo3 = new THREE.PlaneBufferGeometry( conWidth + 0.1, conLength + 0.1 );

				
				var geometry = new THREE.PlaneBufferGeometry( 100, 100 );


				var geometry = new THREE.CylinderBufferGeometry( 0.1, 15 * Math.cos( Math.PI / 180 * 30 ), 0.1, 24, 1 );
				var material = new THREE.MeshPhongMaterial( { color: 0xfff000, emissive: 0x444444 } );
				var sphereCap = new THREE.Mesh( geometry, material );
				sphereCap.position.y = - 15 * Math.sin( Math.PI / 180 * 30 ) - 0.05;
				sphereCap.rotateX( - Math.PI );

				
				// walls
				var planeTop = new THREE.Mesh( planeGeo3, new THREE.MeshPhongMaterial( { map:new THREE.TextureLoader().load( 'ContT.png' ),color: 0xffbebe } ) );
				planeTop.position.y = 265;
				planeTop.rotateX( Math.PI / 2 );
				scene.add( planeTop );

				var planeBottom = new THREE.Mesh( planeGeo3, new THREE.MeshPhongMaterial( { map:new THREE.TextureLoader().load( 'ContT.png' ),color: 0xffbebe } ) );
				planeBottom.position.y = 0.2;
				planeBottom.rotateX( - Math.PI / 2 );
				scene.add( planeBottom );

				var planeFront = new THREE.Mesh( planeGeo1, new THREE.MeshPhongMaterial( { map:new THREE.TextureLoader().load( 'ContF.png' ),color: 0xffbebe } ) );
				planeFront.position.z = 300;
				planeFront.position.y = 132.5;
				planeFront.rotateY( Math.PI );
				scene.add( planeFront );

				var planeBack = new THREE.Mesh( planeGeo1, new THREE.MeshPhongMaterial( { map:new THREE.TextureLoader().load( 'ContF.png' ),color: 0xffbebe } ) );
				planeBack.position.z = -300;
				planeBack.position.y = 132.5;
				planeBack.rotateY(  Math.PI * 2 );
				scene.add( planeBack );

				var planeRight = new THREE.Mesh( planeGeo2, new THREE.MeshPhongMaterial( { map:new THREE.TextureLoader().load( 'ContS.png' ),color: 0xffbebe } ) );
				planeRight.position.x = 122.5;
				planeRight.position.y = 132.5;
				planeRight.rotateY( - Math.PI / 2 );
				scene.add( planeRight );

				var planeLeft = new THREE.Mesh( planeGeo2, new THREE.MeshPhongMaterial( { map:new THREE.TextureLoader().load( 'ContS.png' ),color: 0xffbebe } ) );
				planeLeft.position.x = - 122.5;
				planeLeft.position.y = 132.5;
				planeLeft.rotateY( Math.PI / 2 );
				scene.add( planeLeft );


				////////////////////////////////////////////////////////////////////////////////////////
				////////////////////                     lights                  ////////////////////////
				scene.add( new THREE.AmbientLight( 0x222222 ) );

				var light = new THREE.SpotLight( 0xffffff, 5, 1000 );
				light.position.set( 300, 800, 200 );
				light.angle = 0.5;
				light.penumbra = 0.5;

				light.castShadow = true;
				light.shadow.mapSize.width = 1024;
				light.shadow.mapSize.height = 1024;

				// scene.add( new CameraHelper( light.shadow.camera ) );
				scene.add( light );

				var mainLight = new THREE.PointLight( 0xcccccc, 1.25, 1000 );
				mainLight.position.y = 132.5;
				scene.add( mainLight );
				
				var greenLight = new THREE.PointLight( 0xffffff, 0.5, 500 );
				greenLight.position.set( 122.5, 132.5, 0 );
				scene.add( greenLight );

				var redLight = new THREE.PointLight( 0xffffff, 0.5, 500 );
				redLight.position.set( - 122.5, 132.5, 0 );
				scene.add( redLight );

				var blueLight = new THREE.PointLight( 0xffffff, 0.5, 500 );
				blueLight.position.set( 0, 132.5, -300 );
				scene.add( blueLight );

				var YellowLight = new THREE.PointLight( 0xffffff, 0.5, 500 );
				YellowLight.position.set( 0, 132.5, 300 );
				scene.add( YellowLight );

				//////////////////////////////////////////////////////////////////////////////////////////
				////////////                       Wood palette                  /////////////
				
				var pa_topGeo = new THREE.BoxBufferGeometry( 245,2,600 );
				var pa_boGeo = new THREE.BoxBufferGeometry( 20,20,600 );

				var paMaterial = new THREE.MeshBasicMaterial( { map:new THREE.TextureLoader().load( 'wood.png' )} );

				var pa_top = new THREE.Mesh( pa_topGeo, paMaterial);
				pa_top.position.set( 0, 20, 0 );
				scene.add(pa_top);

				var pa_bottom1 = new THREE.Mesh( pa_boGeo, paMaterial);
				var pa_bottom2 = new THREE.Mesh( pa_boGeo, paMaterial);
				var pa_bottom3 = new THREE.Mesh( pa_boGeo, paMaterial);
				pa_bottom1.position.set( -112, 10, 0 );
				pa_bottom2.position.set( 0, 10, 0 );
				pa_bottom3.position.set( 112, 10, 0 );
				scene.add(pa_bottom1);
				scene.add(pa_bottom2);
				scene.add(pa_bottom3);


				////////////////////////////////////////////////////////////////////////////////////////////
				////////////                       Cable Tray                  /////////////
				var margin = 2;       //여유공간
				var thickness = 1.6;    //두께
				maxWcount = Math.floor(conWidth / (wide+margin));     //폭 최대 개수
				maxHcount = Math.floor((conHeight-30) / (height+margin));  //높이 최대 개수
				maxLcount = Math.floor(conLength / length);  //길이 최대 개수
				var conMaxC = maxWcount * maxHcount * maxLcount; //한 컨테이너 최대 개수
				conCount = Math.ceil(quantity / conMaxC);        //총 컨테이너 개수
				var thisQ = quantity - (conMaxC * (conCount-1)); //현재 컨테이너의 트레이 개수
				var conMargin = conWidth - (maxWcount * (wide+margin)); //컨테이너 남은 넓이
				var wc = 0, hc = 0, lc = 0;

				console.log(maxWcount, maxHcount, maxLcount, conMaxC, conCount, thisQ);

				var boxGroup = new THREE.Object3D();
				scene.add( boxGroup );
	
				var Trayside = new THREE.BoxBufferGeometry( thickness, height, length-0.1);
				var Traybottom = new THREE.BoxBufferGeometry( wide, thickness, length-0.1);
				var TrayMaterial = new THREE.MeshPhysicalMaterial( {clearcoat:1, clearcoatRoughness:0.5, reflectivity:0, color:0xa1a1a1});
					
				for(var i = 0; i < thisQ; i++){
					var Tside1 = new THREE.Mesh( Trayside, TrayMaterial);
					var Tside2 = new THREE.Mesh( Trayside, TrayMaterial);
					var Tbottom = new THREE.Mesh( Traybottom, TrayMaterial);
					Tside1.position.x = (wide - thickness) / 2;
					Tside1.position.y = height/2;
					Tside2.position.x = (-wide + thickness) / 2;
					Tside2.position.y = height/2;
					
					Tbottom.position.set( ((wide+margin) * wc) + (wide/2) - 122.5 + (conMargin/2), ((height+margin) * hc) + 22 , (length * lc) + ((-length/2) * (maxLcount-1)) );
					

					Tbottom.add( Tside1 );
					Tbottom.add( Tside2 );
					boxGroup.add( Tbottom );

					wc++;
					if( wc == maxWcount ){ wc = 0; hc++ }
					if( hc == maxHcount ){ hc = 0; lc++ }
				}

				

				//////////////////////////////////////////////////////////////////////////////////////////
				window.addEventListener( 'resize', onWindowResize, false );
				
			}


			function onWindowResize() {

				renderer.setSize( WIDTH, HEIGHT );

				camera.aspect = WIDTH / HEIGHT;
				camera.updateProjectionMatrix();

				renderer.setSize( window.innerWidth, window.innerHeight-200 );

			}


			function animate() {

				requestAnimationFrame( animate );
				
				renderer.render( scene, camera );

			}
			
			$(function(){
				var name2 = DBname[0];

				$(document).on('change', '.TrayValue', function(){
					
					var name = $("#name").val();
					var DBcnt = DBname.indexOf(name);
					
					if(name != name2){
						
						$("#wide option").remove();
						$("#height option").remove();
						$("#length option").remove();
						$("#radius option").remove();

						for(var a = 0 ; a < DBwide[DBcnt].length ; a++){
							$('#wide').append("<option value="+DBwide[DBcnt][a]+">"+DBwide[DBcnt][a]+"</option>");
						}
						for(var a = 0 ; a < DBheight[DBcnt].length ; a++){
							$('#height').append("<option value="+DBheight[DBcnt][a]+">"+DBheight[DBcnt][a]+"</option>");
						}
						for(var a = 0 ; a < DBlength[DBcnt].length ; a++){
							$('#length').append("<option value="+DBlength[DBcnt][a]+">"+DBlength[DBcnt][a]+"</option>");
						}
						for(var a = 0 ; a < DBradius[DBcnt].length ; a++){
							$('#radius').append("<option value="+DBradius[DBcnt][a]+">"+DBradius[DBcnt][a]+"</option>");
						}

						name2 = name;
					}
					
					

					var W = $("#wide").val() / 10;
					var H = $("#height").val() / 10;
					var L = $("#length").val() / 10;
					var R = $("#radius").val() / 10;
					var S = 12.5;
					quantity = DBquantity[DBcnt];
		
			
					if(name ==  'STRAIGHT TRAY'){
						wide = W;
						height = H;
						length = L;
						radius = R;
					}

					if(name ==  '90° HORIZONTAL ELBOW'){
						wide = R + W + S + S;
						height = H;
						length = R + W + S + S;
						radius = R;
					}

					if(name ==  '60° HORIZONTAL ELBOW'){
						wide = R + W + S + S;
						height = H;
						length = R + W + S + S;
						radius = R;
					}

					if(name ==  '45° HORIZONTAL ELBOW'){
						wide = R + W + S + S;
						height = H;
						length = R + W + S + S;
						radius = R;
					}

					if(name ==  '30° HORIZONTAL ELBOW'){
						wide = R + W + S + S;
						height = H;
						length = R + W + S + S;
						radius = R;
					}

					if(name ==  'HORIZONTAL TEE'){
						wide = W + R + S;
						height = H;
						length = W + R + R + S + S;
						radius = R;
					}

					if(name ==  'HORIZONTAL CROSS'){
						wide = W + R + R + S + S;
						height = H;
						length = W + R + R + S + S;
						radius = R;
					}

					if(name ==  '90° VERTICAL ELBOW INSIDE'){
						wide = Math.sqrt(2) * (R + S);
						height = W;
						length = Math.sqrt(2) * (R + S);
						radius = R;
					}

					if(name ==  '60° VERTICAL ELBOW INSIDE'){
						wide = Math.sqrt(2) * (R + S);
						height = W;
						length = Math.sqrt(2) * (R + S);
						radius = R;
					}

					if(name ==  '45° VERTICAL ELBOW INSIDE'){
						wide = Math.sqrt(2) * (R + S);
						height = W;
						length = Math.sqrt(2) * (R + S);
						radius = R;
					}

					if(name ==  '30° VERTICAL ELBOW INSIDE'){
						wide = Math.sqrt(2) * (R + S);
						height = W;
						length = Math.sqrt(2) * (R + S);
						radius = R;
					}

					if(name ==  '90° VERTICAL ELBOW OUTSIDE'){
						wide = Math.sqrt(2) * (R + S);
						height = W;
						length = Math.sqrt(2) * (R + S);
						radius = R;
					}

					if(name ==  '60° VERTICAL ELBOW OUTSIDE'){
						wide = Math.sqrt(2) * (R + S);
						height = W;
						length = Math.sqrt(2) * (R + S);
						radius = R;
					}

					if(name ==  '45° VERTICAL ELBOW OUTSIDE'){
						wide = Math.sqrt(2) * (R + S);
						height = W;
						length = Math.sqrt(2) * (R + S);
						radius = R;
					}

					if(name ==  '30° VERTICAL ELBOW OUTSIDE'){
						wide = Math.sqrt(2) * (R + S);
						height = W;
						length = Math.sqrt(2) * (R + S);
						radius = R;
					}

					if(name ==  'VERTICAL TEE UP TYPE'){
						wide = W;
						height = R + S + H;
						length = R + R + S + S + H;
						radius = R;
					}

					if(name ==  'VERTICAL TEE DOWN TYPE'){
						wide = W;
						height = R + S + H;
						length = R + R + S + S + H;
						radius = R;
					}


					if(name ==  'STRAIGHT REDUCER'){
						wide = W;
						height = H;
						length = 20 + S + S;
						radius = R;
					}

					if(name ==  'LEFT HAND REDUCER'){
						wide = W;
						height = H;
						length = 20 + S + S;
						radius = R;
					}

					if(name ==  'RIGHT HAND REDUCER'){
						wide = W;
						height = H;
						length = 20 + S + S;
						radius = R;
					}
					


					

					$("#weight").text(weight);
					$("#quantity").text(quantity);
					
					init();
					animate();
		
					
					

					
				});

				
			});

			

			
		</script>
	</body>
</html> 

