<?php
	include('session.php');

	$ScriptKaust=$kl->getScriptKaust();
	$request = str_replace($ScriptKaust, "/",$_SERVER['REQUEST_URI']);
	$request = substr($request, 1, strlen($request));
	$req = explode('/', $request);

?>

<!doctype html>
<html lang="en">
  <head>
	<base href="<?php echo 'http://localhost'.$ScriptKaust;?>" />
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

	<!--JavaScript-->
	<script src="js/jquery-3.3.1.min.js"></script>
	<script src="css/jquery-ui.min.js"></script>
	<script src="js/datepicker-et.js"></script>
	
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
	<link rel="stylesheet" href="css/jquery-ui.min.css">
	<link rel="stylesheet" href="css/MyStyle.css">

    <title>Project</title>

	<script>
		window.setTimeout(function(){
			$(".myalert").fadeTo(500,0).slideUp(500,function(){
				$(this).remove();
			});
		}, 4000);
	</script>

	<body>
		<div class="w-100 py-5">
			<nav class="navbar bg-light fixed-top">
				<div class="container-fluid">
					<a class="navbar-brand" href="<?php echo $ScriptKaust;?>">Project</a>
					<div class="d-flex">
						<?php 
							if ($session->isLogged()) { 
								?>
									<form class="d-flex" method="post" action="process.php">
										<button class="btn btn-outline-danger" type="submit">Log out</button>
									</form>
								<?php

							} else {
								?>
									<form class="d-flex me-2" method="post" action="<?php echo $ScriptKaust.'login'; ?>">
										<button class="btn btn-outline-success" type="submit">Login</button>
									</form>
									<form class="d-flex" method="post" action="<?php echo $ScriptKaust.'register'; ?>">
										<button class="btn btn-outline-primary" type="submit">Register</button>
									</form>
								<?php 
							}
						?>
					</div>
				</div>
			</nav>

			<div class="container py-5">

				<div class="col-md-12 py-5 border border-light bg-light rounded rounded-3">
					<!-- Siin hakkab näitama lehtede sisu -->

					<?php
						if(isset($_SESSION['error']) && !empty($_SESSION['error']['alert'])){
							?>
								<div class="fixed-top p-5 myalert">
									<?php
										if($_SESSION['error']['success']){
											?>
												<div class="mt-2 alert alert-success text-center"><?php echo $_SESSION['error']['alert'];?></div>
											<?php
										}else{
											?>
												<div class="mt-2 alert alert-danger text-center"><?php echo $_SESSION['error']['alert'];?></div>
											<?php
										}

										unset($_SESSION['error']);
									?>
								</div>
							<?php	
						}
					?>
						
					<?php
						if(!empty($req[0]) and $req[0] !='index'){
							#Seega req[0] on failinimi ilma php'ta
							$file = $req[0].'.php';
							if(file_exists($file) and is_file($file)){
								# Fail on, seega laeme
								require_once($file);
							} else {
								# Faili ei leitud, näita infot
								?>
									<p><strong>Fail <?php echo $file;?></strong> ei leitud</p>
								<?php
							}
						} else {
							# Põhileht
							if ($session->isLogged()) {
								?>	
									
									<h3 class="px-3">Sinu kasutaja nimi on : <span class="text-primary fw-bold"><?php echo $_SESSION['fullname'];?></span></h3>
								<?php
							}
						}
					?>
					
				</div>
			</div>

			<footer class="d-flex fixed-bottom flex-wrap justify-content-between align-items-center py-3 my-4 border-top">
				<div class="col-md-4 ms-5 d-flex align-items-center">
					<span class="mb-3 mb-md-0 text-body-secondary">© 2023 Company, Inc</span>
				</div>
			</footer>
		</div>
  </head>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>