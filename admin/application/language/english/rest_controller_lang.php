<?php

/*
 * English language
 */

$lang['text_rest_invalid_api_key'] = 'Invalid API key %s'; // %s is the REST API key
$lang['text_rest_invalid_credentials'] = 'Invalid credentials';
$lang['text_rest_ip_denied'] = 'IP denied';
$lang['text_rest_ip_unauthorized'] = 'IP unauthorized';
$lang['text_rest_unauthorized'] = 'Unauthorized';
$lang['text_rest_ajax_only'] = 'Only AJAX requests are allowed';
$lang['text_rest_api_key_unauthorized'] = 'This API key does not have access to the requested controller';
$lang['text_rest_api_key_permissions'] = 'This API key does not have enough permissions';
$lang['text_rest_api_key_time_limit'] = 'This API key has reached the time limit for this method';
$lang['text_rest_unknown_method'] = 'Unknown method';
$lang['text_rest_unsupported'] = 'Unsupported protocol';

/*
==============================================================================================
								*	Start Users Rest API Messages   *
==============================================================================================
*/
$lang['text_rest_tokeninvalid'] = "Invalid token";
$lang['text_rest_invalidparam'] = "Invalid parameters provided";
$lang['text_rest_uploadfail'] = "Fail to upload image";

// Signup API (api/users/signup)
$lang['text_rest_signup_mailfail'] = "Sorry ! The OTP could not be sent";
$lang['text_rest_email_unique'] = "This email already registered";
$lang['text_rest_phone_unique'] = "This phone number already registered";
$lang['text_signup_sucess'] = "You have successfully signed up";
$lang['text_rest_otp_sent_success'] = "Verification code has been sent to your registered phone number";
$lang['text_invalid_phone'] = "Please enter registered phone number";
$lang['text_document_review'] = "Your documents are being reviewed. We will get back to you soon";
$lang['text_invalid_referal_code'] = "Invalid referral code";

// OTP
$lang['otp_verify'] = "Please verify your OTP";
$lang['otp_verify_success'] = "OTP verified successfully";
$lang['otp_verify_fail'] = "Please enter valid OTP";
$lang['phone_exists'] = "This phone number already registered, try to login or register with another phone number";
$lang['otp_sent_success'] = "OTP sent successfully";
$lang['text_invalid_phone_failtosend'] = "We are unable to send OTP please enter a valid phone number";

// Login API (api/users/login)
$lang['text_rest_login_success'] = "Login successful";
$lang['text_rest_login_fail'] = "Please enter correct phone or password";
$lang['text_rest_fblogin_fail'] = "Invalid facebook id";
$lang['text_rest_account_block'] = "Your account is not activated! Please contact Administrator";

// Users edit (api/users/edit)
$lang['text_usersedit_sucess'] = "Profile updated successfully";

// Users detail/list API (api/users/users)
$lang['text_rest_userdetail_notfound'] = "User details not found";
$lang['text_rest_userlist_notfound'] = "Users list not found";

// Forgotpassword API (api/users/forgotpassword)
$lang['text_rest_forgotpassword_success'] = "Your password has been sent to your registered phone number";
$lang['text_rest_forgotpassword_emailinvalid'] = "The email which you enter is not exists";
$lang['text_rest_forgotpassword_sentfail'] = "Something went wrong";
$lang['text_rest_forgotpassword_fb'] = "You can not change password while you are login with facebook";
$lang['text_rest_forgotpassword_phoneinvalid'] = "The phone which you entered is not exists";

// Users password change API (api/users/changepassword)
$lang['text_rest_changepassword_success'] = "Password changed successfully";
$lang['text_rest_oldpassword_notfound'] = "Your old password does not match";
$lang['text_rest_samepassword'] = "Old password and new password can not be same";

// Users update latlong API (api/users/updatelatlong)
$lang['text_rest_updatelatlong_success'] = "User location updated successfully";

// Users update updatedeviceid API (api/users/updatedeviceid)
$lang['text_rest_updatedeviceid_success'] = "Device id updated successfully";

// User logout API (api/users/logout)
$lang['text_rest_logout_success'] = "Logged out successfully";

// Car API (api/car/get_car_list)
$lang['carlist_found'] = "Car List Found";
/*
==============================================================================================
								*	Stop Users Rest API Messages   *
==============================================================================================
*/



/*
==============================================================================================
								*	Start Driver Rest API Messages   *
==============================================================================================
*/
// Driver Signup (api/driver/signup)
$lang['text_driversignup_sucess'] = "You have successfully signed up";

$lang['table_info_sucess'] = "Table info  get successfully";
$lang['table_info_fales'] = "Table Not found";

