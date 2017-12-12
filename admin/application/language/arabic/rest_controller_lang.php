<?php

/*
 * Arabic language
 */

$lang['text_rest_invalid_api_key'] = 'مفتاح API غير صالح٪ الصورة'; // %s is the REST API key
$lang['text_rest_invalid_credentials'] = 'بيانات الاعتماد غير صالحة';
$lang['text_rest_ip_denied'] = 'نفى IP';
$lang['text_rest_ip_unauthorized'] = 'IP غير المصرح به';
$lang['text_rest_unauthorized'] = 'غير مصرح';
$lang['text_rest_ajax_only'] = 'ويسمح فقط طلبات أجاكس';
$lang['text_rest_api_key_unauthorized'] = 'لم يكن هذا المفتاح API الوصول إلى وحدة تحكم المطلوبة';
$lang['text_rest_api_key_permissions'] = 'لم يكن هذا المفتاح API أذونات كافية';
$lang['text_rest_api_key_time_limit'] = 'وقد بلغ هذا مفتاح API المهلة المحددة لهذه الطريقة';
$lang['text_rest_unknown_method'] = 'طريقة غير معروف';
$lang['text_rest_unsupported'] = 'بروتوكول غير معتمد';

/*
==============================================================================================
								*	Start Users Rest API Messages   *
==============================================================================================
*/
$lang['text_rest_tokeninvalid'] = "رمز غير صحيح";
$lang['text_rest_invalidparam'] = "معلمات غير صالحة المقدمة";
$lang['text_rest_uploadfail'] = "فشل لتحميل الصور";

// Signup API (api/users/signup)
$lang['text_signup_sucess'] = "كنت قد وقعت بنجاح";
$lang['text_rest_signup_mailfail'] = "آسف ! تعذر إرسال مكتب المدعي العام";
$lang['text_rest_email_unique'] = "هذا البريد الإلكتروني مسجل بالفعل";
$lang['text_rest_phone_unique'] = "هذا رقم الهاتف مسجل بالفعل";
$lang['text_rest_otp_sent_success'] = "تم إرسال رمز التحقق إلى رقم الهاتف المسجل الخاص بك";
$lang['text_invalid_phone'] = "يرجى إدخال رقم الهاتف المسجل";
$lang['text_document_review'] = "ويجري استعراض المستندات الخاصة بك. ونحن سوف نعود إليكم قريبا";
$lang['text_invalid_referal_code'] = "إحالة قانون غير صالح";

// OTP
$lang['otp_verify'] = "يرجى التحقق من مكتب المدعي العام الخاص بك";
$lang['otp_verify_success'] = "التحقق من مكتب المدعي العام بنجاح";
$lang['otp_verify_fail'] = "الرجاء إدخال صالحة مكتب المدعي العام";
$lang['phone_exists'] = "هذا رقم الهاتف مسجل بالفعل، في محاولة لتسجيل الدخول أو التسجيل مع رقم هاتف آخر";
$lang['otp_sent_success'] = "أرسلت مكتب المدعي العام بنجاح";
$lang['text_invalid_phone_failtosend'] = "يتعذر علينا إرسال عنوان أوتب الرجاء إدخال رقم هاتف صالح";

// Login API (api/users/login)
$lang['text_rest_login_success'] = "تم الدخول بنجاح";
$lang['text_rest_login_fail'] = "الرجاء إدخال الهاتف أو كلمة المرور الصحيحة";
$lang['text_rest_fblogin_fail'] = "معرف الفيسبوك صالح";
$lang['text_rest_account_block'] = "حسابك غير مفعل! يرجى الاتصال بمسؤول";

// Users edit (api/users/edit)
$lang['text_usersedit_sucess'] = "تم تحديث الملف الشخصي بنجاح";

// Users detail/list API (api/users/users)
$lang['text_rest_userdetail_notfound'] = "تفاصيل المستخدم غير موجود";
$lang['text_rest_userlist_notfound'] = "قائمة المستخدمين لم يتم العثور على";

// Forgotpassword API (api/users/forgotpassword)
$lang['text_rest_forgotpassword_success'] = "تم إرسال كلمة المرور إلى رقم هاتفك المسجل";
$lang['text_rest_forgotpassword_emailinvalid'] = "البريد الإلكتروني الذي قمت بإدخال ليس موجودا";
$lang['text_rest_forgotpassword_sentfail'] = "حدث خطأ ما";
$lang['text_rest_forgotpassword_fb'] = "لا يمكنك تغيير كلمة المرور في حين كنت الدخول مع الفيسبوك";
$lang['text_rest_forgotpassword_phoneinvalid'] = "الهاتف الذي أدخلته غير موجود";

