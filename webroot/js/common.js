$(document).ready(function (){
        $('#login').click(function() {
            $('.login-message').removeClass('fail').removeClass('success').html('&nbsp;');
                var username = _.trim($('#username').val());
                var password = _.trim($('#password').val());
                //alert(username); return false;
                if (username === '' || password === '') {
                    $('.login-message').addClass('fail').html('You must enter a username and password.');
                    return;
                }

                var dataString = '&username=' + username + '&password=' + password;
                 
                $.ajax({
                    method: 'POST',
                    url: "http://localhost/Hr_master/index.php/login/ajax",
                    data: dataString,                    
                    success: function(data) { 
                    //alert(data); return false;                   
                   //  switch(data) {
                   //  case '0': $('.login-message').addClass('fail').html('Your username and password combination could not be found');  break;
                   //  case '1': $('.login-message').addClass('fail').html('Could not log you in - account not active');  break;
                   //  case '2': $('.login-message').addClass('fail').html('Your account is currently locked. Try again in 5 minutes');  break;
                   //  default: $('.login-message').addClass('success').html('Success! Logging in...');                                   
                   //  //this.setPermissions(result);
                   //  $(location).attr('href', '/' + this.DEFAULT_HOME_PAGE);
                   //  break;
                   // }
                    }
                    
                });     
                
                //var result = app.common.request({data: {action: 'login', user: username, pw: password}, async: false});
               
                
        });
        $('#forgotpassword').click(function() {
            app.account.forgotPassword();
        });
    });

$(function () {
    var sf_menu_sub = $('.sf-menu-main');
    $('.nav-btn').on('click', function (e) {
        e.stopPropagation();
        sf_menu_sub.toggle();
    });
    $(document).on('click', function (e) {
        sf_menu_sub.hide();
    });
});
//             setPermissions: function(data) {
//                 var detail = $.parseJSON(data);
//                 this.currentUser = detail.user;
//                 this.permissions = detail.permissions;
//                 this.defaultHomePage = detail.defaultHome;
                
//             },
//             forgotPassword: function() {
//                 $('#forgotpasswordmodal').modal('show');

//                 "use strict";
//                 var options = {};
//                 options.ui = {
//                     container: "#pwd-container",
//                     showVerdictsInsideProgressBar: true,
//                     viewports: {
//                         progress: ".pwstrength_viewport_progress"
//                     },
//                     progressBarExtraCssClasses: "progress-bar-striped active"
//                 };
//                 options.common = {
//                     debug: false,
//                     onLoad: function () {
//                         $('#messages').text('Start typing password');
//                     }
//                 };

//                 $(':password').pwstrength(options);            
//                 $("#pw").pwstrength("forceUpdate");                
//             }
//         },        
//         grid: { 
//             grid: null,
//             render: function(data) {
//                 this.grid = new dhtmlXGridObject(data.htmlElement);
//                 this.grid.setImagePath("javascript/dhtml/codebase/imgs/");
//                 this.grid.setHeader(data.colHeaders);
//                 this.grid.attachHeader(data.filters);
//                 this.grid.setInitWidths(data.colWidths);
//                 this.grid.enableAutoWidth(true);
//                 this.grid.setColAlign(data.colAlign);
//                 this.grid.setColTypes(data.colTypes);

//                 this.grid.setColSorting(data.colSorting);
//                 this.grid.init();
//                 this.grid.enableSmartRendering(true);
//                 this.grid.parse(data.gridData, 'json');

//             },
//             refresh: function(list) {
//                 this.grid.clearAll();
//                 this.grid.parse(list, 'json');  
//                 this.grid.filterByAll();
//             }
            
            
//         },
//         common: { 
//             checkRequired: function() {
//                 var hasError = false;
//                 $('.required').each(function(idx, val) { 
//                     var dat = _.trim($(this).val());
//                     if (_.eq(dat,'')) {
//                         hasError = true;
//                         $(this).addClass('error');
//                     }
//                 });
//                 return hasError;
//             },
            
