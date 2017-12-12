<!-- Left Sidebar Header -->
<?php $this->load->view('admin/header'); ?>
<!-- Left Sidebar Header -->

<!-- Left Sidebar Start -->
<?php $this->load->view('admin/sidebar'); ?>
<!-- Left Sidebar End -->

    <!-- ============================================================== -->
    <!-- Start Dashboard Content here -->
    <!-- ============================================================== -->
		<section role="main" class="content-body">
					<header class="page-header">
						<h2>Dashboard</h2>
					
						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.html">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Dashboard</span></li>
							</ol>
					
							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
					<div class="row">
						  <!-- 
                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?> 
                         -->
                        <?php if($this->session->flashdata('error_msg')){
                        echo '<div class="alert alert-danger">'.$this->session->flashdata('error_msg').'</div>'; 
                        } ?>
                        <?php if(isset($error_msg) && $error_msg != ''){
                        echo '<div class="alert alert-danger">'.$error_msg.'</div>'; 
                        } ?>
                        <?php if($this->session->flashdata('succ_msg')){
                        echo '<div class="alert alert-success">'.$this->session->flashdata('succ_msg').'</div>'; 
                        } ?>
						<div class="col-md-6">
							<form method="post" id="add_user" name="add_user" enctype="multipart/form-data" action="<?php echo site_url('admin/table/add');?>" data-parsley-validate novalidate>
								<section class="panel">
									<header class="panel-heading">
										

										<h2 class="panel-title">Add Tables</h2>
										
									</header>
									<div class="panel-body">
										<div class="validation-message">
											<ul></ul>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Table No<span class="required">*</span></label>
											<div class="col-sm-9">
												<input type="text" name="tableno" class="form-control" title="Plase enter a name." placeholder="eg.: Tabl1-1" required="">
												<?php echo form_error('tableno'); ?>
											</div>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Capacity <span class="required">*</span></label>
											<div class="col-sm-9">
												<input type="text" name="capacity" class="form-control" title="Please enter an email address." placeholder="eg.: 05" required="">
											</div>
											<?php echo form_error('capacity'); ?>
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
						</div>
						
						<div class="col-md-6">
								<section class="panel">
									<header class="panel-heading">
									
						
										<h2 class="panel-title">All TABLES</h2>
									</header>
									<div class="panel-body">
										<div class="table-responsive">
											<table class="table mb-none">
												<thead>
													<tr>
														<th>Table No</th>
														<th>Capacity</th>
														<th>Actions</th>
														
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>O1</td>
														<td>4</td>
														
														<td class="actions">
															<a href=""><i class="fa fa-pencil"></i></a>
															<a href="" class="delete-row"><i class="fa fa-trash-o"></i></a>
														</td>
													</tr>
													<tr>
														<td>F1</td>
														<td>6</td>
													
														<td class="actions">
															<a href=""><i class="fa fa-pencil"></i></a>
															<a href="" class="delete-row"><i class="fa fa-trash-o"></i></a>
														</td>
													</tr>
													<tr>
														<td>B1</td>
														<td>6</td>
														
														<td class="actions">
															<a href=""><i class="fa fa-pencil"></i></a>
															<a href="" class="delete-row"><i class="fa fa-trash-o"></i></a>
														</td>
													</tr>
												</tbody>
											</table>
										</div>
									</div>
								</section>
							</div>
						
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

	<!-- ============================================================== -->
    <!-- End Dashboard content here -->
    <!-- ============================================================== -->

<!-- Left Footer Start -->
<?php $this->load->view('admin/footer'); ?>

<!-- Left Footer End -->