// Users password change API (api/users/changepassword)
$lang['text_rest_changepassword_success'] = "تم تغيير الرقم السري بنجاح";
$lang['text_rest_oldpassword_notfound'] = "لا تطابق كلمة المرور القديمة";
$lang['text_rest_samepassword'] = "كلمة المرور القديمة وكلمة المرور الجديدة لا يمكن أن يكون نفسه";

// Users update latlong API (api/users/updatelatlong)
$lang['text_rest_updatelatlong_success'] = "موقع المستخدم تحديث بنجاح";

// Users update updatedeviceid API (api/users/updatedeviceid)
$lang['text_rest_updatedeviceid_success'] = "معرف الجهاز تحديث بنجاح";

// User logout API (api/users/logout)
$lang['text_rest_logout_success'] = "تسجيل الخروج بنجاح";

// Car API (api/car/get_car_list)
$lang['carlist_found'] = "قائمة سيارة تم العثور عليها";
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
$lang['text_driversignup_sucess'] = "كنت قد وقعت بنجاح";

//Driver Documents (api/driver/driver_document)
$lang['text_rest_driver_id_unique'] = "يقدم المستند بالفعل والتي يجري استعراضها";
$lang['text_driverdocument_sucess'] = "وثائق قدمت بنجاح! سوف المشرف الاتصال بك قريبا";

// Login API (api/driver/login)
$lang['text_rest_login_success'] = "تم الدخول بنجاح";
$lang['text_driver_login_fail'] = "الرجاء إدخال بريد إلكتروني صحيح أو كلمة المرور";
$lang['text_driver_document_verify'] = "لا يتم التحقق من وثيقة الخاص بك حتى الآن! يرجى الاتصال بمسؤول";
$lang['text_driver_account_block'] = "حسابك غير مفعل! يرجى الاتصال بمسؤول";
$lang['text_sp_account_block'] = "إلغاء تنشيط مزود الخدمة حاليا";
$lang['text_driver_login_document_fail'] = "يرجى تقديم المستندات الخاصة بك";

// Driver edit (api/driver/edit)
$lang['text_driveredit_sucess'] = "تم تحديث الملف الشخصي بنجاح";


// Users password change API (api/users/changepassword)
$lang['text_rest_changepassword_success'] = "تم تغيير الرقم السري بنجاح";
$lang['text_rest_oldpassword_notfound'] = "لا تطابق كلمة المرور القديمة";
$lang['text_rest_samepassword'] = "كلمة المرور القديمة وكلمة المرور الجديدة لا يمكن أن يكون نفسه";

// Driver update latlong API (api/driver/updatelatlong)
$lang['text_driver_updatelatlong_success'] = "سائق المكان بنجاح تحديث";

// Driver update updatedeviceid API (api/driver/updatedeviceid)
$lang['text_driver_updatedeviceid_success'] = "معرف الجهاز تحديث بنجاح";

// Driver check referral API (api/driver/checkreferral)
$lang['text_driver_checkreferral_success'] = "إحالة قانون صالحا";
$lang['text_driver_checkreferral_invalid'] = "إحالة قانون غير صالح";

// Driver detail/list API (api/driver/drivers)
$lang['text_rest_driverdetail_notfound'] = "تفاصيل سائق لم يتم العثور على";
$lang['text_rest_driverlist_notfound'] = "السائقين قائمة لم يتم العثور على";

// Driver logout API (api/driver/logout)
$lang['text_driver_logout_success'] = "تسجيل الخروج بنجاح";

// Driver driverfree API (api/driver/driverfree)
$lang['text_driver_free_success'] = "أنت الآن مجانا";


$lang['text_service_changed'] = "خدمة تجديد بنجاح";

// Driver add_couponcard API (api/driver/add_couponcard)
$lang['text_driver_couponcard_invalid'] = "بطاقة قسيمة غير صالحة";
$lang['text_driver_couponcard_already_used'] = "تم استخدام بطاقة القسيمة هذه بالفعل";
$lang['text_driver_couponcard_success'] = "تمت إضافة بطاقة القسيمة بنجاح";
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
$lang['text_trip_nodriverfound'] = "لم يتم العثور على سائق";
$lang['trip_details_found'] = "أجل وجدت";
$lang['fare_estimation_success'] = "ووجد تقدير أجرة";
$lang['trip_date_time'] = 'الرجاء إدخال وقت الرحلة';

