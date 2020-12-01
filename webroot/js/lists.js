$(function() {
    window.app = $.extend(true, window.app || {}, {         
        lists: {
            grid: null,
            init: function() {
                var _this = this;
                
                $('#type').change(function() {
                    $('#selecttype').empty();
                    $('#value, #display_text').val('');
                    var result = app.common.request({ data: { action: 'getListData', type: $(this).val()}, async: false });
                    var obj = $.parseJSON(result);
                    $('#selecttype').append($('<option>', {value: '', text: 'Please select..'}));
                    $('#selecttype').append($('<option>', {value: '0', text: '-- Add New --'}));
                    _.forEach(obj, function(o) {                        
                        $('#selecttype').append($('<option>', {value: o.id, text: o.display_text}));
                    });                    
                });

                $('#selecttype').change(function() {
                    $('#value, #display_text').val('');
                    var result = app.common.request({ data: { action: 'getListData', type: $('#type').val()}, async: false });
                    var obj = $.parseJSON(result);
                    // There will only be 1 result returned
                    _.forEach(obj, function(o) { 
                        $('#value').val(o.value);
                        $('#display_text').val(o.display_text);
                    });
                });                
                
             
                $('#save').on("click", function(e) { 
                    $('#savemessage').html('&nbsp;').removeClass('fail');
                    e.preventDefault();
                    $('.error').removeClass('error');
                    var hasError = false;
                    var data = { id: $('#selecttype').val(), value: $('#value').val(), display_text: $('#display_text').val() };     
                    
                    var datapayload = {action: 'saveData', data: data}
                    
                    var result = app.common.request({data: datapayload, async: false});
                    if (_.isUndefined(result)) {
                        $('#savemessage').html('Could not save list item').addClass('fail');
                    } else {
                        if (_.isNumber(result * 1)) {
                            $('#savemessage').addClass('success').html('List item saved successfully');
                            _this.clearForm();
                        } else {
                            $('#savemessage').addClass('fail').html('Could not save list item');
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
                $('select, input').val('');
            }
            
        }        

    });
});