//Driver Documents (api/driver/driver_document)
$lang['text_rest_driver_id_unique'] = "Your document is already submitted and being reviewed";
$lang['text_driverdocument_sucess'] = "Your profile is being reviewed. We will inform you once it is approved.";


// Login API (api/driver/login)
$lang['text_rest_login_success'] = "Login successful";
$lang['text_driver_login_fail'] = "Please enter correct email or password";
$lang['text_driver_document_verify'] = "Your document is not verified yet! Please contact Administrator";
$lang['text_driver_account_block'] = "Your account is not activated! Please contact Administrator";
$lang['text_sp_account_block'] = "The service provider is currently deactivated";
$lang['text_driver_login_document_fail'] = "Please submit your documents";

// Driver edit (api/driver/edit)
$lang['text_driveredit_sucess'] = "Profile updated successfully";


// Users password change API (api/users/changepassword)
$lang['text_rest_changepassword_success'] = "Password changed successfully";
$lang['text_rest_oldpassword_notfound'] = "Your old password is not match";
$lang['text_rest_samepassword'] = "Old password and new password can not be same";

// Driver update latlong API (api/driver/updatelatlong)
$lang['text_driver_updatelatlong_success'] = "Driver location updated successfully";

// Driver update updatedeviceid API (api/driver/updatedeviceid)
$lang['text_driver_updatedeviceid_success'] = "Device id updated successfully";

// Driver check referral API (api/driver/checkreferral)
$lang['text_driver_checkreferral_success'] = "Your referral code is valid";
$lang['text_driver_checkreferral_invalid'] = "Your referral code is invalid";

// Driver detail/list API (api/driver/drivers)
$lang['text_rest_driverdetail_notfound'] = "Driver details not found";
$lang['text_rest_driverlist_notfound'] = "Drivers list not found";

// Driver logout API (api/driver/logout)
$lang['text_driver_logout_success'] = "Logged out successfully";

// Driver driverfree API (api/driver/driverfree)
$lang['text_driver_free_success'] = "You are now free";


$lang['text_service_changed'] = "Service updated successfully";

// Driver add_couponcard API (api/driver/add_couponcard)
$lang['text_driver_couponcard_invalid'] = "Invalid coupon card";
$lang['text_driver_couponcard_already_used'] = "This coupon card is already used";
$lang['text_driver_couponcard_success'] = "Your coupon card added successfully";

/*
==============================================================================================
								*	Stop Driver Rest API Messages   *
==============================================================================================
*/




/*
==============================================================================================
								*	Start Trips Rest API Messages   *
==============================================================================================
*/

// Trip Status
$lang['text_trip_nodriverfound'] = "No driver found";
$lang['trip_details_found'] = "Order found";
$lang['fare_estimation_success'] = "Fare estimation found";
$lang['trip_date_time'] = 'Please enter trip time';

// Trip fare estimation (api/trip/carlist)
$lang['text_trip_carlist_notfound'] = "Car list not found";
$lang['text_trip_carlist_found'] = "Car list found";

// Trip verify promocode (api/trip/verifypromocode)
$lang['text_trip_usedpromocode'] = "This promocode is already used";
$lang['text_trip_inactivepromocode'] = "This promocode is inactive";
$lang['text_trip_promocodeexpired'] = "This promocode has expired";
$lang['text_trip_invalidpromocode'] = "Invalid promocode";


// Trip placeorder (api/trip/placeorder)
$lang['text_trip_drivernotfound'] = "Currently all drivers are busy. Please try after some time";
$lang['text_trip_driverfound'] = "Driver found";
$lang['text_order_success'] = "Order placed successfully";


// Trip decline_request (api/trip/decline_request)
$lang['text_trip_decline_invalidorderid'] = "Invalid order";
$lang['text_trip_cannotdecline'] = "You cannot decline this order";
$lang['text_trip_request_cancelled'] = "Request cancelled";

// Trip accept_order (api/trip/accept_order)
$lang['text_trip_already_assigned'] = "Order already assigned";
$lang['text_trip_already_arrived'] = "Order already arrived";
$lang['text_trip_already_completed'] = "Order already completed";
$lang['text_trip_already_cancelled'] = "Order already cancelled";
$lang['text_trip_already_processing'] = "This order has already started";
$lang['text_trip_accept_invalid'] = "You cannot accept this order";

// Trip arrived_pickup (api/trip/arrived_pickup)
$lang['text_trip_arrived_pickup_success'] = "Driver arrived at pickup location";
$lang['text_trip_not_assignedtodriver'] = "This order is not assigned to you";

