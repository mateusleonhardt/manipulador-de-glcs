<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pt-br" lang="pt-br">
	<head>
		<meta name="robots" content="noindex, nofollow"/>
		<?php 
			$page = basename($_SERVER['PHP_SELF']); 
		?>
		
		<meta charset="utf-8" />
		<title><?php echo (isset($title)) ? $title : 'Manipulador'?></title>
			<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

			<!-- FAVICON -->
			<link rel="icon" href="favicon.ico">

			<!-- CSS -->
			<link rel="stylesheet" type="text/css" href="/manipulador-de-glcs/css/style.css"/>
			<link rel="stylesheet" type="text/css" href="/manipulador-de-glcs/css/bootstrap.css"/>
			<link rel="stylesheet" type="text/css" href="/manipulador-de-glcs/css/bootstrap-theme.min.css"/>						

			<!-- JS -->
			<script type="text/javascript" src="/manipulador-de-glcs/js/jquery-1.11.1.min.js"></script>
			<script type="text/javascript" src="/manipulador-de-glcs/js/bootstrap.min.js"></script>
			<script type="text/javascript" src="/manipulador-de-glcs/js/script.js"></script>
			<script type="text/javascript" src="/manipulador-de-glcs/js/jquery.mask.min.js"></script>
	</head>

	<body>
		<?php
		if($page != '404page.php')
		{
		?>

			<!-- Header -->
			<div class="page-header">
				<div class="container">
	  				<h1></h1>
	  			</div>
			</div>
		
			<!-- Navigation -->
			<div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
				<div class="container">
				  <div class="container-fluid">
				    <!-- Brand and toggle get grouped for better mobile display -->
				    <div class="navbar-header">
				    	<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
        					<span class="sr-only">Toggle navigation</span>
        					<span class="icon-bar"></span>
        					<span class="icon-bar"></span>
        					<span class="icon-bar"></span>
      					</button>
				    </div>

				    <!-- Collect the nav links, forms, and other content for toggling -->
				    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
				        <ul class="nav navbar-nav">
				        	<li class="<?= ($page == "gera-gramatica.php" ? "active" : "")?>"><a href="/manipulador-de-glcs/gera-gramatica">Gerar Gramática</a></li>
				      	</ul>
				      	<form class="navbar-form navbar-right" action="buscar" method="get" role="search">
				        	<div class="form-group">
					         	<input type="text" class="form-control" name="k" placeholder="Buscar...">
					        </div>
					        <button type="submit" class="btn btn-primary">Buscar</button>
				      	</form>		      
				    </div>
				  </div>
				</div>
			</div>
			<div class="container">
				<h2><?= $title ?></h2>
		<?php
		}
		?>