// Trip fare estimation (api/trip/carlist)
$lang['text_trip_carlist_notfound'] = "قائمة سيارة لم يتم العثور على";
$lang['text_trip_carlist_found'] = "وجدت قائمة سيارة";

// Trip verify promocode (api/trip/verifypromocode)
$lang['text_trip_usedpromocode'] = "يستخدم هذا الرمز الترويجي بالفعل";
$lang['text_trip_inactivepromocode'] = "هذا الرمز الترويجي غير نشط";
$lang['text_trip_promocodeexpired'] = "انتهت صلاحية هذا الرمز الترويجي";
$lang['text_trip_invalidpromocode'] = "الرمز الترويجي غير صالح";


// Trip placeorder (api/trip/placeorder)
$lang['text_trip_drivernotfound'] = "حاليا جميع السائقين مشغولة. يرجى المحاولة بعد بعض الوقت";
$lang['text_trip_driverfound'] = "سائق وجدت";
$lang['text_order_success'] = "ترتيب وضعها بنجاح";

// Trip decline_request (api/trip/decline_request)
$lang['text_trip_decline_invalidorderid'] = "طلب غير صحيح";
$lang['text_trip_cannotdecline'] = "لا يمكنك التراجع هذا النظام";
$lang['text_trip_request_cancelled'] = "طلب إلغاء";

// Trip accept_order (api/trip/accept_order)
$lang['text_trip_already_assigned'] = "أجل تعيينها بالفعل";
$lang['text_trip_already_arrived'] = "أجل وصل بالفعل";
$lang['text_trip_already_completed'] = "أجل الانتهاء بالفعل";
$lang['text_trip_already_cancelled'] = "تم إلغاء الطلب بالفعل";
$lang['text_trip_already_processing'] = "وقد بدأ هذا الأمر بالفعل";
$lang['text_trip_accept_invalid'] = "كنت لا يمكن أن تقبل هذا النظام";

// Trip arrived_pickup (api/trip/arrived_pickup)
$lang['text_trip_arrived_pickup_success'] = "وصل السائق في موقع بيك اب";
$lang['text_trip_not_assignedtodriver'] = "لم يتم تعيين هذا الأمر لك";

// Trip pickup (api/trip/pickup)
$lang['text_trip_pickup_success'] = "وانيت ناجحة";
$lang['already_pickup'] = "يتم اختيار هذا البند بالفعل";
$lang['text_trip_before_reach_pickup'] = "لا يمكنك التقاط قبل الوصول الى المكان بيك اب";

// Trip dropoff_trip (api/trip/dropoff_trip)
$lang['text_trip_dropoff_already_completed'] = "اكتمال هذا النظام بالفعل";
$lang['text_trip_dropoff_order_not_started'] = "لم يتم تشغيل هذا النظام";

// Trip get_order (api/trip/get_order)
$lang['text_trip_get_order'] = "تفاصيل الطلب لم يتم العثور على";

// Trip rate_review (api/trip/rate_review)
$lang['text_trip_rate_review'] = "قدمت مراجعة بنجاح";

// Trip check_trip (api/trip/check_trip)
$lang['text_trip_check'] = "النظام لم يتم العثور";

// Trip past_trips (api/trip/past_trips)
$lang['text_past_trips_notfound'] = "ترتيب قائمة لم يتم العثور";
$lang['past_trip_found_success'] = "وجدت قائمة ترتيب";

// Trip future_trips (api/trip/future_trips)
$lang['text_future_trips_notfound'] = "ترتيب قائمة لم يتم العثور";
$lang['future_trip_found_success'] = "وجدت قائمة ترتيب";

// Trip makepaymentdone (api/trip/makepaymentdone)
$lang['text_makepaymentdone'] = "تغيير حالة الدفع";

// Trip cancel_trip (api/trip/cancel_trip)
$lang['text_trip_cancel_success'] = "رحلة ألغيت بنجاح";


// Trip decline_info (api/trip/decline_info)
$lang['text_decline_info_success'] = "الانخفاض المعلومات المقدمة بنجاح";

// Trip decline_info (api/trip/user_payment)
$lang['text_payment_success'] = "تلقى الدفع بنجاح";
$lang['text_order_not_completed'] = "لم يكتمل هذا النظام";

$lang['review_success'] = "قدمت مراجعة بنجاح";

