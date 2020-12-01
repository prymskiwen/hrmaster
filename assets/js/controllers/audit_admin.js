app.controller('auditadminController',  ['$scope', '$rootScope', 'hrmAPIservice', '$location','cookie','$routeParams','uiGridConstants', '$anchorScroll',function ($scope, $rootScope, hrmAPIservice, $location, cookie, $routeParams, uiGridConstants, $anchorScroll) {
    $scope.pageheader = "Audit Checklist Manager";
    $scope.pageError = 0;
    $scope.formActive = 0;
    $scope.master = {};
    $scope.editMode = 0; 
    $scope.showPrintChecklistMessage = 0;
    $scope.showchecklist = 0;
    $scope.showchecklistLoading = 0;
    $scope.printlist = [];

    var userData = cookie.checkLoggedIn();
    cookie.getPermissions();
    
    $scope.isAllowed = false;
    var perm = cookie.getCookie("permissions");
    if (!perm || perm['47'] == null || perm['47']==undefined)      $scope.isAllowed = false;
    else {
        if (perm['47'].r == '1') $scope.isAllowed = true;
        else                     $scope.isAllowed = false;
    }
    if(!$scope.isAllowed){
        cookie.deleteCookie('user');
        cookie.deleteCookie('permissions');
        $rootScope.isLoggedin = 0;
        $location.path('/');
    }

    $scope.loadPage = function(){        
        hrmAPIservice.getAdminChecklists(userData).then(function(response) {
            $scope.gridOptionsComplex.data = response.data.checklists;                        
        });
        $anchorScroll('top');
    }
    
    $scope.newChecklist = function(){
        
        var clname = prompt('Please enter name for new checklist', '');
        
        if (clname !== null) {
            if (clname.length < 2) {
                alert('Name must contain at least 2 characters');
                return;
            }
            
            hrmAPIservice.newAuditChecklist(clname, userData).then(function(response) {
                $scope.gridOptionsComplex.data = response.data.checklists; 
                $scope.checklistdetail = {};
                $scope.checklistdetail.id = response.data.checklist.id;
                $scope.checklistdetail.name = response.data.checklist.name;
                $scope.showchecklist = 1;
            });
        }
    }
    
    $scope.newChecklistGroup = function() {
        var clname = prompt('Please enter name for new group', '');
        
        if (clname !== null) {
            if (clname.length < 2) {
                alert('Name must contain at least 2 characters');
                return;
            }
            
            hrmAPIservice.newAuditChecklistGroup($scope.checklistdetail.id, clname, userData).then(function(response) {
                $scope.checkList = response.data.checklist;
                $scope.showchecklist = 1;
            });
        }        
    }
    
    $scope.newItem = function(groupDetail) {
        var clname = prompt('Please enter name for new item', '');
        
        if (clname !== null) {
            if (clname.length < 2) {
                alert('Name must contain at least 2 characters');
                return;
            }
            
            hrmAPIservice.newAuditChecklistItem(groupDetail, clname, userData).then(function(response) {
                $scope.checkList = response.data.checklist;
                $scope.showchecklist = 1;
            });
        }        
    }
    
    $scope.reorderItem = function(direction, obj) {
        // Direction: 0 - up : 1 - down
        hrmAPIservice.reorderAuditChecklistItem(direction, obj, userData).then(function(response) {
            $scope.checkList = response.data.checklist;
        });  
    }     
    
    
    
    

    $scope.editTemplate = function(row){
        $scope.showPrintChecklistMessage = 0;
        $scope.showchecklistLoading = 1;
        hrmAPIservice.getChecklist(row.id).then(function(response) {
            $scope.checkList = response.data.checklist;            
            $scope.clLength = Object.keys(response.data.checklist).length;
            
            $scope.showchecklistLoading = 0;
            $scope.checklistdetail = {};
            $scope.checklistdetail.id = row.id;
            $scope.checklistdetail.name = response.data.name.name;
            $scope.showchecklist = 1;
            $scope.editMode = 1;
        });
    }
    
    $scope.viewTemplate = function(row){
        $scope.showchecklist = 0;
        $scope.showPrintChecklistMessage = 0;
        $scope.showchecklistLoading = 1;
        $scope.editMode = 0;
        hrmAPIservice.getChecklist(row.id).then(function(response) {
            $scope.checkList = response.data.checklist;
            $scope.checklistdetail = {};
            $scope.showchecklistLoading = 0;
            $scope.checklistdetail.id = row.id;
            $scope.checklistdetail.name = response.data.name.name;
            $scope.showchecklist = 1;
        });
    }
    
    $scope.editChecklistName = function(){
        var clname = prompt('Please enter new name for "' + $scope.checklistdetail.name + '"', $scope.checklistdetail.name);
        
        if (clname !== null) {
            if (clname.length < 2) {
                alert('Name must contain at least 2 characters');
                return;
            }
            
            hrmAPIservice.updateAuditChecklistName(clname, $scope.checklistdetail, userData).then(function(response) {
                $scope.gridOptionsComplex.data = response.data.checklists;
                $scope.checklistdetail.id = response.data.name.id;
                $scope.checklistdetail.name = response.data.name.name;
            });
        }
    }
    
    $scope.bulkPrintChecklist = function(){

        if ($scope.printlist.length === 0) {
            alert('You must select at least 1 checklist to print');
            return;
        }
        
        var confirmCreate = confirm("Would you also like to create an Audit Activity item to enter your results later?"); 
        var doCreate = (confirmCreate) ? 1 : 0;
        
        $scope.showPrintChecklistMessage = 2;     
        hrmAPIservice.bulkPrintChecklists($scope.printlist, doCreate, userData).then(function(response) {
            $scope.printChecklistUrl = '<a href="'+response.data.url+'" target="_blank">Click to view <strong>Bulk Checklists</strong> in PDF format</a>';
            $scope.showPrintChecklistMessage = 1;
        });       
        
        
    }
    
    $scope.setCheckbox = function(id) {
        if ($scope.printlist.includes(id)) {
            $scope.printlist = $scope.printlist.filter(function(item) {
               return item !== id; 
            });
        } else {
            $scope.printlist.push(id);
        }
    }    
    
    $scope.editItem = function(obj){
        var clname = prompt('Please enter new name for "' + obj.name + '"', obj.name);
        
        if (clname !== null) {
            if (clname.length < 2) {
                alert('Name must contain at least 2 characters');
                return;
            }
            
            hrmAPIservice.updateAuditChecklistItem(clname, obj, userData).then(function(response) {
                $scope.checkList = response.data.checklist;
            });
        }
    }     
    
    
    
    var header1Template = '<div class="grid-header">Checklist <span class="glyphicon glyphicon-info-sign" aria-hidden="true" onmouseover="tooltip(1, `checklist-hover`);" onmouseout="tooltip(0, `checklist-hover`);"></span></div>';
    var headerActionTemplate = '<div class="grid-header">Action <span class="glyphicon glyphicon-info-sign" aria-hidden="true" onmouseover="tooltip(1, `action-hover`);" onmouseout="tooltip(0, `action-hover`);"></span></div>';       
    var headerFlagTemplate = '<div class="grid-header">Flag <span class="glyphicon glyphicon-info-sign" aria-hidden="true" onmouseover="tooltip(1, `flag-hover`);" onmouseout="tooltip(0, `flag-hover`);"></span></div>'; 
    
    $scope.gridOptionsComplex = {
        enableFiltering: true,
        showGridFooter: true,
        showColumnFooter: false,
        paginationPageSizes: [10, 20, 30],
        paginationPageSize: 10,
        onRegisterApi: function onRegisterApi(registeredApi) {
            gridApi = registeredApi;
        },
        columnDefs: [
          { name: 'id', visible: true, width: '10%', enableCellEdit: false, cellClass: 'center', type: 'number' }, 
          { name: 'name', width: '35%', enableCellEdit: false, displayName: 'Checklist Name', headerCellTemplate: header1Template, enableFiltering: true },
          { name: 'checklistType', width: '20%', enableCellEdit: false, enableFiltering: true, displayName: 'Checklist Type', cellClass: 'center', filter: {type: uiGridConstants.filter.SELECT,                                                                                                                                                     selectOptions: [{ value: 'Favourite', label: 'Favourite' }, { value: 'Template', label: 'Template' } ],} },
          { name: 'action', enableFiltering: false, width: '35%',  cellClass: 'center', enableCellEdit: false, displayName: 'Action', headerCellTemplate: headerActionTemplate,
              cellTemplate: '<div class="ui-grid-cell-contents grid-center-cell">\n\
                                <div ng-click="grid.appScope.editTemplate(row.entity)">\n\
                                    <span class="text-edit">Edit</span><span class="option-separator">|</span>\n\
                                </div>\n\
                                <div ng-click="grid.appScope.viewTemplate(row.entity)">\n\
                                    <span class="text-view">View</span><span class="option-separator">|</span>\n\
                                </div>\n\
                                <div ng-click="grid.appScope.printTemplate(row.entity)">\n\
                                    <span class="text-print">Print</span><span class="option-separator">|</span>\n\
                                </div>\n\
                                <div ng-click="grid.appScope.duplicateTemplate(row.entity)">\n\
                                    <span class="text-duplicate">Duplicate</span><span class="option-separator">|</span>\n\
                                </div>\n\
                                <div ng-click="grid.appScope.deleteAuditChecklist(row.entity)">\n\
                                    <span class="text-delete">Delete</span>\n\
                                </div>\n\
                            </div>'
          },         
                                      
        ]
    };    
    
    $scope.duplicateTemplate = function(obj) {
        var response = confirm('Duplicate checklist "' + obj.name + '"?  Once duplicated, the checklist will appear as a Favourite in the list and is fully editable for your purposes.');
        if (response) {
            hrmAPIservice.duplicateAuditChecklist(obj, userData).then(function(response) {
                $scope.gridOptionsComplex.data = response.data.checklists;
            });
        }
    }
    
    
    $scope.deleteAuditChecklist = function(obj) {
        var response = confirm('Are you sure you want to delete checklist  "' + obj.name + '"');
        
        if (response) {
            hrmAPIservice.deleteAuditChecklist(obj, userData).then(function(response) {
                $scope.gridOptionsComplex.data = response.data.checklists;
            });
        }        
    }
    
    $scope.deleteItem = function(obj) {
        var message = 'Delete checklist item "' + obj.name + '"?';
        if (obj.parent_id == 0) {
            var message = 'Delete checklist group "' + obj.name + '" and all items?'; 
        }
        
        var response = confirm(message);
        if (response) {
            hrmAPIservice.deleteChecklistItem(obj, userData).then(function(response) {
                $scope.checkList = response.data.checklist;
            });
        }        
    }
    
    
    $scope.printTemplate = function(item) {
        if (angular.isUndefined($scope.checklistdetail)) {
            $scope.checklistdetail = {};
        }
        $scope.checklistdetail.id = item.id;
        $scope.printChecklist();
        
    }
    
    $scope.printChecklist = function() {
        $scope.showPrintChecklistMessage = 2;
        $scope.printChecklistUrl = '';
        hrmAPIservice.printChecklist($scope.checklistdetail.id).then(function(response) {
            $scope.printChecklistUrl = '<a href="'+response.data.url+'" target="_blank">Click to view <strong>'+ response.data.checklist.name +'</strong> checklist in PDF format</a>';
            $scope.showPrintChecklistMessage = 1;
        });
    }
   
  
   
    var isUndefinedOrNull = function(val){
        return angular.isUndefined(val) || val === null;
    }    
    
    const setDateFld = function(date){
        if (isUndefinedOrNull(date) || date == '0000-00-00') {
            return '';
        }

        return new Date(date);        
    }    
        
    $scope.loadPage();
    

}]);