$(function() {
    window.app = $.extend(true, window.app || {}, {         
        ppaf: {
            grid: null,
            employeeList: [],
            userDetail: [],
            dateFields: ['dob','startdate','enddate','visaexpiry'],
            init: function() {
                var _this = this;
                this.grid();
                // calculate value of all questions 
                $('.question input[type="radio"]').click(function() { 
                   var currScore = 0;
                   var totalScore = 0;
                   $('.question input[type="radio"]:checked').each(function(i, val) {
                       var value = $(this).val() * 1;
                       currScore += value;
                       totalScore += 5;
                   });
                   
                   $('.current-score').html(currScore);
                   $('.total-score').html(totalScore);                   
                });
             
                $('#save').on("click", function(e) { 
                    $('#savemessage').html('&nbsp;').removeClass('fail');
                    e.preventDefault();
                    $('.error').removeClass('error');
                    var hasError = false;
                    var data = {};
                    var numQuestions = $('.question:not(.header)').length;
                    var questionsAnswered = $('.question:not(.header) input:checked').length;
                    
                    if (_.eq($('#id').val(),'0')) {
                        $('#savemessage').addClass('fail').html('Please select an employee from the list');
                        return;                        
                    }

                    if (!_.eq(numQuestions,questionsAnswered)) {
                        $('#savemessage').addClass('fail').html('Please complete all questions before proceeding');
                        return;
                    }
                    if (_.eq($('#overallRating').val(),'')) {
                        $('#savemessage').addClass('fail').html('Please select the employees rating');
                        return;                        
                    }                    

                    if (_.eq($('#employeeRecommendation').val(),'')) {
                        $('#savemessage').addClass('fail').html('Please select the employees recommended course of action');
                        return;                        
                    }                       
                    
                    $('.question input[type="radio"]:checked').each(function(i, val) {
                       var name = $(val).attr('name');                       
                       data[name] = $(val).val(); 
                    });                    
                    
                    var datapayload = {action: 'savePpaf', data: data, employee_id: $('#id').val(), notes: $('#notes').val(), rating: $('#overallRating').val(), recommendation: $('#employeeRecommendation').val()}
                    
                    var result = app.common.request({data: datapayload, async: false});
                    if (_.isUndefined(result)) {
                        $('#savemessage').html('Could not save PPAF answers').addClass('fail');
                    } else {
                        if (_.isNumber(result * 1)) {
                            $('#savemessage').addClass('success').html('This form has been successfully lodged. You will receive an email shortly.');
                            _this.clearForm();
                        } else {
                            $('#savemessage').addClass('fail').html('Could not lodge form');
                        }
                    }
                    $('#savemessage').show().delay(6000).fadeOut(1000,function() {
                       $(this).show().html('&nbsp;'); 
                    });
                    //$('#id').val(result);
                                                        
                });
  
                this.clearForm();
            },
            
            clearForm: function() {
                $('.error').removeClass('error');
                $('#id').val('0');
                $('select, textarea').val('');
                $('input[type="radio"]').attr('checked', false);
                $('#questionaire-header .detail').html('');
            },
            
            populateForm: function(data) {
                var obj = $.parseJSON(data);
                $('#id').val(obj.id);
                $('#employeeName').html(obj.FullName);
                $('#employeeStartDate').html(obj.startdate);
            },
            
            selectEmployee: function(id) {
                this.clearForm();
                $('input[type="radio"]').attr('checked', false); 
                var datapayload = {action: 'getEmployee', data: {employee_id: id }}                    
                var result = app.common.request({data: datapayload, async: false});
                this.populateForm(result);                                
            },
            grid: function() { 
                var empList = app.common.request({data: {action: 'getEmployees', customAction: 'Select', customClickAction: 'app.ppaf.selectEmployee'}, async: false});

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
        ppaf_admin: {
            questions: [],
            init: function() {
                var _this = this;
                $('#ppaf-admin-form select, #ppaf-admin-form input, #save').attr('disabled', true);
                this.grid();
                $('#save').on("click", function(e) { 
                    $('#savemessage').html('&nbsp;').removeClass('fail');
                    e.preventDefault();
                    $('.error').removeClass('error');
                    var hasError = false;
                    var data = {};
                    
                    if (_.eq($('#page').val(),'')) {
                        $('#savemessage').addClass('fail').html('Please select a page');
                        return;                        
                    }

                    if (_.eq(_.trim($('#question').val()),'')) {
                        $('#savemessage').addClass('fail').html('Please enter a question');
                        return;                        
                    }                    
                
                    $('#ppaf-admin-form select, #ppaf-admin-form input').each(function(idx, val) {
                        var elid = $(this).attr('id');
                        data[elid] = $(this).val();
                    });
                    
                    var datapayload = {action: 'saveQuestion', data: data};
                    
                    var result = app.common.request({data: datapayload, async: false});
                    if (_.isUndefined(result)) {
                        $('#savemessage').html('Could not save question').addClass('fail');
                    } else {
                        if (_.isNumber(result * 1)) {
                            $('#savemessage').addClass('success').html('Question has been saved successfully');
                            _this.clearForm();
                            _this.questions = app.common.request({data: {action: 'getQuestions', page: 'ppaf'}, async: false});
                            app.grid.refresh(_this.questions);
                        } else {
                            $('#savemessage').addClass('fail').html('Could not save question');
                        }
                    }
                    $('#ppaf-admin-form select, #ppaf-admin-form input, #save').attr('disabled', true); 
                    $('#savemessage').show().delay(6000).fadeOut(1000,function() {
                       $(this).show().html('&nbsp;');                        
                    });
                    //$('#id').val(result);
                                                        
                });
                
                $('#clear').click(function() {
                   $('#ppaf-admin-form select, #ppaf-admin-form input, #save').attr('disabled', false); 
                });
                
            },  
            edit: function(id) {
                $('#ppaf-admin-form select, #ppaf-admin-form input, #save').attr('disabled', false); 
                var questions = $.parseJSON(this.questions);
                var details = _.find(questions.rows, function(o) { return o.id == id; } );
                _.forEach(details.detail, function(val, key) {
                    if (_.eq(key,'page')) {
                        val = _.lowerCase(val);
                    } 
                    if ($('#' + key).length > 0) {
                        $('#' + key).val(val);
                    }
                });

            },
            remove: function(id) {
                var answer = confirm('Confirm removal of this question?');
                if (answer) {
                    this.questions = app.common.request({data: {action: 'removeQuestion', page: 'ppaf', id: id}, async: false});
                    app.grid.refresh(this.questions);
                }
            },            
            clearForm: function() {                
                $('select, input').val('');
                $('#id').val('0');
                $('#active').val('1');
            },            
            grid: function() { 
                this.questions = app.common.request({data: {action: 'getQuestions', page: 'ppaf'}, async: false});

                var griddata = {};
                griddata.htmlElement = 'gridbox';
                griddata.colHeaders = 'Page,Question,Order,Active,Action';
                griddata.filters = '#text_filter,#text_filter,#text_filter,#select_filter';
                griddata.colWidths = '100,560,100,100,119.8';
                griddata.colAlign = 'left,left,center,center,center';
                griddata.colTypes = 'txt,txt,txt,txt,txt';
                griddata.colSorting = 'str,str,str,str,str';
                griddata.gridData = this.questions;
                
                app.grid.render(griddata);
            }             
            
        }
        

    });
});