$lang['text_order_complete'] = "الانتهاء ركوب بنجاح";
/*
==============================================================================================
								*	Stop Trips Rest API Messages   *
==============================================================================================
*/

/*===================================== ADMIN PANEL ==========================================*/

/*Date: 30 march 2017*/

//Dashboard
$lang['admin_dashboard_title'] = "لوحة القيادة";
$lang['admin_dashboard_welcome'] = "مرحبا بكم في نهاية المطاف تاكسي";

$lang['admin_total_user'] = "إجمالي المستخدمين";
$lang['admin_total_driver'] = "مجموع السائقين";
$lang['admin_promocode_list'] = "قائمة بروموكود";
$lang['admin_vehicle'] = "المركبات";
$lang['admin_couponcard'] = "بطاقة القسيمة";
$lang['admin_order_management'] = "إدارة النظام";

$lang['admin_waiting_order'] = "أوامر الانتظار";
$lang['admin_assigned_order'] = "أوامر مخصصة";
$lang['admin_arrived_order'] = "وصل أوامر";
$lang['admin_processing_order'] = "أوامر المعالجة";
$lang['admin_completed_order'] = "الطلبات المكتملة";
$lang['admin_cancelled_order'] = "الطلبات الملغاة";

$lang['admin_setting'] = "إعدادات";
$lang['admin_reports'] = "التقارير";

//User List
$lang['admin_user_list'] = "قائمة المستخدم";

$lang['fb_id'] = "معرف فب";
$lang['name'] = "اسم";
$lang['email'] = "البريد الإلكتروني";
$lang['phone'] = "هاتف";
$lang['last_login'] = "آخر تسجيل دخول";
$lang['otp'] = "مكتب المدعي العام";
$lang['is_login'] = "هو تسجيل الدخول";
$lang['is_active'] = "نشط";
$lang['action'] = "أفعال";

$lang['login'] = "تسجيل الدخول";
$lang['logout'] = "الخروج";
$lang['verified'] = "تم التحقق";
$lang['not_verified'] = "لم يتم التحقق منه";

$lang['active'] = "نشيط";
$lang['inactive'] = "غير نشط";

$lang['used'] = "مستخدم";
$lang['unused'] = "غير مستعمل";

$lang['map'] = "خريطة";
$lang['search'] = "بحث"; 

$lang['sure_to_active'] = "هل تريد بالتأكيد تنشيط هذا المستخدم؟";
$lang['sure_to_inactive'] = "هل تريد بالتأكيد تعطيل هذا المستخدم؟";
$lang['sure_to_delete'] = "هل أنت متأكد أنك تريد حذف؟";

/*Date: 31 march 2017*/

//Driver List
$lang['admin_driver_list'] = "قائمة السائقين";

$lang['car'] = "سيارة";
$lang['is_free'] = "بدون مقابل";
$lang['is_service'] = "هي الخدمة";
$lang['is_verified'] = "تم التحقق";
$lang['docs'] = "محرر المستندات";
$lang['new_docs'] = "محرر المستندات الجديد";
$lang['on'] = 'على';
$lang['off'] = 'إيقاف';
$lang['not_submitted'] = "لم يتم الإرسال"; 
$lang['yes'] = "نعم فعلا";
$lang['no'] = 'لا';

//Edit User
$lang['edit_user'] = "تحرير العضو";
$lang['fname'] = "الاسم الاول";
$lang['lname'] = "الكنية";
$lang['password'] = "كلمه السر";
$lang['cpassword'] = "تأكيد كلمة المرور";
$lang['profile_image'] = "صورة الملف الشخصي";
$lang['update'] = "تحديث";
$lang['back'] = "الى الخلف";

//Edit Driver
$lang['edit_driver'] = 'تحرير برنامج التشغيل';

//View User
$lang['view_user'] = "عرض المستخدم";
$lang['latitude'] = "خط العرض";
$lang['longitude'] = "خط الطول";
$lang['device_id'] = "معرف الجهاز";
$lang['device_type'] = "نوع الجهاز";
$lang['referal_code'] = "كود الإحالة";
$lang['wallet'] = "محفظة نقود";
$lang['token'] = "الرمز المميز";

$lang['car_model'] = "طراز السيارة";
$lang['car_color'] = "لون السيارة";
$lang['car_platenumber'] = "رقم لوحة السيارة";

//View Driver
$lang['view_driver'] = "مشاهدة ملف دريفر";

