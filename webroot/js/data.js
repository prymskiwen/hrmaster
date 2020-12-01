$(function() {
    window.app = $.extend(true, window.app || {}, { 
        
        data: {
            grid: null,
            init: function() {
                this.grid();
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
                    $('.main-content input:not(".dbignore"), .main-content select:not(".dbignore")').each(function(idx, val) {
                        id = $(this).attr('id');                        
                        data[id] = $(this).val();
                    });

                    
                    var result = app.common.request({data: {action: 'saveData', data: data}, async: false});
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
                    $('#id').val(result);
                    
                    var dataList = app.common.request({data: {action: 'getSiteData'}, async: false});
                    app.grid.refresh(dataList);
                                    
                });
                $('#clear').click(function() {
                    $('.error').removeClass('error');
                    $('input, select').val('');
                    $('#id').val('0');
                });   

                $('#clear').trigger('click');
            },
            grid: function() { 
                var dataList = app.common.request({data: {action: 'getSiteData'}, async: false});
                
                var griddata = {};
                griddata.htmlElement = 'datagrid';
                griddata.colHeaders = 'Data Type,Data Value, Display Text, Action';
                griddata.filters = '#select_filter,&nbsp;,#text_filter';
                griddata.colWidths = '240,240,230,160';
                griddata.colAlign = 'left,center,center,center';
                griddata.colTypes = 'txt,txt,txt,txt';
                griddata.colSorting = 'str,str,str,str';
                griddata.gridData = dataList;
                
                app.grid.render(griddata);
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