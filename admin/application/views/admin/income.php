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
								<img src="http://hanafirestaurant.com/wp-content/uploads/thegem-logos/logo_87Rent19ccce3e4affcdeb5405627c2bdb_1x.png" alt="Joseph Doe" class="img-circle" data-lock-picture="assets/images/!logged-user.jpg" />
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

			<div class="inner-wrapper">
				<!-- start: sidebar -->
				<aside id="sidebar-left" class="sidebar-left">
				
					<div class="sidebar-header">
						<div class="sidebar-title">
							Navigation
						</div>
						<div class="sidebar-toggle hidden-xs" data-toggle-class="sidebar-left-collapsed" data-target="html" data-fire-event="sidebar-left-toggle">
							<i class="fa fa-bars" aria-label="Toggle sidebar"></i>
						</div>
					</div>
				
					<div class="nano">
						<div class="nano-content">
							<nav id="menu" class="nav-main" role="navigation">
								<ul class="nav nav-main">
									<li class="nav-active">
										<a href="index.html">
											<i class="fa fa-home" aria-hidden="true"></i>
											<span>Dashboard</span>
										</a>
									</li>
									<li>
										<a href="bill.html">
											
											<i class="fa fa-envelope" aria-hidden="true"></i>
											<span>Billing</span>
										</a>
									</li>
								<li>
								
								
								
										<a href="table.html">
											
											<i class="fa fa-envelope" aria-hidden="true"></i>
											<span>TABLES</span>
										</a>
									</li>
								
									
								
								
									<li class="nav-parent">
										<a>
											<i class="fa fa-align-left" aria-hidden="true"></i>
											<span>Menu</span>
										</a>
										<ul class="nav nav-children">
											<li>
												<a href="category.html">Category</a>
											</li>
											<li>
												<a href="products.html">Products</a>
											</li>
										
										</ul>
									</li>
									
									
										<li class="nav-parent">
										<a>
											<i class="fa fa-align-left" aria-hidden="true"></i>
											<span>Account</span>
										</a>
										<ul class="nav nav-children">
											<li>
												<a href="income.html">ADD INCOME</a>
											</li>
											<li>
												<a href="expence.html">ADD EXPENCE</a>
											</li>
										
										</ul>
									</li>
									
									
									
									<li>
										<a href="#" >
											<i class="fa fa-external-link" aria-hidden="true"></i>
											<span>Reports</span>
										</a>
									</li>
								</ul>
							</nav>
				
						
						</div>
				
					</div>
				
				</aside>
				<!-- end: sidebar -->
			<section role="main" class="content-body">
				<!-- start: page -->
					<div class="col-md-6">
							<form id="summary-form" action="forms-validation.html" class="form-horizontal" novalidate="novalidate">
								<section class="panel">
									<header class="panel-heading">
										

										<h2 class="panel-title">Add Income</h2>
										
									</header>
									<div class="panel-body">
										<div class="validation-message">
											<ul></ul>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Income<span class="required">*</span></label>
											<div class="col-sm-9">
												<input type="text" name="fullname" class="form-control" title="Plase enter a name." placeholder="eg.: John Doe" required="">
											</div>
										</div>	
										<div class="form-group">
											<label class="col-sm-3 control-label">Amount<span class="required">*</span></label>
											<div class="col-sm-9">
												<input type="text" name="fullname" class="form-control" title="Plase enter a name." placeholder="eg.: John Doe" required="">
											</div>
										</div>
										
										<div class="form-group">
												<label class="col-md-3 control-label" for="inputSuccess">Type</label>
												<div class="col-md-6">
													<select class="form-control  mb-md">
														<option>Cash</option>
														<option>Account</option>
														<option>Paytm</option>
													</select>
													
												</div>
											</div>
										
										<div class="form-group">
												<label class="col-md-3 control-label">Date</label>
												<div class="col-md-6">
													<div class="input-group">
														<span class="input-group-addon">
															<i class="fa fa-calendar"></i>
														</span>
														<input type="text" data-plugin-datepicker="" class="form-control">
													</div>
												</div>
											</div>
										
										
									</div>
									<footer class="panel-footer">
										<div class="row">
											<div class="col-sm-9 col-sm-offset-3">
												<button class="btn btn-primary">Submit</button>
												<button type="reset" class="btn btn-default">Reset</button>
											</div>
										</div>
									</footer>
								</section>
							</form>
						</div>	<div class="col-md-6">
						<section class="panel">
							<header class="panel-heading">
								<div class="panel-actions">
									<a href="#" class="fa fa-caret-down"></a>
									<a href="#" class="fa fa-times"></a>
								</div>
						
								<h2 class="panel-title">Income</h2>
							</header>
							<div class="panel-body">
								<table class="table table-bordered table-striped mb-none" id="datatable-default">
									<thead>
										<tr>
											<th>Income</th>
											<th>Amount</th>
											<th>Type</th>	
											<th>Date</th>
										
											
										</tr>
									</thead>
									<tbody>
										<tr class="gradeX">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
											
												
											
										</tr>
										<tr class="gradeC">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>12<td>
											<td>1200<td>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeC">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeX">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeC">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeC">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeA">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeX">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeX">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeX">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeC">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeC">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
										<tr class="gradeU">
											<td>Home Delevery</td>
											<td>900</td>
											<td>Cash</td> <td>07/11/2017</td>
										</tr>
									</tbody>
								</table>
							</div>
						</section>
						</div>
						
					<!-- end: page -->
				</section>
			</div>

			<aside id="sidebar-right" class="sidebar-right">
				<div class="nano">
					<div class="nano-content">
						<a href="#" class="mobile-close visible-xs">
							Collapse <i class="fa fa-chevron-right"></i>
						</a>
			
						<div class="sidebar-right-wrapper">
			
							<div class="sidebar-widget widget-calendar">
								<h6>Upcoming Tasks</h6>
								<div data-plugin-datepicker data-plugin-skin="dark" ></div>
			
								<ul>
									<li>
										<time datetime="2014-04-19T00:00+00:00">04/19/2014</time>
										<span>Company Meeting</span>
									</li>
								</ul>
							</div>
			
							<div class="sidebar-widget widget-friends">
								<h6>Friends</h6>
								<ul>
									<li class="status-online">
										<figure class="profile-picture">
											<img src="assets/images/!sample-user.jpg" alt="Joseph Doe" class="img-circle">
										</figure>
										<div class="profile-info">
											<span class="name">Joseph Doe Junior</span>
											<span class="title">Hey, how are you?</span>
										</div>
									</li>
									<li class="status-online">
										<figure class="profile-picture">
											<img src="assets/images/!sample-user.jpg" alt="Joseph Doe" class="img-circle">
										</figure>
										<div class="profile-info">
											<span class="name">Joseph Doe Junior</span>
											<span class="title">Hey, how are you?</span>
										</div>
									</li>
									<li class="status-offline">
										<figure class="profile-picture">
											<img src="assets/images/!sample-user.jpg" alt="Joseph Doe" class="img-circle">
										</figure>
										<div class="profile-info">
											<span class="name">Joseph Doe Junior</span>
											<span class="title">Hey, how are you?</span>
										</div>
									</li>
									<li class="status-offline">
										<figure class="profile-picture">
											<img src="assets/images/!sample-user.jpg" alt="Joseph Doe" class="img-circle">
										</figure>
										<div class="profile-info">
											<span class="name">Joseph Doe Junior</span>
											<span class="title">Hey, how are you?</span>
										</div>
									</li>
								</ul>
							</div>
			
						</div>
					</div>
				</div>
			</aside>
		</section>

		<!-- Vendor -->
		<script src="<?php echo base_url(); ?>assets/vendor/jquery/jquery.js"></script>
		<script src="<?php echo base_url(); ?>assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
		<script src="<?php echo base_url(); ?>assets/vendor/bootstrap/js/bootstrap.js"></script>
		<script src="<?php echo base_url(); ?>assets/vendor/nanoscroller/nanoscroller.js"></script>
		<script src="<?php echo base_url(); ?>assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
		<script src="<?php echo base_url(); ?>assets/vendor/magnific-popup/magnific-popup.js"></script>
		<script src="<?php echo base_url(); ?>assets/vendor/jquery-placeholder/jquery.placeholder.js"></script>
		
		<!-- Specific Page Vendor -->
		<script src="<?php echo base_url(); ?>assets/vendor/select2/select2.js"></script>
		<script src="<?php echo base_url(); ?>assets/vendor/jquery-datatables/media/js/jquery.dataTables.js"></script>
		<script src="<?php echo base_url(); ?>assets/vendor/jquery-datatables/extras/TableTools/js/dataTables.tableTools.min.js"></script>
		<script src="<?php echo base_url(); ?>assets/vendor/jquery-datatables-bs3/assets/js/datatables.js"></script>
		
		<!-- Theme Base, Components and Settings -->
		<script src="<?php echo base_url(); ?>assets/javascripts/theme.js"></script>
		
		<!-- Theme Custom -->
		<script src="<?php echo base_url(); ?>assets/javascripts/theme.custom.js"></script>
		
		<!-- Theme Initialization Files -->
		<script src="<?php echo base_url(); ?>assets/javascripts/theme.init.js"></script>


		<!-- Examples -->
		<script src="<?php echo base_url(); ?>assets/javascripts/tables/examples.datatables.default.js"></script>
		<script src="<?php echo base_url(); ?>assets/javascripts/tables/examples.datatables.row.with.details.js"></script>
		<script src="<?php echo base_url(); ?>assets/javascripts/tables/examples.datatables.tabletools.js"></script>
	</body>
</html>