//Edit Documents
$lang['edit_document'] = "تحرير المستند";
$lang['registration_image'] = "صورة التسجيل";
$lang['vehicle_front_image'] = "صورة مركبة الجبهة";
$lang['vehicle_back_image'] = "صورة مركبة العودة";
$lang['licence_image'] = "صورة الترخيص";
$lang['driver_id_image'] = "صورة معرف برنامج التشغيل";
$lang['owner_id_image'] = "صورة رقم تعريف المالك";
$lang['is_document_verified'] = "تم التحقق من المستند";

/*Please select valid image*/
$lang['invalid_image'] = "الرجاء تحديد ملف صورة صالح";

//Vehicles List
$lang['add'] = "إضافة";
$lang['car_image_selected'] = "الصورة المحددة";
$lang['car_image_unselected'] = "صورة غير محددة";
$lang['car_name'] = "اسم";
$lang['base_fare'] = "أجرة قاعدة";
$lang['rate_per_km'] = "معدل لكل كيلومتر";
$lang['rate_per_min'] = "معدل لكل دقيقة";
$lang['waiting_min'] = "رسوم الانتظار لكل دقيقة";
$lang['car_order'] = "أمر سيارة";

//Edit Vehicle
$lang['edit_vehicle'] = "تحرير السيارة";

//Driver couponcard List
$lang['upload_file'] = "رفع ملف";
$lang['demo_link'] = "رابط تجريبي";
$lang['btn_close'] = "قريب";
$lang['btn_upload'] = "تحميل";
$lang['couponcard'] = "بطاقة القسيمة";
$lang['add_couponcard'] = "إضافة بطاقة القسيمة";
$lang['no_couponcard_found'] = "لم يتم العثور على قائمة بطاقة قسيمة السائق";
$lang['couponcard_added_successfully'] = "تمت إضافة بطاقة القسيمة بنجاح";
$lang['couponcard_updated'] = "تم تحديث بطاقة القسيمة بنجاح";
$lang['couponcard_delete_successfully'] = "تم حذف بطاقة القسيمة بنجاح";
$lang['couponcard_doesnot_exists'] = 'بروموكود الذي تحاول حذف غير موجود';
$lang['couponcards_exists'] = 'بطاقات القسيمة التي تحاول إضافتها موجودة بالفعل';
$lang['couponcards_format_not_allowed'] = 'معذرة! تنسيق بيانات كسف هذا غير مسموح به';

//Trip List
$lang['waiting'] = "انتظار";
$lang['assigned'] = "تعيين";
$lang['arrived'] = "وصلت";
$lang['processing'] = "معالجة";
$lang['completed'] = "منجز";
$lang['cancelled'] = "تم الالغاء";

$lang['trip_now'] = "رحلة الآن";
$lang['trip_later'] = "رحلة لاحقا";

$lang['driver'] = "سائق";
$lang['user'] = "المستعمل";
$lang['pickup'] = "امسك";
$lang['dropoff'] = "إنزال";
$lang['vehicle'] = "مركبة";
$lang['distance'] = "مسافه: بعد";
$lang['total'] = "مجموع";
$lang['payment'] = "دفع";
$lang['trip_type'] = "نوع الرحلة";
$lang['start_datetime'] = "وقت بدء الرحلة";
$lang['tripdatetime'] = "وقت الرحلة";
$lang['paid'] = "دفع";
$lang['unpaid'] = "غير مدفوع";
$lang['cancelled_by'] = "تم الإلغاء بواسطة";
$lang['reason'] = "السبب";

$lang['now'] = "الآن";
$lang['later'] = "في وقت لاحق";

/*Edit Trip*/
$lang['edit_trip'] = "تعديل الرحلة";

/*Assign Trip*/
$lang['assign_trip'] = "تعيين رحلة";
$lang['select_driver'] = "حدد برنامج التشغيل";

/*View Trip*/
$lang['view'] = "رأي";
$lang['promocode_id'] = "معرف بروموكود";
$lang['pickup_latitude'] = "لاقط خط العرض";
$lang['pickup_longitude'] = "بيك اب خط الطول";
$lang['dropoff_latitude'] = "إسقاط لاتيتيود";
$lang['dropoff_longitude'] = "قطرة من خط الطول";

$lang['start_trip'] = "بدء الرحلة";
$lang['end_trip'] = "نهاية الرحلة";
$lang['trip_duration'] = "مدة الرحلة";
$lang['promocode_amount'] = "بروموكود المبلغ";
$lang['tip'] = "تلميح";
$lang['tot_amount'] = "المبلغ الإجمالي";
$lang['transaction_id'] = "معرف المعاملة";
$lang['status'] = "الحالة";
$lang['waiting_time'] = "وقت الانتظار";

