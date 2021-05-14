<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="author" content="<?php echo $config['author'];?>">
		<meta name="keywords" content="<?php echo $config['keywords'];?>">
		<link rel="stylesheet" href="/assets/css/main.css">
		<link rel="shortcut icon" href="/assets/img/H.png" type="image/x-icon">
		<title><?php echo strtoupper($config['page_title']);?> | HOME</title>
		<!-- SCRIPT START -->
			<script src="/assets/js/main.js"></script>
			<script src="https://kit.fontawesome.com/319cbe1b39.js" crossorigin="anonymous"></script>
		<!-- SCRIPT END -->
	</head>
    <body>

		<?php if($config['maintenance']):?>

		<div class="infobanner banner-info">
			<span class="closebtn" onclick="closeinfo()">&times;</span> 
			<strong>Info!</strong> Wartungsmodus!
		</div>
		
		<?php endif;?>

		<header>
			<div class="pageframe">
				<div class="container">
					<a href="/"><img src="/assets/img/H.png"></a>
					<a href="/"><span class="headerTitle">HATBE</span></a>
				</div>
				<div class="container">
				</div>
			</div>
		</header>
		<nav class="topnav" id="navtop">
			<div class="pageframe">
				<a class="navbtn" href="/"><span>Home</span></a>
				<a class="navbtn" href="/articles/list">Artikel</a>
				<a class="navbtn" href="/me/work">My Work</a>
				<a class="navbtn" href="/index/contact">Kontakt</a>
				<!--if(isset($_SESSION['login'])) {
				echo '<a class="active navbtn" href="/ai">AI</a>';
				}-->
				<a id="icon" class="icon" onclick="triggerNav()">
				<div class="hamburger"></div>
				<div class="hamburger"></div>
				<div class="hamburger"></div>
				</a>
			</div>
    	</nav>