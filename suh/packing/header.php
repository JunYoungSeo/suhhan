<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Auto</title>
		<link href="<?=SUH_PATH?>/css/reset.css" rel="stylesheet" media="screen" type="text/css" />
		<link href="<?=SUH_PATH?>/css/style.css" rel="stylesheet" media="screen" type="text/css" />
		<link href="<?=SUH_PATH?>/css/popup.css" rel="stylesheet" media="screen" type="text/css" />
		<link href="<?=SUH_PATH?>/css/layerpopup.css" rel="stylesheet" media="screen" type="text/css" />
		<link href="<?=SUH_PATH?>/css/tab_menu.css" rel="stylesheet" media="screen" type="text/css" />
		<link href="<?=SUH_PATH?>/css/quo.css" rel="stylesheet" media="screen" type="text/css" />
		<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
		<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
		<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
	</head>
	<body>

	<div id="header">
		<?
			session_start();
			$user_id = $_SESSION['id'];
		?>
		<div class="header-wrap">
			<h1 class="logo"><a href="<?=SUH_PATH?>/info.php"><img src="<?=SUH_PATH?>/images/logo.jpg" alt="suhhani" style="width:42%;"/></a></h1>
				<ul class="login_info">
					<li><strong> <?=$user_id?> 님</strong> 안녕하세요</li>
					<li><a href="<?=SUH_PATH?>/mypage.php">마이페이지</a></li>
					<li><a href="<?=SUH_PATH?>/login_out.php">로그아웃</a></li>
				</ul>
		</div>

		<div class="gnb">
			<ul>
				<li><a href="https://www.office.com/?auth=2&home=1" target="_blank">OFFICE 365</a></li>
				<li><a href="<?=SUH_PATH?>/info.php">Quotation</a></li>
				<li><a href="<?=SUH_PATH?>/Groupware/">Groupware</a></li>
				<li><a href="<?=SUH_PATH?>/pl/">Robot Analysis</a></li>
				<li><a href="https://newsstand.naver.com/?list=ct2" target="_blank">Newspaper</a></li>
				<li><a href="<?=SUH_PATH?>/history.php">History</a></li>
				<li><a href="<?=SUH_PATH?>/software/swk/main.php" target="_blank">Application software</a></li>
			</ul>
		</div>
	</div>  
	
	<?=write_log();?>

<script>
	$(function(){
		var path = "<?=$_SERVER['SCRIPT_FILENAME']?>";
		

		if(path == "/www/sh1920.godohosting.com/suh/info.php"){
			$("#header > div.gnb > ul > li:nth-child(2) > a").css("color","blue");
		}else if(path == "/www/sh1920.godohosting.com/suh/pj_set.php"){
			$("#header > div.gnb > ul > li:nth-child(2) > a").css("color","blue");
		}else if(path == "/www/sh1920.godohosting.com/suh/pj.php"){
			$("#header > div.gnb > ul > li:nth-child(2) > a").css("color","blue");
		}
		
		
		else if(path == "/www/sh1920.godohosting.com/suh/Groupware/index.php"){
			$("#header > div.gnb > ul > li:nth-child(3) > a").css("color","blue");
		}
		
		
		else if(path == "/www/sh1920.godohosting.com/suh/pl/index.php"){
			$("#header > div.gnb > ul > li:nth-child(4) > a").css("color","blue");
		}
		
		
		else if(path == "/www/sh1920.godohosting.com/suh/history.php"){
			$("#header > div.gnb > ul > li:nth-child(6) > a").css("color","blue");
		}else if(path == "/www/sh1920.godohosting.com/suh/project_history.php"){
			$("#header > div.gnb > ul > li:nth-child(6) > a").css("color","blue");
		}
		
		
		
		else if(path == "/www/sh1920.godohosting.com/suh/software.php"){
			$("#header > div.gnb > ul > li:nth-child(7) > a").css("color","blue");
		}else if(path == "/www/sh1920.godohosting.com/suh/software/test.php"){
			$("#header > div.gnb > ul > li:nth-child(7) > a").css("color","blue");
		}else if(path == "/www/sh1920.godohosting.com/suh/software/test2.php"){
			$("#header > div.gnb > ul > li:nth-child(7) > a").css("color","blue");
		}


		

	})
</script>