/*Promocode List*/
$lang['promocode'] = "رمز ترويجي";
$lang['promocode_type'] = "نوع بروموكود";
$lang['amount'] = "كمية";
$lang['select_amount'] = "حدد المبلغ";
$lang['start_date'] = "تاريخ البدء";
$lang['end_date'] = "تاريخ الانتهاء";
$lang['count'] = "العد";
$lang['description'] = "وصف";
$lang['no_promocode_found'] = "لم يتم العثور على بروموكود";

/*Edit Promocode*/
$lang['edit_promocode'] = "تحرير بروموكود";
 
/*Add Promocode*/
$lang['add_promocode'] = "إضافة بروموكود";

/*Used Promocode List*/
$lang['used_promocode_list'] = "تستخدم قائمة بروموكود";
$lang['date'] = "تاريخ";
$lang['datetime'] = "تاريخ / وقت";

/*Reports*/
$lang['report_list'] = "قائمة التقارير";
$lang['total_booking'] = "إجمالي الحجز";
$lang['go'] = "اذهب";
$lang['clear_all'] = "امسح الكل";
$lang['export'] = "تصدير";
$lang['export_excel'] = "تصدير إكسيل";
$lang['import_excel'] = "استيراد إكسيل";
$lang['export_pdf'] = "PDF تصدير";

/*Settings*/
$lang['settings'] = "إعدادات";
$lang['edit_settings'] = "تحرير الإعدادات";
$lang['user_settings'] = "إعدادات المستخدم";
$lang['driver_settings'] = "إعدادات برنامج التشغيل";
$lang['records_per_page'] = "تسجيلات لكل صفحة";
$lang['driver_percent'] = "نسبة السائق";
$lang['owner_percent'] = "نسبة المالك";
$lang['referal_amount'] = "مبلغ الإحالة";
$lang['base_distance'] = "قاعدة المسافة";
$lang['min_wallet'] = "دقيقة المحفظة";

/*Success and Error Message*/

//Vehicle
$lang['vehicle_added_successfully'] = "تمت إضافة السيارة بنجاح";
$lang['vehicle_delete_successfully'] = "تم حذف المركبة بنجاح";
$lang['vehicle_doesnot_exists'] = 'السيارة التي تحاول حذفها غير موجودة';
$lang['vehicle_carorder_exists'] = "أمر السيارة الذي تحاول تعديله موجود بالفعل";

//Driver
$lang['driver_updated_successfully'] = "تم تحديث برنامج التشغيل بنجاح";
$lang['driver_document_updated_successfully'] = "تم تحديث وثيقة برنامج التشغيل بنجاح";

$lang['driver_delete_successfully'] = "تم حذف برنامج التشغيل بنجاح";
$lang['driver_doesnot_exists'] = 'برنامج التشغيل الذي تحاول حذفه غير موجود';

$lang['profile_update_successfully'] = "تم تحديث الملف الشخصي بنجاح";

$lang['user_settings_updated'] = "تم تحديث إعدادات المستخدم بنجاح";
$lang['driver_settings_updated'] = "تم تحديث إعدادات برنامج التشغيل بنجاح";

$lang['trip_updated'] = "تم تحديث الرحلة بنجاح";

$lang['request_sent'] = "تم إرسال الطلب بنجاح";

$lang['trip_delete_successfully'] = "تم حذف الرحلة بنجاح";
$lang['trip_doesnot_exists'] = 'الرحلة التي تحاول حذفها غير موجودة';


$lang['promocode_updated'] = "تم تحديث بروموكود بنجاح";
$lang['promocode_delete_successfully'] = "تم حذف بروموكود بنجاح";
$lang['promocode_doesnot_exists'] = 'بروموكود الذي تحاول حذف غير موجود';

$lang['user_updated'] = "تم تحديث المستخدم بنجاح";
$lang['user_delete_successfully'] = "تم حذف المستخدم بنجاح";
$lang['user_doesnot_exists'] = 'المستخدم الذي تحاول حذفه غير موجود';

/*Admin Profile*/
$lang['edit_profile'] = "تعديل الملف الشخصي";
$lang['profile'] = "الملف الشخصي";

$lang['required_validation'] = "الرجاء إدخال قيمة";
?>