// Trip pickup (api/trip/pickup)
$lang['text_trip_pickup_success'] = "Pickup successful";
$lang['already_pickup'] = "This item is already picked up";
$lang['text_trip_before_reach_pickup'] = "You cannot pickup before reaching the pickup location";

// Trip dropoff_trip (api/trip/dropoff_trip)
$lang['text_trip_dropoff_already_completed'] = "This order is already completed";
$lang['text_trip_dropoff_order_not_started'] = "This order is not started";

// Trip get_order (api/trip/get_order)
$lang['text_trip_get_order'] = "Order details not found";

// Trip rate_review (api/trip/rate_review)
$lang['text_trip_rate_review'] = "Review submitted successfully";

// Trip check_trip (api/trip/check_trip)
$lang['text_trip_check'] = "Order not found";

// Trip past_trips (api/trip/past_trips)
$lang['text_past_trips_notfound'] = "Orderlist not found";
$lang['past_trip_found_success'] = "Orderlist found";

// Trip future_trips (api/trip/future_trips)
$lang['text_future_trips_notfound'] = "Orderlist not found";
$lang['future_trip_found_success'] = "Orderlist found";

// Trip makepaymentdone (api/trip/makepaymentdone)
$lang['text_makepaymentdone'] = "Payment status changed";

// Trip cancel_trip (api/trip/cancel_trip)
$lang['text_trip_cancel_success'] = "Trip cancelled successfully";


// Trip decline_info (api/trip/decline_info)
$lang['text_decline_info_success'] = "Decline information submitted successfully";

// Trip decline_info (api/trip/user_payment)
$lang['text_payment_success'] = "Payment received successfully";
$lang['text_order_not_completed'] = "This order is not completed";

$lang['review_success'] = "Review submitted successfully";

$lang['text_order_complete'] = "Ride completed successfully";

/*
==============================================================================================
								*	Stop Trips Rest API Messages   *
==============================================================================================
*/



/*===================================== ADMIN PANEL ==========================================*/

/*Date: 30 march 2017*/

//Dashboard
$lang['admin_dashboard_title'] = "Dashboard";
$lang['admin_dashboard_welcome'] = "Welcome to SAIK Taxi";

$lang['admin_total_user'] = "Users";
$lang['admin_total_driver'] = "Drivers";
$lang['admin_promocode_list'] = "Promocode List";
$lang['admin_vehicle'] = "Vehicles";
$lang['admin_couponcard'] = "Coupon Card";
$lang['admin_order_management'] = "Orders";

$lang['admin_waiting_order'] = "Waiting Orders";
$lang['admin_assigned_order'] = "Assigned Orders";
$lang['admin_arrived_order'] = "Arrived Orders";
$lang['admin_processing_order'] = "Processing Orders";
$lang['admin_completed_order'] = "Completed Orders";
$lang['admin_cancelled_order'] = "Cancelled Orders";

$lang['admin_setting'] = "Settings";
$lang['admin_reports'] = "Reports";

$lang['admin_sms'] = "Send SMS";
$lang['user_type'] = "User Type";
$lang['userType'] = "User Type";
$lang['user_select'] = "Select User";
$lang['message'] = "message";

$lang['driver_type'] = "Driver Type";
$lang['driverType'] = "Driver Type";
$lang['driver_select'] = "Select Driver";

//User List
$lang['admin_user_list'] = "User List";

$lang['fb_id'] = "FB ID";
$lang['name'] = "Name";
$lang['email'] = "Email";
$lang['phone'] = "Phone";
$lang['last_login'] = "Last Login";
$lang['otp'] = "OTP";
$lang['is_login'] = "Is Login";
$lang['is_active'] = "Is Active";
$lang['action'] = "Actions";

$lang['login'] = "Login";
$lang['logout'] = "Logout";
$lang['verified'] = "Verified";
$lang['not_verified'] = "Not Verified";

$lang['active'] = "Active";
$lang['inactive'] = "Inactive";

$lang['used'] = "Used";
$lang['unused'] = "Unused";

$lang['map'] = "Map";
$lang['search'] = "Search"; 

$lang['sure_to_active'] = "Are you sure you want to Activate?";
$lang['sure_to_inactive'] = "Are you sure you want to Inactivate?";
$lang['sure_to_delete'] = "Are you sure you want to delete?";

//add by ibrahim
$lang['delete_user'] = "Delete User";
$lang['delete_driver'] = "Delete Driver";
$lang['delete_trip'] = "Delete Trip";
$lang['delete_vehicle'] = "Delete Vehicle";
$lang['delete_Coupon'] = "Delete Coupon Card";
$lang['edit_Coupon'] = "Edit Coupon Card";
$lang['delete_promocode'] = "Delete Promocode";

