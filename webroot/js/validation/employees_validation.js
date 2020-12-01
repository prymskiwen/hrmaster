$('#employee_add_form').on('submit', function(){
	var emp_fname = $("#emp_fname").val();
	if(emp_fname == "" )
	{
		$("#errormsg_emp_fname").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}

	var emp_lname = $("#emp_lname").val();
	if(emp_lname == "" )
	{
		$("#errormsg_emp_lname").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}

	var emp_email = $("#emp_email").val();
	if(emp_email == "" )
	{
		$("#errormsg_emp_email").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}

	var emp_phone = $("#emp_phone").val();
	if(emp_phone == "" )
	{
		$("#errormsg_emp_phone").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}

	var emp_address = $("#emp_address").val();
	if(emp_address == "" )
	{
		$("#errormsg_emp_address").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}

	var emp_suburb = $("#emp_suburb").val();
	if(emp_suburb == "" )
	{
		$("#errormsg_emp_suburb").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}

	var emp_state_id = $("#emp_state_id").val();
	if(emp_state_id == "" )
	{
		$("#errormsg_emp_state_id").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}

	var emp_postcode = $("#emp_postcode").val();
	if(emp_postcode == "" )
	{
		$("#errormsg_emp_postcode").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}

	var emp_title = $("#emp_title").val();
	if(emp_title == "" )
	{
		$("#errormsg_emp_title").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}

	var emp_gender = $("#emp_gender").val();
	if(emp_gender == "" )
	{
		$("#errormsg_emp_gender").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}

	var emp_dob = $("#emp_dob").val();
	if(emp_dob == "" )
	{
		$("#errormsg_emp_dob").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}

	var emp_age = $("#emp_age").val();
	if(emp_age == "" )
	{
		$("#errormsg_emp_age").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}

	var emp_nationality = $("#emp_nationality").val();
	if(emp_nationality == "" )
	{
		$("#errormsg_emp_nationality").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}

	var emp_visa_type = $("#emp_visa_type").val();
	if(emp_visa_type == "" )
	{
		$("#errormsg_emp_visa_type").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}

	var emp_visa_expiry = $("#emp_visa_expiry").val();
	if(emp_visa_expiry == "" )
	{
		$("#errormsg_emp_visa_expiry").css({"display":"block"});
		$("#tab1_h").addClass('active');
		$("#tab1").addClass('in active');
		$("#tab2_h").removeClass('active');
		$("#tab2").removeClass('active');
		return false;
	}	












	
	var empw_start_date = $("#empw_start_date").val();
	if(empw_start_date == "" )
	{
		$("#errormsg_empw_start_date").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}	
	
	/*
	var empw_end_date = $("#empw_end_date").val();
	if(empw_end_date == "" )
	{
		$("#errormsg_empw_end_date").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}
	*/	
	
	var empw_hourly_rate = $("#empw_hourly_rate").val();
	if(empw_hourly_rate == "" )
	{
		$("#errormsg_empw_hourly_rate").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}	
	
	var empw_weekly_rate = $("#empw_weekly_rate").val();
	if(empw_weekly_rate == "" )
	{
		$("#errormsg_empw_weekly_rate").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}	
	
	var empw_anual_rate = $("#empw_anual_rate").val();
	if(empw_anual_rate == "" )
	{
		$("#errormsg_empw_anual_rate").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}	
	
	/*
	var empw_bonus = $("#empw_bonus").val();
	if(empw_bonus == "" )
	{
		$("#errormsg_empw_bonus").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}	
	*/
	
	/*
	var empw_commission = $("#empw_commission").val();
	if(empw_commission == "" )
	{
		$("#errormsg_empw_commission").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}
	*/	
	
	var empw_hours_per_week = $("#empw_hours_per_week").val();
	if(empw_hours_per_week == "" )
	{
		$("#errormsg_empw_hours_per_week").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}	
	
	var empw_position = $("#empw_position").val();
	if(empw_position == "" )
	{
		$("#errormsg_empw_position").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}	
	
	var empw_level = $("#empw_level").val();
	if(empw_level == "" )
	{
		$("#errormsg_empw_level").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}	
	
	var empw_department = $("#empw_department").val();
	if(empw_department == "" )
	{
		$("#errormsg_empw_department").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}	
	
	var empw_state = $("#empw_state").val();
	if(empw_state == "" )
	{
		$("#errormsg_empw_state").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}	
	
	var empw_entitle = $("#empw_entitle").val();
	if(empw_entitle == "" )
	{
		$("#errormsg_emp_visa_expiry").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}	
	
	var empw_emp_type = $("#empw_emp_type").val();
	if(empw_emp_type == "" )
	{
		$("#errormsg_empw_emp_type").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}	
	
	/*
	var empw_anual_leave_owing = $("#empw_anual_leave_owing").val();
	if(empw_anual_leave_owing == "" )
	{
		$("#errormsg_empw_anual_leave_owing").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}	
	*/
	
	/*
	var empw_personal_leave_owing = $("#empw_personal_leave_owing").val();
	if(empw_personal_leave_owing == "" )
	{
		$("#errormsg_empw_personal_leave_owing").css({"display":"block"});
		$("#tab2_h").addClass('active');
		$("#tab2").addClass('in active');
		$("#tab1_h").removeClass('active');
		$("#tab1").removeClass('active');
		return false;
	}	
	*/
	
	
});
