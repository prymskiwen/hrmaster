app.controller('auditactionController',  ['$scope', '$rootScope', 'hrmAPIservice', '$location','cookie','$routeParams','uiGridConstants', '$anchorScroll',function ($scope, $rootScope, hrmAPIservice, $location, cookie, $routeParams, uiGridConstants, $anchorScroll) {
    $scope.pageheader = "Conduct Audit";
    $scope.pageError = 0; 
    $scope.formActive = 0;
    $scope.master = {};
    $scope.audit = {};
    $scope.action = {};
    $scope.editMode = 0;
    $scope.viewOnly = 0;
    $scope.showPrintChecklistMessage = 0;
    $scope.showchecklist = 0;
    $scope.showchecklistLoading = 0;
    $scope.newAuditAction = 0;

    var userData = cookie.checkLoggedIn();
    cookie.getPermissions();
    var perm = cookie.getCookie("permissions");
    console.log(perm);
    if (!perm || perm['48'] == null || perm['48']==undefined)      $scope.isAllowed = false;
    else {
        if (perm['48'].r == '1') $scope.isAllowed = true;
        else                     $scope.isAllowed = false;
    }
    if(!$scope.isAllowed){
        cookie.deleteCookie('user');
        cookie.deleteCookie('permissions');
        $rootScope.isLoggedin = 0;
        $location.path('/');
    }
    
    $scope.loadPage = function(){  
        hrmAPIservice.getActionChecklists(userData).then(function(response) {
            $scope.cl = {}
            $scope.cl.checklists = response.data.checklists;  
            $scope.gridOptionsComplex.data = response.data.actionlists;
            $scope.employeeList = response.data.employees;                    
        });        

        $anchorScroll('top');
    }
    
    
    $scope.newAudit = function(){
        $scope.newAuditAction = 1;
        $scope.audit = {};
        $scope.audit.checklist_id = '0';
    }
    
/*  $scope.confirmSelectedAudit = () => {
        $scope.newAuditAction = 1;
    } */   
    
    $scope.confirmSelectedAudit = function(){
        $scope.showchecklistLoading = 1;
        hrmAPIservice.createAudit($scope.audit.checklist_id, userData).then(function(response) {
            $scope.newAuditAction = 0;
            $scope.gridOptionsComplex.data = response.data.actionlists; 
            renderChecklist(response); 
        });        
    }

    renderChecklist = function(response){
        $scope.chk = angular.copy({});
        $scope.action = angular.copy({});
        $scope.showchecklist = 1;
        $scope.showchecklistLoading = 0;

        $scope.checklistdetail = angular.copy(response.data.checklist);             
        $scope.checkList = angular.copy(response.data.items);

        for(let i=0; i<response.data.items.length; i++) {
            for(let j=0; j<response.data.items[i].subitems.length; j++) {
                let item = {id: response.data.items[i].subitems[j].id};
                $scope.setCheckbox(item, response.data.items[i].subitems[j].is_ok);
                $scope.chk[item.id].needs = response.data.items[i].subitems[j].needs; 

                // Proposed completion date
                if (isUndefinedOrNull(response.data.items[i].subitems[j].proposed_completion_date) || 
                        response.data.items[i].subitems[j].proposed_completion_date == '0000-00-00') {

                } else {
                    $scope.chk[item.id].proposed_completion_date = new Date(response.data.items[i].subitems[j].proposed_completion_date);
                }

                // Date of Completion
                if (isUndefinedOrNull(response.data.items[i].subitems[j].date_of_completion) || 
                        response.data.items[i].subitems[j].date_of_completion == '0000-00-00') {

                } else {
                    $scope.chk[item.id].date_of_completion = new Date(response.data.items[i].subitems[j].date_of_completion);
                }      

                $scope.chk[item.id].allocated_to_id = response.data.items[i].subitems[j].allocated_to_id; 
            }
        }

        $scope.action.other_issues = response.data.checklist.other_issues;
        $scope.action.completed_by_id = response.data.checklist.completed_by_id;
        $scope.action.complete = response.data.checklist.complete;

        if (isUndefinedOrNull(response.data.checklist.next_audit_date) || response.data.checklist.next_audit_date == '0000-00-00') {
        } else {
            $scope.action.next_audit_date = new Date(response.data.checklist.next_audit_date);
        }        
    }    
    
    
    
    $scope.viewTemplate = function(row){
        $scope.showchecklist = 0;
        $scope.showPrintChecklistMessage = 0;
        $scope.showchecklistLoading = 1;
        $scope.editMode = 0;
        hrmAPIservice.getChecklist(row.id).then(function(response) {
            $scope.checkList = angular.copy({});
            $scope.checkList = angular.copy(response.data.checklist);
            $scope.checklistdetail = angular.copy({});
            $scope.showchecklistLoading = 0;
            $scope.checklistdetail.id = row.id;
            $scope.checklistdetail.name = response.data.name.name;
            $scope.showchecklist = 1;
        });
    }
   
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
          { name: 'id', width: '10%', cellClass: 'center', type: 'number', enableCellEdit: false, },
          { name: 'ChecklistName', width: '30%', enableCellEdit: false, displayName: 'Checklist Name',  },
          { name: 'Status', width: '15%', enableCellEdit: false, enableFiltering: true, cellClass: 'center',},
          { name: 'completed_date', width: '20%', enableCellEdit: false, enableFiltering: true, cellClass: 'center', type:'date', cellFilter: 'date:"dd/MM/yyyy"' },
          { name: 'action', enableFiltering: false, width: '25%',  cellClass: 'center', enableCellEdit: false, displayName: 'Action',
              cellTemplate: '<div class="ui-grid-cell-contents grid-center-cell">\n\
                                <div ng-click="grid.appScope.editAuditAction(row.entity)" ng-hide="row.entity.Status == \'Complete\'">\n\
                                    <span class="text-edit">Enter results</span><span class="option-separator" ng-hide="false">|</span>\n\
                                </div>\n\ \n\
                                <div ng-click="grid.appScope.viewAuditAction(row.entity)" ng-hide="row.entity.Status != \'Complete\'">\n\
                                    <span class="text-edit">View</span><span class="option-separator" ng-hide="false">|</span>\n\
                                </div>\n\
                                <div ng-click="grid.appScope.archiveAuditAction(row.entity)" ng-hide="true">\n\
                                    <span class="text-archive">Archive</span><span class="option-separator" ng-hide="row.entity.Status == \'Complete\'">|</span>\n\
                                </div>\n\
                                <div ng-click="grid.appScope.deleteAuditAction(row.entity)" ng-hide="row.entity.Status == \'Complete\'">\n\
                                    <span class="text-delete">Delete</span>\n\
                                </div>\n\
                            </div>'
          }
        ]
    };    
    

    
    
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
    
    $scope.editAuditAction = function(item) {
        $scope.showchecklist = 0;
        $scope.showchecklistLoading = 1;
        $scope.checklistName = item.ChecklistName;
        $scope.viewOnly = 0;
        hrmAPIservice.getActionChecklist(item.id).then(function(response) {
            renderChecklist(response);
        });        
    }
    
    $scope.viewAuditAction = function(item) {
        $scope.showchecklist = 0;
        $scope.showchecklistLoading = 1;
        $scope.checklistName = angular.copy({});
        $scope.checklistName = angular.copy(item.ChecklistName);
        $scope.viewOnly = 1;
        
        hrmAPIservice.getActionChecklist(item.id).then(function(response) {
            renderChecklist(response);
        });         
    }
    
    $scope.saveActionChecklist = function() {
        hrmAPIservice.saveAuditActionResult($scope.checklistdetail, $scope.action, userData).then(function(response) {
            $scope.cl = {}
            $scope.cl.checklists = response.data.checklists;  
            $scope.gridOptionsComplex.data = response.data.actionlists; 
            $scope.showchecklist = 0;
        });
    }
    
    $scope.setCheckbox = function(item,opt, doSave) {
        
        doSave = (angular.isDefined(doSave)) ? true : false;
        
        if (angular.isUndefined($scope.chk[item.id])) {
            $scope.chk[item.id] = {};
        }
        if (parseInt(opt) === 1) {
            $scope.chk[item.id].yes = 1;
            $scope.chk[item.id].no = 0;            
        } else {
            $scope.chk[item.id].yes = 0;
            $scope.chk[item.id].no = 1;            
        }
        
        if (doSave) {
            $scope.chk[item.id].is_ok = opt;
            $scope.saveItem(item, 'is_ok');
        }
    }
   
    $scope.saveItem = function(item, fld) {
        hrmAPIservice.saveActionItemResult(item, $scope.chk, fld, userData).then(function(response) {

        });        
    }
    
    $scope.archiveAuditAction = function(item, fld) {
        var answer = confirm('Archive "' + item.ChecklistName + '"? Once archived, you can find this record in the Audit Archive section of Autosafe. Continue?');
        if (!answer) { 
            return;
        }     
        hrmAPIservice.archiveAuditAction(item, userData).then(function(response) {
            $scope.gridOptionsComplex.data = response.data.actionlists; 
        });      
    }
    
    $scope.deleteAuditAction = function(item, fld) {
        var answer = confirm('Are you sure you want to delete audit "' + item.ChecklistName + '"?');
        if (!answer) {
            return;
        }
        
        hrmAPIservice.deleteAuditAction(item, userData).then(function(response) {
            $scope.gridOptionsComplex.data = response.data.actionlists; 
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
    
    $scope.toggleMenu = function() {
        $rootScope.hideNav = $scope.showMenu;
    }
        
    $scope.loadPage();
    

}]);