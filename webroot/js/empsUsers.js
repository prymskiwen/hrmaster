$(function() {
    window.app = $.extend(true, window.app || {}, {         
        employees: {
            grid: null,
            employeeList: [],
            userDetail: [],
            dateFields: ['dob','startdate','enddate','visaexpiry'],
            updateAge: function() {
                var dob = $('#dob').val();
                if (app.common.isDate(dob)) {
                    var age = app.common.calculateAge(app.common.parseDate(dob), new Date());
                    $('#age').val(age);   
                } else {
                    $('#age').val('');   
                }
            },  
            init: function() {
                this.grid();
                myTabbar = new dhtmlXTabBar({
                    parent: "emptabs",
                    close_button: false,
                    tabs: [
                        {id: "a1", text: "Personal Detail", active: true},
                        {id: "a2", text: "Work Detail"},
                        {id: "a3", text: "Quals/Licence"}
                    ]
                });
                
                app.common.calendar({input: 'dob', button: 'dobcal', 'click': app.employees.updateAge });
                app.common.calendar({input: 'visaexpiry', button: 'visacal'});
                app.common.calendar({input: 'startdate', button: 'startcal'});
                app.common.calendar({input: 'enddate', button: 'endcal'});
             
                $('#save').on("click", function(e) { 
                    e.preventDefault();
                    $('.error').removeClass('error');
                    var hasError = false;
                    var data = {};
                    var personal = {};
                    var work = {};
                    
                    // First check the required values fields
                   /* $('#emptabs .required').each(function(idx, val) {  
                        if (_.trim($(this).val()) === '') {
                            hasError = true;
                            $(this).addClass('error');
                        }
                    });*/
                    
                    // First check the required values fields
                    var hasError = app.common.checkRequired()                    
                    if (_.eq(hasError,true)) {
                        return;
                    }
                    
                    var id = '';
                    var value = '';
                    $('#emptabs #personal input:not(".dbignore"), #emptabs #personal select:not(".dbignore")').each(function(idx, val) {
                        id = $(this).attr('id');
                        value = (_.indexOf(app.employees.dateFields, id) >= 0) ? app.common.convertDateToDB($(this).val()) : $(this).val();
                        personal[id] = value;                       
                    });
                    $('#emptabs #workdetail input:not(".dbignore"), #emptabs #workdetail select:not(".dbignore")').each(function(idx, val) {
                        id = $(this).attr('id');
                        value = (_.indexOf(app.employees.dateFields, id) >= 0) ? app.common.convertDateToDB($(this).val()) : $(this).val();
                        work[id] = value;                     
                    });    
                    
                    var result = app.common.request({data: {action: 'saveEmployee', work: work, personal: personal}, async: false});
                    if (_.isUndefined(result)) {
                        $('#savemessage').html('Could not save employee details').addClass('fail');
                    } else {
                        if (_.isNumber(result * 1)) {
                            $('#savemessage').addClass('success').html('Employee details have been save successfully');
                            $('#clear').trigger('click');
                        } else {
                            $('#savemessage').addClass('fail').html('Could not save employee details');
                        }
                    }
                    $('#savemessage').show().delay(6000).fadeOut(1000,function() {
                       $(this).show().html('&nbsp;'); 
                    });
                    $('#id').val(result);
                    
                    var empList = app.common.request({data: {action: 'getEmployees'}, async: false});
                    app.employees.employeeList = empList;
                    app.grid.refresh(empList);
                                    
                });
                $('#clear').click(function() {
                    $('.error').removeClass('error');
                    $('#emptabs input, #emptabs select').val('');
                    $('#id').val('0');
                });   
                $('.editEmployee').click(function() {
                   var id = $(this).data('id'); 
                   var result = app.common.request({data: {action: 'getItem', id: id, table: 'EmployeeWork'}, async: false});
                   var empObj = $.parseJSON(result);
                   _.forEach(empObj, function(val,key) {
                      if (_.indexOf(app.employees.dateFields, key) > -1) {
                          val = app.common.convertDBToDate(val);
                      } 
                      if (_.gt($('#' + key).length, 0)) {
                          $('#' + key).val(val);
                      } 

                      // Calculate the age as an exception
                      if (_.eq(key, 'dob')) {
                          app.employees.updateAge();
                      }
                   });
                   
                });
                $('#clear').trigger('click');
            },
            grid: function() { 
                var empList = app.common.request({data: {action: 'getEmployees'}, async: false});
                app.employees.employeeList = empList;

                var griddata = {};
                griddata.htmlElement = 'gridbox';
                griddata.colHeaders = 'Name,Phone,Email,State,Gender,Action';
                griddata.filters = '#text_filter,#text_filter,#text_filter,#select_filter';
                griddata.colWidths = '260,120,230,160,90,119.8';
                griddata.colAlign = 'left,center,center,center,center,center';
                griddata.colTypes = 'txt,txt,txt,txt,txt,txt';
                griddata.colSorting = 'str,str,str,str,str,str';
                griddata.gridData = empList;
                
                app.grid.render(griddata);
            } 
        },
        employee_dashboard: {
        	init: function() {
                	app.employees.grid();
	                myTabbar = new dhtmlXTabBar({
	                    parent: "emptabs",
	                    close_button: false,
	                    tabs: [
	                        {id: "a1", text: "Personal Detail", active: true},
	                        {id: "a2", text: "Work Detail"},
	                        {id: "a3", text: "Quals/Licence"}
	                    ]
	                });
	                             	                
	                myLogbar = new dhtmlXTabBar({
	                    parent: "emplogtabs",
	                    close_button: false,
	                    tabs: [
	                        {id: "a1", text: "PPAF Page", active: true},
	                        {id: "a2", text: "General"},
	                        {id: "a3", text: "Leave"},
	                        {id: "a4", text: "Safety"},
	                        {id: "a5", text: "Cessation"},
	                        {id: "a6", text: "Dispute"},
	                        {id: "a7", text: "Toolbox Talk"}
	                        	                        
	                    ]
	                });
	                app.common.calendar({input: 'leavestart', button: 'leavestartcal'});
        	        app.common.calendar({input: 'leaveend', button: 'leaveendcal'});	                
	        	
        	
        	}
        
        
        },
        users: {
            userList: [],
            dateFields: ['dob'],
            init: function() {
                this.grid();
                app.common.calendar({input: 'dob', button: 'dobcal'});
                $('#save').on("click", function(e) { 
                    e.preventDefault();
                    $('.error').removeClass('error');
                    var hasError = false;
                    var data = {};
                    
                    // First check the required values fields
                    /*$('.main-content .required').each(function(idx, val) {  
                        if (_.trim($(this).val()) === '') {
                            hasError = true;
                            $(this).addClass('error');
                        }
                    });*/
                    
                    // First check the required values fields
                    var hasError = app.common.checkRequired()                    
                    if (_.eq(hasError,true)) {
                        return;
                    }
                    
                    var id = '';
                    var value = '';
                    $('.main-content input:not(".dbignore"), .main-content select:not(".dbignore")').each(function(idx, val) {
                        id = $(this).attr('id');
                        value = (_.indexOf(app.users.dateFields, id) >= 0) ? app.common.convertDateToDB($(this).val()) : $(this).val();
                        data[id] = value;                       
                    });
                    
                    var result = app.common.request({data: {action: 'saveUser', user: data}, async: false});
                    if (_.isUndefined(result)) {
                        $('#savemessage').html('Could not save user details').addClass('fail');
                    } else {
                        if (_.isNumber(result * 1)) {
                            $('#savemessage').addClass('success').html('User details have been save successfully');
                            $('#clear').trigger('click');
                        } else {
                            $('#savemessage').addClass('fail').html('Could not save user details');
                        }
                    }
                    $('#savemessage').show().delay(6000).fadeOut(1000,function() {
                       $(this).show().html('&nbsp;'); 
                    });
                    $('#id').val(result);
                    
                    var userList = app.common.request({data: {action: 'getUsers'}, async: false});
                    app.users.userList = userList;
                    app.grid.refresh(userList);
                                    
                });
                $('#clear').click(function() {
                    $('.error').removeClass('error');
                    $('.main-content input, .main-content select').val('');
                    $('#id').val('0');
                });   
                $('#clear').trigger('click');
                 
                
            },
            grid: function() {
                var userList = app.common.request({data: {action: 'getUsers'}, async: false});
               
                var griddata = {};
                griddata.htmlElement = 'usergrid';
                griddata.colHeaders = 'Name,Phone,Email,State,Gender,Action';
                griddata.filters = '#text_filter,#text_filter,#text_filter,#select_filter';
                griddata.colWidths = '260,120,230,160,90,119.8';
                griddata.colAlign = 'left,center,center,center,center,center';
                griddata.colTypes = 'txt,txt,txt,txt,txt,txt';
                griddata.colSorting = 'str,str,str,str,str,str';
                griddata.gridData = userList;
                
                app.grid.render(griddata);               
            },
            editUser: function(id) {
                var result = app.common.request({data: {action: 'getItem', id: id, table: 'user'}, async: false});
                var userObj = $.parseJSON(result);
                _.forEach(userObj, function(val,key) {
                    if (_.indexOf(app.users.dateFields, key) > -1) {
                        val = app.common.convertDBToDate(val);
                    } 
                    if (_.gt($('#' + key).length, 0)) {
                        $('#' + key).val(val);
                    } 
                });                                
            }
            
            
            
        }
    });
});