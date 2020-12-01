app.filter('trustHtml',function($sce){
  return function(html){
    return $sce.trustAsHtml(html)
  }
})
app.controller('injuryregisterController', ['$scope', '$rootScope', 'cookie','uiGridConstants', '$location', 'hrmAPIservice', 'fileService',  function ($scope, $rootScope, cookie, uiGridConstants, $location, hrmAPIservice, fileService) {

    var userData = cookie.checkLoggedIn();

    cookie.getPermissions();

    $scope.isAllowed = false;
    var perm = cookie.getCookie("permissions");
    console.log(perm);
    if (!perm || perm['38'] == null || perm['38']==undefined)      $scope.isAllowed = false;
    else {
        if (perm['38'].r == '1') $scope.isAllowed = true;
        else                     $scope.isAllowed = false;
    }
    if(!$scope.isAllowed){
        cookie.deleteCookie('user');
        cookie.deleteCookie('permissions');
        $rootScope.isLoggedin = 0;
        $location.path('/');
    }

    $scope.pageTitle = "Injury Register";

    $scope.formEnabled = 0;

    $scope.master = {};

    $scope.ir = {};



    $scope.ddList = [];

    $scope.hhList = [];

    $scope.mmList = [];

    $scope.fileLength = 0;                  //flag at present only one

    $scope.severity_list = [

        {id: 0, display_text: "!!!!Kill or cause permanent disablility or ill health"},

        {id: 1, display_text: "!!!Long term illness or serious injury"},

        {id: 2, display_text: "!!Medical attention and several days off work"},

        {id: 3, display_text: "!First aid needed"}

    ];

    $scope.liklihood_list = [

        {id: 0, display_text: "++Very likely Could happen any time"},

        {id: 1, display_text: "+Likely Could happen any time"},

        {id: 2, display_text: "-Unlikely Could happen, but very rarely"},

        {id: 3, display_text: "--Very unlikely Could happen, but probably never will"}

    ];

    var item = {};

    for(var i=0; i<=260; i++) {

        $scope.ddList.push(i);

    }

    for(var i=0; i<=23; i++) {

        var ival = (i < 10) ? '0'+i : i + '';

        $scope.hhList.push(ival);

    }

    

    for(var i=0; i<=59; i++) {

        var ival = (i < 10) ? '0'+i : i + '';

        $scope.mmList.push(ival);

    }    

    $scope.doSelectedEmployee = function() { 

        //alert($scope.ir.employee_id);

        //$scope.alloc_course.employee_id = $scope.cs.course_supervisor.value;

    }    

    $scope.empSearch = function(query) { 

        var list = [];

        if(query != null && query.length > 0) {

            for(var i=0; i<$scope.employeeList.length; i++) {

                if ($scope.employeeList[i].firstname.toLowerCase().indexOf(query.toLowerCase()) > -1 || $scope.employeeList[i].lastname.toLowerCase().indexOf(query.toLowerCase()) > -1) {

                    var emp = $scope.employeeList[i].firstname + " " + $scope.employeeList[i].lastname;

                    list.push({id: $scope.employeeList[i].id, name: emp});

                }

            }                    

        }

        return list;

    }  

    $scope.locationSearch = function (query) {

        var list = [];

        if(query != null && query.length > 0) {

            for(var i=0; i<$scope.siteList.length; i++) {

                if ($scope.siteList[i].display_text.toLowerCase().indexOf(query.toLowerCase()) > -1) {

                    list.push({id: $scope.siteList[i].id, name: $scope.siteList[i].display_text});

                }

            }                    

        }

        return list;

    }    

    $scope.doChangeEmployee = function(typedthings) {

        $scope.employee_list = [];

        for (var i=0; i<$scope.employeeList.length; i++) {

            if (typedthings == '' || $scope.employeeList[i].firstname.indexOf(typedthings) > -1 || $scope.employeeList[i].lastname.indexOf(typedthings) > -1) {

                $scope.employee_list.push($scope.employeeList[i].firstname + " " + $scope.employeeList[i].lastname);

            }

        }

    }



    $scope.doSelectedEmployee = function(suggestion) {

        for(let i=0; i<$scope.employeeList.length; i++) {

            var emp = $scope.employeeList[i].firstname + " " + $scope.employeeList[i].lastname;

            if(emp == suggestion) {

                $scope.ir.employee_id = $scope.employeeList[i].id;

                break;

            }

        }

    }    
     
    console.log($rootScope.perms);
    

    $scope.gridOptionsComplex = {

        enableFiltering: true,

        showGridFooter: false,

        showColumnFooter: false,

        paginationPageSizes: [10, 20, 30],

        paginationPageSize: 10,

        onRegisterApi: function onRegisterApi(registeredApi) {

            gridApi = registeredApi;

        },

        columnDefs: [

          { name: 'id', visible: false },
          { name: 'read', visible: false },
          { name: 'write', visible: false },
          { name: 'delete', visible: false },

          { name: 'injuredName', width: '20%',enableCellEdit: false },

          { name: 'dateOfIncident', width: '15%', cellClass: 'center',enableCellEdit: false },

          { name: 'siteLocation', width: '20%', enableFiltering: true, cellClass: 'center',enableCellEdit: false},

          { name: 'natureOfInjury', width: '20%', enableFiltering: true, cellClass: 'center',enableCellEdit: false},

          { name: 'status', width: '15%', enableFiltering: false, cellClass: 'center',enableCellEdit: false,
              cellClass: function(grid, row, col, rowRenderIndex, colRenderIndex) {

                  if (grid.getCellValue(row ,col).toLowerCase() === 'closed') {

                      return 'green center';

                  }else if(grid.getCellValue(row ,col).toLowerCase() === 'active'){
                      return 'blue center';

                  }else if(grid.getCellValue(row ,col).toLowerCase() === 'overdue'){
                      return 'red center';
                  }
                  else return 'center';

              }
          },

          { name: 'action', enableFiltering: false, width: '10%',  cellClass: 'center', enableCellEdit: false,

              cellTemplate: '<div class="ui-grid-cell-contents grid-center-cell" ><span ng-click="grid.appScope.viewInjury(row.entity)" ng-if="row.entity.read == 1"><span class="glyphicon glyphicon-eye-open text-edit"></span></span>&nbsp;&nbsp;&nbsp;<span ng-click="grid.appScope.editInjury(row.entity)" ng-if="row.entity.write == 1"><span class="glyphicon glyphicon-edit text-edit"></span></span>&nbsp;&nbsp;&nbsp;<span ng-click="grid.appScope.deleteInjury(row.entity)" ng-if="row.entity.delete == 1"><span class="glyphicon glyphicon-trash text-danger"></span></span></div>'

          }

        ]

    };

    $scope.deleteIR = function(irDetail) {

        var answer = confirm("Delete " + arDetail.name + '? Are you sure?');

        if (answer) {

            hrmAPIservice.delete(irDetail, userData, 'ir').then(function(response) {

                $scope.gridOptionsComplex.data = response.data;

            });

        }

    }

    $scope.viewInjury = function(obj) {
        hrmAPIservice.send('injuryregister/get/'+obj.id).then(function(response) {

            $scope.formEnabled = 0;

            $scope.ir = response.data;
            console.log(response.data);

            $scope.downloadable_file = response.data.upload_file_path == null  ? '' : response.data.upload_file_path;

            var itime = response.data.incident_time.split(':');           

            $scope.injury_hh = itime[0];

            $scope.injury_mm = itime[1];

            var ltime = response.data.lost_time.split(':');

            $scope.injury_tlhh = ltime[0] == null || ltime[0] == '00' ? '0' : ltime[0];

            $scope.injury_tlmm = ltime[1];
            
            $scope.injury_tlh = parseInt($scope.injury_tlhh)*24+parseInt($scope.injury_tlmm);
            if($scope.injury_tlh!='0' && $scope.injury_tlh!='1') $scope.injury_tlh+=" hours";
            else if($scope.injury_tlh=='1') $scope.injury_tlh+=" hour";

            var isActiveUser = 0;

            for(var i=0; i<$scope.employeeList.length; i++) {

                if($scope.employeeList[i].id == response.data.employee_id) {
                    
                    isActiveUser = 1;

                    $scope.employee = {id: $scope.employeeList[i].id, name: $scope.employeeList[i].firstname + " " + $scope.employeeList[i].lastname};

                    break;

                }

            }

            if(!isActiveUser) $scope.employee = null;
            
            var isActiveLocation = 0;
            
            for(var i=0; i<$scope.siteList.length; i++) {

                if($scope.siteList[i].id == response.data.site_location_id) {
                    
                    isActiveLocation = 1;

                    $scope.site_location = {id: $scope.siteList[i].id, name: $scope.siteList[i].display_text};

                    break;

                }

            }            

            if(!isActiveLocation) $scope.site_location = null;

            setDate('incident_date');

            if (response.data.insurernotified_date != '0000-00-00' && angular.isDefined(response.data.insurernotified_date)) {

                setDate('insurernotified_date');

            }else{
                $scope.ir['insurernotified_date'] = null;
            }

            if (response.data.safeworknotified_date != '0000-00-00' && angular.isDefined(response.data.safeworknotified_date)) {

                setDate('safeworknotified_date');

            }else{
                $scope.ir['safeworknotified_date'] = null;
            }

            if (response.data.workcover_date != '0000-00-00' && angular.isDefined(response.data.workcover_date)) {

                setDate('workcover_date');

            }else{
                $scope.ir['workcover_date'] = null;
            }

            if (response.data.closed_date != '0000-00-00' && angular.isDefined(response.data.closed_date)) {

                setDate('closed_date');

            }else{
                $scope.ir['closed_date'] = null;
            }



        });       

    }

    $scope.editInjury = function(obj) {

        // hrmAPIservice.getInjury(obj.id).then(function(response) {
        hrmAPIservice.send('injuryregister/get/'+obj.id).then(function(response) {

            $scope.formEnabled = 1;

            $scope.ir = response.data;
            console.log(response.data);

            $scope.downloadable_file = response.data.upload_file_path == null  ? '' : response.data.upload_file_path;

            var itime = response.data.incident_time.split(':');           

            $scope.injury_hh = itime[0];

            $scope.injury_mm = itime[1];

            var ltime = response.data.lost_time.split(':');

            $scope.injury_tlhh = ltime[0] == null || ltime[0] == '00' ? '0' : ltime[0];

            $scope.injury_tlmm = ltime[1];

            
            $scope.injury_tlh = parseInt($scope.injury_tlhh)*24+parseInt($scope.injury_tlmm);
            if($scope.injury_tlh!=0 || $scope.injury_tlh!=1) $scope.injury_tlh+=" hours";
            else if($scope.injury_tlh=='1') $scope.injury_tlh+=" hour";

            var isActiveUser = 0;
            for(var i=0; i<$scope.employeeList.length; i++) {

                if($scope.employeeList[i].id == response.data.employee_id) {

                    isActiveUser = 1;
                    $scope.employee = {id: $scope.employeeList[i].id, name: $scope.employeeList[i].firstname + " " + $scope.employeeList[i].lastname};

                    break;

                }

            }
            
            if(!isActiveUser){
                $scope.employee = null;
            }

            var isActiveLocation = 0;

            for(var i=0; i<$scope.siteList.length; i++) {

                if($scope.siteList[i].id == response.data.site_location_id) {
                    
                    isActiveLocation = 1;

                    $scope.site_location = {id: $scope.siteList[i].id, name: $scope.siteList[i].display_text};

                    break;

                }

            }            

            if(!isActiveLocation) $scope.site_location = null;

            setDate('incident_date');

            if (response.data.insurernotified_date != '0000-00-00' && angular.isDefined(response.data.insurernotified_date)) {

                setDate('insurernotified_date');

            }else{
                $scope.ir['insurernotified_date'] = null;
            }

            if (response.data.safeworknotified_date != '0000-00-00' && angular.isDefined(response.data.safeworknotified_date)) {

                setDate('safeworknotified_date');

            }else{
                $scope.ir['safeworknotified_date'] = null;
            }

            if (response.data.workcover_date != '0000-00-00' && angular.isDefined(response.data.workcover_date)) {

                setDate('workcover_date');

            }else{
                $scope.ir['workcover_date'] = null;
            }

            if (response.data.closed_date != '0000-00-00' && angular.isDefined(response.data.closed_date)) {

                setDate('closed_date');

            }else{
                $scope.ir['closed_date'] = null;
            }



        });       

    }

    $scope.deleteInjury = function(obj) {
        console.log(obj);
        hrmAPIservice.deleteIRData(userData).then(function(response) {
            hrmAPIservice.getIRData(userData).then(function(response) {

                for(var i = 0; i < response.data.injuries.length; i++){
                    response.data.injuries[i].delete = Number($rootScope.perms.injuryregister.delete);
                    response.data.injuries[i].write = Number($rootScope.perms.injuryregister.write);
                    response.data.injuries[i].read = Number($rootScope.perms.injuryregister.read);
                }
                $scope.gridOptionsComplex.data = response.data.injuries;
        
                $scope.employeeList = response.data.employees;
        
                $scope.locationList = response.data.locations;     
        
                $scope.siteList = response.data.sites; 
        
                $scope.natureList = response.data.nature;
        
                $scope.bodypartList = response.data.bodypart;
        
                $scope.mechanismList = response.data.mechanism;
        
                $scope.riskconsequencesList = response.data.rq;
        
                $scope.risklikelihoodList = response.data.rl;
        
                $scope.reminderfrequencyList = response.data.rf;
        
                $scope.remedialpriorityList = response.data.rp;
        
                hrmAPIservice.getInfoTextByType(userData, "injury_register").then(function(response){
                    $scope.info_risk_identification = response.data.result.risk_identification;
                    $scope.info_risk_assessment = response.data.result.risk_assessment;
                    $scope.info_risk_controls = response.data.result.risk_controls;
                    console.log($scope.info_risk_identification);
                    console.log($scope.info_risk_assessment);
                    console.log($scope.info_risk_controls);
                });
        
            });
        });
    }
    $scope.newInjury = function() {

        $scope.ir = {};

        $scope.showMessage = 0;

        $scope.clearForm();

        $scope.ir.incident_date = new Date();

        // $scope.ir.insurernotified_date = new Date();
        //
        // $scope.ir.safeworknotified_date = new Date();
        //
        // $scope.ir.workcover_date = new Date();
        //
        // $scope.ir.closed_date = new Date();

        $scope.formEnabled = 1;

    }

    $scope.clearForm = function() {

        $scope.ir = angular.copy($scope.master);

        

        $scope.employee = null;

        $scope.site_location = null;

        $scope.employee_id = '';

        $scope.ir.employee_id = '';

        $scope.ir.natureofinjury_id = '';

        $scope.ir.mechanismofinjury_id = '';

        $scope.ir.location_id = '';

        $scope.ir.injuredbodypart_id = '';

        $scope.ir.insurer_notified = '0';

        $scope.ir.safework_notified = '0';

        $scope.injury_hh = '00';

        $scope.injury_mm = '00';

        $scope.injury_tlhh = '00';

        $scope.injury_tlmm = '00';

        $scope.injury_tlh = '0';

        $scope.ir.id = 0;

        $scope.ir.account_id = userData.account_id;

        $scope.ir.created_by = userData.id;

        $scope.ir.updated_by = 0;    

        $scope.formEnabled = 0;

    }

    const setDate = function(fld) {

        var date = $scope.ir[fld];

        if (date == null || !date) {

            return;

        }

        if ($scope.ir[fld]) {

            if (date == '0000-00-00') {

                $scope.ir[fld] = new Date(); 

                return;

            }

            var d = new Date(date);

            $scope.ir[fld] = d;

        }

    }

    hrmAPIservice.getIRData(userData).then(function(response) {

        for(var i = 0; i < response.data.injuries.length; i++){
            response.data.injuries[i].delete = Number($rootScope.perms.injuryregister.delete);
            response.data.injuries[i].write = Number($rootScope.perms.injuryregister.write);
            response.data.injuries[i].read = Number($rootScope.perms.injuryregister.read);
        }
        $scope.gridOptionsComplex.data = response.data.injuries;

        $scope.employeeList = response.data.employees;

        $scope.locationList = response.data.locations;     

        $scope.siteList = response.data.sites; 

        $scope.natureList = response.data.nature;

        $scope.bodypartList = response.data.bodypart;

        $scope.mechanismList = response.data.mechanism;

        $scope.riskconsequencesList = response.data.rq;

        $scope.risklikelihoodList = response.data.rl;

        $scope.reminderfrequencyList = response.data.rf;

        $scope.remedialpriorityList = response.data.rp;

        hrmAPIservice.getInfoTextByType(userData, "injury_register").then(function(response){
            $scope.info_risk_identification = response.data.result.risk_identification;
            $scope.info_risk_assessment = response.data.result.risk_assessment;
            $scope.info_risk_controls = response.data.result.risk_controls;
        });

    });

    $scope.saveIR = function() {

        $scope.showMessage = 0;

        $scope.ir.employee_id = $scope.employee.id;

        $scope.ir.site_location_id = $scope.site_location.id;



        $scope.ir.incident_time = $scope.injury_hh + ":" + $scope.injury_mm;
        $scope.ir.lost_time = $scope.injury_tlhh + ":" + $scope.injury_tlmm;
        if($scope.fileLength < fileService.length){
            console.log("less than length of fileService");
            var file = fileService[fileService.length - 1];

            var uploadUrl = "assets/php/upload.php";
            
            if($scope.ir.id > 0){
                hrmAPIservice.saveIR($scope.ir, userData).then(function(response) {
                    for(var i = 0; i < response.data.injuries.length; i++){
                        response.data.injuries[i].delete = Number($rootScope.perms.injuryregister.delete);
                        response.data.injuries[i].write = Number($rootScope.perms.injuryregister.write);
                        response.data.injuries[i].read = Number($rootScope.perms.injuryregister.read);
                    }
                    $scope.gridOptionsComplex.data = response.data.injuries;

                    $scope.success = 1;

                    $scope.showMessage = 1;

                    $scope.userMessage = "Injury details have been updated successfully!";

                    $scope.clearForm();

                });
            }
            else{
                hrmAPIservice.uploadFileToUrl(file, uploadUrl).then(function(response) {
                    alert(response.data.msg);
    
                    if(!response.data.status){
                        return;
    
                    } else {
                        $scope.ir.upload_file_path = "assets/php/uploads/" + file.name;
                        hrmAPIservice.saveIR($scope.ir, userData).then(function(response) {
                            for(var i = 0; i < response.data.injuries.length; i++){
                                response.data.injuries[i].delete = Number($rootScope.perms.injuryregister.delete);
                                response.data.injuries[i].write = Number($rootScope.perms.injuryregister.write);
                                response.data.injuries[i].read = Number($rootScope.perms.injuryregister.read);
                            }
                            $scope.gridOptionsComplex.data = response.data.injuries;
    
                            $scope.success = 1;
    
                            $scope.showMessage = 1;
    
                            $scope.userMessage = "Injury details have been saved successfully!";
    
                            $scope.clearForm();
    
                        });
    
                    }
                });
            }
        }
        else{
            
            console.log("more than length of fileService");
            $scope.ir.upload_file_path = "";
            hrmAPIservice.saveIR($scope.ir, userData).then(function(response) {
                for(var i = 0; i < response.data.injuries.length; i++){
                    response.data.injuries[i].delete = Number($rootScope.perms.injuryregister.delete);
                    response.data.injuries[i].write = Number($rootScope.perms.injuryregister.write);
                    response.data.injuries[i].read = Number($rootScope.perms.injuryregister.read);
                }
                $scope.gridOptionsComplex.data = response.data.injuries;



                $scope.success = 1;

                $scope.showMessage = 1;

                $scope.userMessage = "Injury details have been saved successfully!";

                $scope.clearForm();

            });
        }


    }

    $scope.calcFreq = function(){

        if(!($scope.ir.level_of_risk == '' || $scope.ir.risk_likelihood == '')){

            switch($scope.ir.level_of_risk / 1 + $scope.ir.risk_likelihood / 1){

                case 0: $scope.ir.email_frequency = "Email every 1 day"; break;

                case 1: $scope.ir.email_frequency = "Email every 1 day"; break;

                case 2: $scope.ir.email_frequency = "Email every 3 days"; break;

                case 3: $scope.ir.email_frequency = "Email every 7 days"; break;

                case 4: $scope.ir.email_frequency = "Email every 14 days"; break;

                case 5: $scope.ir.email_frequency = "Email every 21 days"; break;

                case 6: $scope.ir.email_frequency = "Email every 28 days"; break; 

                default: $scope.ir.email_frequency = "No email";

            }

        }else{

            $scope.ir.email_frequency = "No email";

        }

    }





}]);

