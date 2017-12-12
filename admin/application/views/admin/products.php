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
							<form method="post" id="add_user" name="add_user" enctype="multipart/form-data" action="<?php echo site_url('admin/products/add');?>" data-parsley-validate novalidate>
								<section class="panel">
									<header class="panel-heading">
										

										<h2 class="panel-title">Add Products</h2>
										
									</header>
									<div class="panel-body">
										<div class="validation-message">
											<ul></ul>
										</div>
										<div class="form-group">
											<label class="col-sm-3 control-label">Product Name<span class="required">*</span></label>
											<div class="col-sm-9">
												<input type="text" name="productname" class="form-control" title="Plase enter a name." placeholder="eg.: John Doe" required="">
											</div>
											<?php echo form_error('productname'); ?>
										</div>
										
										
										<div class="form-group">
												<label class="col-md-3 control-label" >Category</label>
												<div class="col-md-9">
													<select class="form-control mb-md" name='category'>
														<option value=''>Select Category</option>
														<option value='1'>Staters</option>
														<option value='2'>Gravy</option>
														<option value='3'>Roti</option>
														<option value='4'>Rice</option>
														
													</select>
												<?php echo form_error('Category'); ?>
													
												</div>
												<?php echo form_error('item_type'); ?>
											</div>
											
											<div class="form-group">
											<label class="col-md-3 control-label" >Item Type</label>
											<div class="col-md-9">
												<select class="form-control mb-md" name="item_type">
													<option value=''>Select Item Type</option>
													<option value='Veg'>Veg</option>
													<option value='Non-Veg'>Non-Veg</option>
													<option value='Half Plate'>Half Plate</option>
													<option value='Full Plate'>Full Plate</option>
												</select>
											</div>
											<?php echo form_error('item_type'); ?>
										</div>
										
										
										<div class="form-group">
											<label class="col-sm-3 control-label">Price <span class="required">*</span></label>
											<div class="col-sm-9">
												<input type="text" name="price" class="form-control" title="Please enter an email address." placeholder="eg.: john@doe.com" required="">
											</div>
											<?php echo form_error('price'); ?>
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
									
						
										<h2 class="panel-title">All Products</h2>
									</header>
									<div class="panel-body">
										<div class="table-responsive">
											<table class="table mb-none">
												<thead>
													<tr>
														<th>Product Name</th>
														<th>Category</th>
														<th>Price</th>
														<th>Action</th>
														
													</tr>
												</thead>
												<tbody>
													<tr>
														<td>Biryani</td>
														<td>Rice</td>
														<td>120/-</td>
														
														<td class="actions">
															<a href=""><i class="fa fa-pencil"></i></a>
															<a href="" class="delete-row"><i class="fa fa-trash-o"></i></a>
														</td>
													</tr>
													<tr>
														<td>Biryani</td>
														<td>Rice</td>
														<td>120/-</td>
													
														<td class="actions">
															<a href=""><i class="fa fa-pencil"></i></a>
															<a href="" class="delete-row"><i class="fa fa-trash-o"></i></a>
														</td>
													</tr>
													<tr><td>Biryani</td>
														<td>Rice</td>
														<td>120/-</td>
														
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