//             parseDate: function(dateStr) {
//                 var dateParts = dateStr.split("/");
//                 return new Date(dateParts[2], (dateParts[1] - 1), dateParts[0]);
//             },       
//             calculateAge: function(dateOfBirth, dateToCalculate) {
//                 var calculateYear = dateToCalculate.getFullYear();
//                 var calculateMonth = dateToCalculate.getMonth();
//                 var calculateDay = dateToCalculate.getDate();

//                 var birthYear = dateOfBirth.getFullYear();
//                 var birthMonth = dateOfBirth.getMonth();
//                 var birthDay = dateOfBirth.getDate();

//                 var age = calculateYear - birthYear;
//                 var ageMonth = calculateMonth - birthMonth;
//                 var ageDay = calculateDay - birthDay;

//                 if (ageMonth < 0 || (ageMonth == 0 && ageDay < 0)) {
//                     age = parseInt(age) - 1;
//                 }                
//                 return age;
//             },
//             isDate: function isDate(txtDate) {
//                 var currVal = txtDate;
//                 if (_.eq(currVal,'')) {
//                     return true;
//                 }

//                 //Declare Regex
//                 var rxDatePattern = /^(\d{1,2})(\/|-)(\d{1,2})(\/|-)(\d{4})$/;
//                 var dtArray = currVal.match(rxDatePattern); // is format OK?

//                 if (_.isNull(dtArray)) {
//                     return false;
//                 }        
                
//                 //Checks for dd/mm/yyyy format.
//                 var dtDay = dtArray[1];
//                 var dtMonth = dtArray[3];
//                 var dtYear = dtArray[5];

//                 if (!_.inRange(dtMonth, 1,12)) {
//                     return false;
//                 } else if (!_.inRange(dtDay,1,31)) {
//                     return false;
//                 } else if ((_.indexOf([4,6,9,11], dtMonth) > -1) && (_.eq(dtDay,31))) {
//                       return false;
//                 } else if (_.eq(dtMonth,2)) {
//                     var isleap = (dtYear % 4 == 0 && (dtYear % 100 != 0 || dtYear % 400 == 0));
//                     if (dtDay > 29 || (dtDay == 29 && !isleap)) {
//                         return false;
//                     }
//                 }
//                 return true;
//             },          
            
//             calendar: function(attribs) {
//                 var cal = new dhtmlXCalendarObject({input: attribs.input, button: attribs.button});
//                 //cal.setSkin("dhx_skyblue");
//                 cal.setDateFormat("%d/%m/%Y");
//                 cal.showToday();  

//                 if (!_.isUndefined(attribs.click)) { 
//                     cal.attachEvent('onClick', attribs.click);
//                 }
//             },
//             prefixDate: function(t) { 
//                 if (_.isUndefined(t)) {
//                     return;
//                 }
//                 if (t.length < 2) {
//                     t = '0' + t;
//                 }   
//                 return t;                
//             },
//             convertDateToDB: function(date) {
//                 if (_.eq(date,'')) {
//                     return '0000-00-00';
//                 }
            
//                 var dateSplit = date.split('/');
//                 return dateSplit[2] + '-' + this.prefixDate(dateSplit[1]) + '-' + this.prefixDate(dateSplit[0]);
//             },
//             convertDBToDate: function(val) {
//                 if (_.eq(val,'0000-00-00')) {
//                     return '';
//                 }
//                 var dateSplit = val.split('-');
//                 return dateSplit[2] + '/' + dateSplit[1] + '/' + dateSplit[0];                
//             },
//         	request: function(data) {
//                // alert(data); return false;
//                 var returndata = '';
//         		$.ajax({
//                     method: 'POST',
//                     url: "<?php= base_url()?>/login/ajax",
//                     data: (_.isUndefined(data.data)) ? {} : data.data,
//                     dataType: 'json',
//                     async: (_.isUndefined(data.async)) ? true : data.async,
//                     success: function(data) { 
//                     alert(data); return false;                   
//                         returndata = data.responseText;
//                     },
//                     fail: function(data) {
                        
//                     },
//                     complete: function(data) {
//                         returndata = data.responseText;
//                     }
//                 });        	
//                 return returndata;
//             }        
//         },

//     });
// });