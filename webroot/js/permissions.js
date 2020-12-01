$(function() {
    window.app = $.extend(true, window.app || {}, { 
        
        permissions: {
            init: function() {
                $('#save').on("click", function(e) { 
                    e.preventDefault();
                    $('.error').removeClass('error');
                    var data = {};
                   
                    // First check the required values fields
                    var hasError = app.common.checkRequired()                    
                    if (_.eq(hasError,true)) {
                        return;
                    }
                    
                    var id = '';
                    var value = '';
                    $('input[type=checkbox]').each(function(idx, val) {
                        id = $(this).attr('id');
                        data[id] = ($(this).prop('checked')) ? 1 : 0;
                    });
                    data['usertype_id'] = $('#usertype').val();
                    if (_.eq($('#usertype').val(),'')) {
                        alert('error');
                        return;
                    }

                    
                    var result = app.common.request({data: {action: 'savePermissions', data: data}, async: false});
                    if (_.isUndefined(result)) {
                        $('#savemessage').html('Could not save data details').addClass('fail');
                    } else {
                        if (_.isNumber(result * 1)) {
                            $('#savemessage').addClass('success').html('Data details have been save successfully');
                            $('#clear').trigger('click');
                        } else {
                            $('#savemessage').addClass('fail').html('Could not save data details');
                        }
                    }
                    $('#savemessage').show().delay(6000).fadeOut(1000,function() {
                       $(this).show().html('&nbsp;'); 
                    });
                                    
                });
                $('#clear').click(function() {
                    $('input, select').val('');
                });   

                $('#clear').trigger('click');
            },

            editData: function(id) {
                var result = app.common.request({data: {action: 'getItem', id: id, table: 'data'}, async: false});
                var obj = $.parseJSON(result);
                _.forEach(obj, function(val,key) {
                    if (_.gt($('#' + key).length, 0)) {
                        $('#' + key).val(val);
                    } 
                });                                
            }            
        }
    });
});