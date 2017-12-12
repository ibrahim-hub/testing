<!doctype html>
<html class="fixed">
	<head>

		<!-- Basic -->
		<meta charset="UTF-8">

		<title>Hanafi Admin</title>
		<meta name="keywords" content="HTML5 Admin Template" />
		<meta name="description" content="Porto Admin - Responsive HTML5 Template">
		<meta name="author" content="okler.net">

		<!-- Mobile Metas -->
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

		<!-- Web Fonts  -->
		<link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

        <?php
            
           
                /*If ENGLISH*/
                header('Content-language: en');

                echo '<link href="'.base_url().'assets/vendor/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css" />';
                echo '<link href="'.base_url().'ssets/vendor/font-awesome/css/font-awesome.css" rel="stylesheet" type="text/css" />';
                echo '<link href="'.base_url().'assets/vendor/magnific-popup/magnific-popup.css" rel="stylesheet" type="text/css" />';
                echo '<link href="'.base_url().'assets/vendor/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" />';
				
				/*Specific Page Vendor CSS*/
				echo '<link href="'.base_url().'assets/vendor/jquery-ui/css/ui-lightness/jquery-ui-1.10.4.custom.css" rel="stylesheet" type="text/css" />';
				echo '<link href="'.base_url().'assets/vendor/bootstrap-multiselect/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />';
				echo '<link href="'.base_url().'assets/vendor/morris/morris.css" rel="stylesheet" type="text/css" />';
				
				/* Specific Page Vendor CSS */
				echo '<link href="'.base_url().'assets/vendor/select2/select2.css" rel="stylesheet" type="text/css" />';
				echo '<link href="'.base_url().'assets/vendor/jquery-datatables-bs3/assets/css/datatables.css" rel="stylesheet" type="text/css" />';
				
				echo '<link href="'.base_url().'assets/stylesheets/theme.css" rel="stylesheet" type="text/css" />';
				echo '<link href="'.base_url().'assets/stylesheets/skins/default.css" rel="stylesheet" type="text/css" />';
				echo '<link href="'.base_url().'assets/stylesheets/theme-custom.css" rel="stylesheet" type="text/css" />';
				echo '<link href="'.base_url().'assets/vendor/modernizr/modernizr.js" rel="stylesheet" type="text/css" />';
        ?>
	</head>
	<body>
		<section class="body">

			<!-- start: header -->
			<header class="header">
				<div class="logo-container">
					<a href="" class="logo" style="    color: #171717;
    font-size: 19px;
    font-weight: bold;">
						<img src="http://hanafirestaurant.com/wp-content/uploads/thegem-logos/logo_87f119ccce3e4affcdeb5405627c2bdb_1x.png" height="35" alt="Porto Admin" /> HANAFI RESTAURANT
					</a>
					<div class="visible-xs toggle-sidebar-left" data-toggle-class="sidebar-left-opened" data-target="html" data-fire-event="sidebar-left-opened">
						<i class="fa fa-bars" aria-label="Toggle sidebar"></i>
					</div>
				</div>
			
				<!-- start: search & user box -->
				<div class="header-right">
			
					
			
				
			
					<span class="separator"></span>
			
					<div id="userbox" class="userbox">
						<a href="#" data-toggle="dropdown">
							<figure class="profile-picture">
								<img src="http://hanafirestaurant.com/wp-content/uploads/thegem-logos/logo_87f119ccce3e4affcdeb5405627c2bdb_1x.png" alt="Joseph Doe" class="img-circle" data-lock-picture="assets/images/!logged-user.jpg" />
							</figure>
							<div class="profile-info" data-lock-name="hanafi" data-lock-email="Hanafi@hanafi.hanafi">
								<span class="name">HANAFI Restaurant</span>
								<span class="role">administrator</span>
							</div>
			
							<i class="fa custom-caret"></i>
						</a>
			
						<div class="dropdown-menu">
							<ul class="list-unstyled">
								<li class="divider"></li>
								
								<li>
									<a role="menuitem" tabindex="-1" href="signin.html"><i class="fa fa-power-off"></i> Logout</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
				<!-- end: search & user box -->
			</header>
			<!-- end: header -->