/*Date: 31 march 2017*/

//Driver List
$lang['admin_driver_list'] = "Driver List";

$lang['car'] = "Car";
$lang['is_free'] = "Is Free";
$lang['is_service'] = "Is Service";
$lang['is_verified'] = "Is Verified";
$lang['docs'] = "Docs";
$lang['new_docs'] = "New Docs";
$lang['on'] = 'On';
$lang['off'] = 'Off';
$lang['not_submitted'] = "Not submitted"; 
$lang['yes'] = "Yes";
$lang['no'] = 'No';


//Edit User
$lang['edit_user'] = "Edit User";
$lang['fname'] = "First Name";
$lang['lname'] = "Last Name";
$lang['password'] = "Password";
$lang['cpassword'] = "Confirm Password";
$lang['profile_image'] = "Profile Image";
$lang['update'] = "Update";
$lang['back'] = "Back";

//Edit Driver
$lang['edit_driver'] = 'Edit Driver';

//View User
$lang['view_user'] = "View User";
$lang['latitude'] = "Latitude";
$lang['longitude'] = "longitude";
$lang['device_id'] = "Device ID";
$lang['device_type'] = "Device Type";
$lang['referal_code'] = "Referal Code";
$lang['wallet'] = "Wallet";
$lang['token'] = "Token";

$lang['car_model'] = "Car Model";
$lang['car_color'] = "Car Color";
$lang['car_platenumber'] = "Car Plate Number";

//View Driver
$lang['view_driver'] = "View Driver";

//Edit Documents
$lang['edit_document'] = "Edit Document";
$lang['registration_image'] = "Registration Image";
$lang['vehicle_front_image'] = "Vehicle Front Image";
$lang['vehicle_back_image'] = "Vehicle Back Image";
$lang['licence_image'] = "License Image";
$lang['driver_id_image'] = "Driver Id Image";
$lang['owner_id_image'] = "Owner Id Image";
$lang['is_document_verified'] = "Is Document Verified";

/*Please select valid image*/
$lang['invalid_image'] = "Please select valid image file";

//Vehicles List
$lang['add'] = "Add";
$lang['car_image_selected'] = "Seleted Image";
$lang['car_image_unselected'] = "Unselected Image";
$lang['car_name'] = "Name";
$lang['base_fare'] = "Base Fare";
$lang['rate_per_km'] = "Rate Per Km";
$lang['rate_per_min'] = "Rate Per Minute";
$lang['waiting_min'] = "Waiting Charge Per Minute";
$lang['car_order'] = "Car Order";

//Edit Vehicle
$lang['edit_vehicle'] = "Edit Vehicle";

//Driver couponcard List
$lang['upload_file'] = "Upload File";
$lang['demo_link'] = "Demo Link";
$lang['btn_close'] = "Close";
$lang['btn_upload'] = "Upload";
$lang['add_couponcard'] = "Add Coupon Card";
$lang['couponcard'] = "Coupon Card";
$lang['no_couponcard_found'] = "Driver coupon card list not found";
$lang['couponcard_added_successfully'] = "Coupon card added successfully";
$lang['couponcard_updated'] = "Coupon card updated successfully";
$lang['couponcard_delete_successfully'] = "Coupon card deleted successfully";
$lang['couponcard_doesnot_exists'] = 'The Coupon card you are trying to delete does not exist';
$lang['couponcards_exists'] = 'The Coupon cards you are trying to add is already exists';
$lang['couponcards_format_not_allowed'] = 'Sorry! This CSV data format is not allowed';

//Trip List
$lang['waiting'] = "Waiting";
$lang['assigned'] = "Assigned";
$lang['arrived'] = "Arrived";
$lang['processing'] = "Processing";
$lang['completed'] = "Completed";
$lang['cancelled'] = "Cancelled";

$lang['trip_now'] = "Trip Now";
$lang['trip_later'] = "Trip Later";

$lang['driver'] = "Driver";
$lang['user'] = "User";
$lang['pickup'] = "Pickup";
$lang['dropoff'] = "Dropoff";
$lang['vehicle'] = "Vehicle";
$lang['distance'] = "Distance";
$lang['total'] = "Total";
$lang['payment'] = "Payment";
$lang['trip_type'] = "Trip Type";
$lang['start_datetime'] = "Start Trip Time";
$lang['tripdatetime'] = "Trip Time";
$lang['paid'] = "Paid";
$lang['unpaid'] = "Unpaid";
$lang['cancelled_by'] = "Cancelled By";
$lang['reason'] = "Reason";

$lang['now'] = "Now";
$lang['later'] = "Later";

/*Edit Trip*/
$lang['edit_trip'] = "Edit Trip";

/*Assign Trip*/
$lang['assign_trip'] = "Assign Trip";
$lang['select_driver'] = "Select Driver";

/*View Trip*/
$lang['view'] = "View";
$lang['promocode_id'] = "Promocode Id";
$lang['pickup_latitude'] = "Pickup Latitude";
$lang['pickup_longitude'] = "Pickup Longitude";
$lang['dropoff_latitude'] = "Dropoff Latitude";
$lang['dropoff_longitude'] = "Dropoff Longitude";

$lang['start_trip'] = "Start Trip";
$lang['end_trip'] = "End Trip";
$lang['trip_duration'] = "Trip Duration";
$lang['promocode_amount'] = "Promocode Amount";
$lang['tip'] = "Tip";
$lang['tot_amount'] = "Total Amount";
$lang['transaction_id'] = "Transaction Id";
$lang['status'] = "Status";
$lang['waiting_time'] = "Waiting Time";

/*Promocode List*/
$lang['promocode'] = "Promocode";
$lang['promocode_type'] = "Promocode Type";
$lang['amount'] = "Amount";
$lang['select_amount'] = "Select Amount";
$lang['start_date'] = "Start Date";
$lang['end_date'] = "End Date";
$lang['count'] = "Count";
$lang['description'] = "Description";
$lang['no_promocode_found'] = "Promocode not found";

/*Edit Promocode*/
$lang['edit_promocode'] = "Edit Promocode";
 
/*Add Promocode*/
$lang['add_promocode'] = "Add Promocode";

/*Used Promocode List*/
$lang['used_promocode_list'] = "Used Promocode List";
$lang['date'] = "Date";
$lang['datetime'] = "Date/Time";

/*Reports*/
$lang['report_list'] = "Reports List";
$lang['total_booking'] = "Total Booking";
$lang['go'] = "Go";
$lang['clear_all'] = "Clear All";
$lang['export'] = "Export";
$lang['export_excel'] = "Export Excel";
$lang['import_excel'] = "Import Excel";
$lang['export_pdf'] = "Export PDF";

/*Settings*/
$lang['settings'] = "Settings";
$lang['edit_settings'] = "Edit Settings";
$lang['user_settings'] = "User Settings";
$lang['driver_settings'] = "Driver Settings";
$lang['records_per_page'] = "Records Per Page";
$lang['driver_percent'] = "Driver Percent";
$lang['owner_percent'] = "Owner Percent";
$lang['referal_amount'] = "Referal Amount";
$lang['base_distance'] = "Base Distance";
$lang['min_wallet'] = "Min Wallet";

/*Success and Error Message*/

//Vehicle
$lang['vehicle_added_successfully'] = "Vehicle added successfully";
$lang['vehicle_delete_successfully'] = "Vehicle deleted successfully";
$lang['vehicle_doesnot_exists'] = 'The Vehicle you are trying to delete does not exist';
$lang['vehicle_carorder_exists'] = "The Vehicle order you are trying to edit this already exists";

//Driver
$lang['driver_updated_successfully'] = "Driver updated successfully";
$lang['driver_document_updated_successfully'] = "Driver document updated successfully";

$lang['driver_delete_successfully'] = "Driver deleted successfully";
$lang['driver_doesnot_exists'] = 'The Driver you are trying to delete does not exist';

$lang['profile_update_successfully'] = "Profile updated successfully";

$lang['user_settings_updated'] = "User settings updated successfully";
$lang['driver_settings_updated'] = "Driver settings updated successfully";

$lang['trip_updated'] = "Trip updated successfully";

$lang['request_sent'] = "Request sent successfully";

$lang['trip_delete_successfully'] = "Trip deleted successfully";
$lang['trip_doesnot_exists'] = 'The Trip you are trying to delete does not exist';


$lang['promocode_updated'] = "Promocode updated successfully";
$lang['promocode_delete_successfully'] = "Promocode deleted successfully";
$lang['promocode_doesnot_exists'] = 'The Promocode you are trying to delete does not exist';

$lang['user_updated'] = "User updated successfully";
$lang['user_delete_successfully'] = "User deleted successfully";
$lang['user_doesnot_exists'] = 'The User you are trying to delete does not exist';

/*Admin Profile*/
$lang['edit_profile'] = "Edit Profile";
$lang['profile'] = "Profile";

$lang['required_validation'] = "Please enter value";

?>