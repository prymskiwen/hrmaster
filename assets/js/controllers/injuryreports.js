app.controller('injuryreportsController', ['$scope', '$rootScope', 'cookie', '$location', 'hrmAPIservice', function ($scope, $rootScope, cookie, $location, hrmAPIservice) {
    var userData = cookie.checkLoggedIn();
    cookie.getPermissions();
    
    
    $scope.gridApiDprt = null;
    $scope.gridApiLtn = null;
    $scope.gridApiEply = null;
    $scope.gridApiPosition = null;
    
    $scope.param ={
        filter_fromdate: "",
        filter_todate: "",
        dep: "",
        pos: "",
        loc: ""
    };
    $scope.employees = [];
    $scope.departments = [];
    $scope.locations = [];
    $scope.positions = [];
    $scope.mechanisms = [];
    $scope.natures = [];
    
    $scope.gridOptionsComplexDprt = {
        enableFiltering: true,
        showGridFooter: false,
        showColumnFooter: false,
        onRegisterApi: function(registeredApi) {
            $scope.gridApiDprt = registeredApi;
        },
        columnDefs: [
            {
                field: 'department',
                width: '100%',
                cellClass: 'center',
                cellTemplate: '<a ng-click="grid.appScope.updateDepartment(row.entity.id, $event)" class="ListItems">{{row.entity.department}}</a>'
            }
        ]
    };
    $scope.gridOptionsComplexLtn = {
        enableFiltering: true,
        showGridFooter: false,
        showColumnFooter: false,
        onRegisterApi: function(registeredApi) {
            $scope.gridApiLtn = registeredApi;
        },
        columnDefs: [
            {
                field: 'location',
                width: '100%',
                cellClass: 'center',
                cellTemplate: '<a ng-click="grid.appScope.updateLocation(row.entity.id, $event)" class="ListItems">{{row.entity.location}}</a>'
            }
        ]
    };
    $scope.gridOptionsComplexEply = {
        enableFiltering: true,
        showGridFooter: false,
        showColumnFooter: false,
        onRegisterApi: function(registeredApi) {
            $scope.gridApiEply = registeredApi;
        },
        columnDefs: [
            {
                field: 'employee',
                width: '100%',
                cellClass: 'center',
                cellTemplate: '<a ng-click="grid.appScope.updateEmployee(row.entity.id, $event)" class="ListItems">{{row.entity.employee}}</a>'
            },  
        ]
    };
    $scope.gridOptionsComplexPosition = {
        enableFiltering: true,
        showGridFooter: false,
        showColumnFooter: false,
        onRegisterApi: function(registeredApi) {
            $scope.gridApiPosition = registeredApi;
        },
        columnDefs: [
            {
                field: 'position',
                width: '100%',
                cellClass: 'center',
                cellTemplate: '<a ng-click="grid.appScope.updatePosition(row.entity.id, $event)" class="ListItems">{{row.entity.position}}</a>'
            },  
        ]
    };
    $scope.gridOptionsComplexInjNature = {
        enableFiltering: true,
        showGridFooter: false,
        showColumnFooter: false,
        onRegisterApi: function onRegisterApi(registeredApi) {
            gridApi = registeredApi;
        },
        columnDefs: [
            {
                field: 'nature',
                displayName: 'Nature of Injury',
                width: '100%',
                cellClass: 'center',
                cellTemplate: '<a ng-click="grid.appScope.updateInjuryNature(row.entity.id, $event)" class="ListItems">{{row.entity.nature}}</a>'
            },      
        ]
    };
    $scope.gridOptionsComplexInjMech = {
        enableFiltering: true,
        showGridFooter: false,
        showColumnFooter: false,
        onRegisterApi: function onRegisterApi(registeredApi) {
            gridApi = registeredApi;
        },
        columnDefs: [
            {
                field: 'mechanism',
                displayName: 'Mechanism of Injury',
                width: '100%',
                cellClass: 'center',
                cellTemplate: '<a ng-click="grid.appScope.updateInjuryMechanism(row.entity.id, $event)" class="ListItems">{{row.entity.mechanism}}</a>'
            },  
        ]
    };
    $scope.gridOptionsComplexFreeEply = {
        enableFiltering: true,
        showGridFooter: true,
        showColumnFooter: false,
        paginationPageSizes: [10, 20, 30],
        paginationPageSize: 10,
        onRegisterApi: function onRegisterApi(registeredApi) {
            gridApi = registeredApi;
        },
        columnDefs: [
          { name: 'employeename', width: '20%', displayName: 'Employee Name'},
          { name: 'position', cellClass: 'center', displayName: 'Position'},
          { name: 'department', cellClass: 'center', displayName: 'Department'},
          { name: 'location', cellClass: 'center', displayName: 'Site Location'}
        ]
    };
    
    $scope.isEmployeeListExist = true;
    $scope.isDepartmentListExist = true;
    $scope.isLocationListExist = true;
    $scope.isPositionListExist = true;
    $scope.isInjuryNatureListExist = true;
    $scope.isInjuryMechanismListExist = true;
    
    $scope.selected_department = "";
    $scope.selected_location = "";
    $scope.selected_employee = "";
    $scope.selected_position = "";
    $scope.selected_mechanism = "";
    $scope.selected_nature = "";
    
    $scope.filter = {
        date_from: "",
        date_to : ""
    };
    $scope.getChartJSONDataForChart1 = [];
    $scope.getChartJSONDataForChart2 = [];
    $scope.getChartJSONDataForChart3 = [];
        
    $scope.showMode1 = 0;
    $scope.showMode2 = 0;
    $scope.showMode3 = 0;
    
    $scope.graph3Data =[];
    
    $scope.isAllowed = false;
    var perm = cookie.getCookie("permissions");
    
    console.log(perm);
    if (!perm || perm['49'] == null || perm['49']==undefined)      $scope.isAllowed = false;
    else {
        if (perm['49'].r == '1') $scope.isAllowed = true;
        else                     $scope.isAllowed = false;
    }
    if(!$scope.isAllowed){
        cookie.deleteCookie('user');
        cookie.deleteCookie('permissions');
        $rootScope.isLoggedin = 0;
        $location.path('/');
    }
    $scope.init = function () {
        $scope.userId = cookie.getCookie('user').account_id;
        var perm = cookie.getCookie("permissions");
        if (perm['49'] == null || perm['49']==undefined)      $scope.isAllowed = false;
        else {
            if (perm['49'].r == '1') $scope.isAllowed = true;
            else                     $scope.isAllowed = false;
        }
        if(!$scope.isAllowed){
            cookie.deleteCookie('user');
            cookie.deleteCookie('permissions');
            $rootScope.isLoggedin = 0;
            $location.path('/');
        }
        
        var now = new Date();
        now.setMonth(now.getMonth() - 12);
        $scope.filter.date_from = now;
        $scope.filter.date_to = new Date();
        
        hrmAPIservice
            .send('department_list/' + $scope.userId)
            .then(function (response) {
                if (response.data.res == null) return;
                $scope.departments = response.data.res;
                var tmp = angular.copy($scope.departments);
                tmp.unshift({id: 0, department: "All Departments"});
                $scope.gridOptionsComplexDprt.data = tmp;
                setTimeout(function(){
                    $(".grid-departments .ui-grid-row:first-child .ListItems").addClass("selected");
                },500);
                
                $scope.department_length = ($scope.gridOptionsComplexDprt.data.length * 31) + 71;
                if ($scope.department_length >= 377) 
                    $scope.department_length = 'noNeed';
                    
            });

        hrmAPIservice
            .send('location_list/' + $scope.userId)
            .then(function (response) {
                if (response.data.res == null) return;
                $scope.locations = response.data.res;
                var tmp = angular.copy($scope.locations);
                tmp.unshift({id: 0, location: "All Locations"});
                $scope.gridOptionsComplexLtn.data = tmp;
                setTimeout(function(){
                    $(".grid-locations .ui-grid-row:first-child .ListItems").addClass("selected");
                },500);
                $scope.location_length = ($scope.gridOptionsComplexLtn.data.length * 31) + 71;
                if ($scope.location_length >= 377)
                    $scope.location_length = 'noNeed';
                $scope.updateReport();
            });

        hrmAPIservice
            .send('injured_employee_list/' + $scope.userId)
            .then(function (response) {
                if (response.data.res == null) return;
                $scope.employees = response.data.res;
                var tmp = angular.copy($scope.employees);
                tmp.unshift({id: 0, employee: "All Employees"});
                $scope.gridOptionsComplexEply.data = tmp;
                setTimeout(function(){
                    $(".grid-employees .ui-grid-row:first-child .ListItems").addClass("selected");
                },500);
                $scope.employees_length = ($scope.gridOptionsComplexEply.data.length * 31) + 71;
                if ($scope.employees_length >= 377)
                    $scope.employees_length = 'noNeed';
            });
        hrmAPIservice
            .send('position_list/' + $scope.userId)
            .then(function (response) {
                if (response.data.res == null) return;
                $scope.positions = response.data.res;
                var tmp = angular.copy($scope.positions);
                tmp.unshift({id: 0, position: "All Positions"});
                $scope.gridOptionsComplexPosition.data = tmp;
                setTimeout(function(){
                    $(".grid-positions .ui-grid-row:first-child .ListItems").addClass("selected");
                },500);
                $scope.positions_length = ($scope.gridOptionsComplexPosition.data.length * 31) + 71;
                if ($scope.positions_length >= 377)
                    $scope.positions_length = 'noNeed';
            });
        hrmAPIservice
            .send('mechanism_list/' + $scope.userId)
            .then(function (response) {
                if (response.data.res == null) return;
                $scope.mechanisms = response.data.res;
                console.log(response.data.res);
                var tmp = angular.copy($scope.mechanisms);
                tmp.unshift({id: 0, mechanism: "All Mechanisms"});
                $scope.gridOptionsComplexInjMech.data = tmp;
                setTimeout(function(){
                    $(".grid-mechanism .ui-grid-row:first-child .ListItems").addClass("selected");
                },500);
                $scope.mechanism_length = ($scope.gridOptionsComplexInjMech.data.length * 31) + 71;
                if ($scope.mechanism_length >= 377)
                    $scope.mechanism_length = 'noNeed';
            });
        hrmAPIservice
            .send('nature_list/' + $scope.userId)
            .then(function (response) {
                if (response.data.res == null) return;
                $scope.natures = response.data.res;
                console.log(response.data.res);
                var tmp = angular.copy($scope.natures);
                tmp.unshift({id: 0, nature: "All Natures"});
                $scope.gridOptionsComplexInjNature.data = tmp;
                setTimeout(function(){
                    $(".grid-nature .ui-grid-row:first-child .ListItems").addClass("selected");
                },500);
                $scope.nature_length = ($scope.gridOptionsComplexInjNature.data.length * 31) + 71;
                if ($scope.nature_length >= 377)
                    $scope.nature_length = 'noNeed';
            });
        
    }
    $scope.updateReport = function() {
        $scope.param['filter_fromdate'] = $scope.filter.date_from;
        $scope.param['filter_todate'] = $scope.filter.date_to;
        $scope.param['dep'] = $scope.selected_department;
        $scope.param['pos'] = $scope.selected_position;
        $scope.param['loc'] = $scope.selected_location;
        $scope.param['emp'] = $scope.selected_employee;
        $scope.param['mechanism'] = $scope.selected_mechanism;
        $scope.param['nature'] = $scope.selected_nature;
        
        buildBarReportByDepartment();
        buildPieReportByDepartment();
        buildBarReportByLocation();
        buildPieReportByLocation();
        buildBarLostReportByLocation();
        updateHealthyEmployeesGrid();
    }
    
    $scope.updateDepartment = function(dept, event) {
        console.log(dept, event);
        if($(".grid-departments .ListItems.selected").length>0)
            $(".grid-departments .ListItems.selected").removeClass("selected");
        $(event.target).addClass('selected');
        if(dept==0) $scope.selected_department = "";
        else $scope.selected_department = dept;
        $scope.updateReport();
    }
    $scope.updateLocation = function(loc, event) {
        if($(".grid-locations .ListItems.selected").length>0)
            $(".grid-locations .ListItems.selected").removeClass("selected");
        $(event.target).addClass('selected');
        if(loc==0) $scope.selected_location = "";
        else $scope.selected_location = loc;
        $scope.updateReport();
    }
    $scope.updateEmployee = function(emp, event) {
        if($(".grid-employees .ListItems.selected").length>0)
            $(".grid-employees .ListItems.selected").removeClass("selected");
        $(event.target).addClass('selected');
        if(emp==0) $scope.selected_employee = "";
        else $scope.selected_employee = emp;
        $scope.updateReport();
        
        var filter_params = JSON.stringify($scope.param);
        
        hrmAPIservice.printInjuryHistory(emp, filter_params).then(function(response) {
            //$scope.injuryHistoryUrl = response.data.url;
            console.log(response.data.url);
            // var urls = response.data.url;
            // for(var i=0; i<urls.length; i++){
            //     var link = document.createElement('a');
            //     link.href = urls[i];
            //     link.class = "link-injuryhistory"
            //     link.target = "_blank";
            //     link.style = "display: none";
            //     document.getElementsByClassName('grid-employees').appendChild(link);
            //     link.click();
            // }
        });
    } 
    $scope.updatePosition = function(pos, event) {
        if($(".grid-positions .ListItems.selected").length>0)
            $(".grid-positions .ListItems.selected").removeClass("selected");
        $(event.target).addClass('selected');
        if(pos==0) $scope.selected_position = "";
        else $scope.selected_position = pos;
        $scope.updateReport();
    }        
    $scope.updateInjuryMechanism = function(mechanism, event) {
        if($(".grid-mechanism .ListItems.selected").length>0)
            $(".grid-mechanism .ListItems.selected").removeClass("selected");
        $(event.target).addClass('selected');
        if(mechanism==0) $scope.selected_mechanism = "";
        else $scope.selected_mechanism = mechanism;
        $scope.updateReport();
    }
    $scope.updateInjuryNature = function(nature, event) {
        if($(".grid-nature .ListItems.selected").length>0)
            $(".grid-nature .ListItems.selected").removeClass("selected");
        $(event.target).addClass('selected');
        if(nature==0) $scope.selected_nature = "";
        else $scope.selected_nature = nature;
        $scope.updateReport();
    }
    
    function updateHealthyEmployeesGrid(){
        var param = JSON.stringify($scope.param);
        hrmAPIservice
            .send('noninjury_employee_list/'+$scope.userId+"/"+param)
            .then(function(response){
                if(response.data.res==null) return;
                $scope.free_employees = response.data.res;
                $scope.gridOptionsComplexFreeEply.data = $scope.free_employees;
                
                $scope.getCSVHeaderForGrid = ['User ID', 'Employee Name', 'Location', 'Department', 'Position'];
                $scope.getJSONDataForGrid = $scope.free_employees;
            });
    }
    
    function ReplaceNumberWithCommas(yourNumber) {
        //Seperates the components of the number
        var n= yourNumber.toString().split(".");
        //Comma-fies the first part
        n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        //Combines the two sections
        return n.join(".");
    }
    
    function buildBarReportByDepartment(){ 
        $scope.departmentBarLabels = [];
        $scope.departmentBarData = [];
        $scope.departmentBarColors = [];
        $scope.departmentBarOptions = {
            layout: {
                padding: {
                    top: 30,
                    left: 30,
                    right: 30,
                    bottom: 30
                }
            },
            barShowStroke : false,
            plugins: {
                datalabels: {
                    display: function(context) {
                        return context.dataset.data[context.dataIndex] !== 0; // or >= 1 or ...
                    },
                    align: 'center',
                    anchor: 'center',
                    color: '#000'
                }
            },
            scales: {
                xAxes: [{
                    stacked: true,
                    ticks: {
                        autoSkip: false
                    }
                }],
                yAxes: [{
                    stacked: true,
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        };
        $scope.departmentBarColors = [];
        var graphLabels = [];
        var graphData = [];
        
        var graphData_value = [];
        
        var filter_params = JSON.stringify($scope.param);
        hrmAPIservice.getInjuryReportByDepartment(userData, $scope.showMode1, filter_params).then(function(response){ //overdue
            var count_data = response.data.department_report;
            var index=0;
            $.each(count_data, function(key, value){
                if(value==null) value=0;
                graphData[key] = value;
                if(graphData[key]!=0){
                    graphData_value[index]=graphData[key];
                    graphLabels[index]=key;
                    index++;
                }
            })
            $scope.departmentBarLabels = graphLabels;
            $scope.departmentBarData = graphData_value;
            $scope.getCSVHeaderForChart1 = ['Department', 'Number of Injuries'];
            for(var i=0; i<$scope.departmentBarLabels.length; i++){
                $scope.getChartJSONDataForChart1[i] = {
                    'department': $scope.departmentBarLabels[i],
                    'number': graphData_value[i]
                } 
            }
        });
    }
    function buildPieReportByDepartment(){ 
        $scope.departmentPieLabels = [];
        $scope.departmentPieData = [];
        $scope.departmentPieColors = [];
        $scope.departmentPieOptions = {
            layout: {
                padding: {
                    top: 30,
                    left: 30,
                    right: 30,
                    bottom: 30
                }
            },
            plugins: {
                datalabels: {
                    display: function(context) {
                        return context.dataset.data[context.dataIndex] !== 0; // or >= 1 or ...
                    },
                    formatter: function(value, ctx){
                        let sum = 0;
                        let dataArr = ctx.chart.data.datasets[0].data;
                        dataArr.map(function(data){
                            sum += parseInt(data);
                        });
                        let percentage = (value*100 / sum).toFixed(2)+"%";
                        return percentage;
                    },
                    align: 'center',
                    anchor: 'center',
                    color: '#000'
                }
            },
            legend: {
                display: true,
                labels: {
                    fontColor: 'rgb(0, 0, 0)'
                },
                position: "bottom"
            }
        };
        $scope.departmentPieColors = [];
        var graphLabels = [];
        var graphData = [];
        
        var graphData_value = [];
        
        var filter_params = JSON.stringify($scope.param);
        hrmAPIservice.getInjuryReportByDepartment(userData, $scope.showMode1, filter_params).then(function(response){ //overdue
            var count_data = response.data.department_report;
            var index=0;
            $.each(count_data, function(key, value){
                if(value==null) value=0;
                graphData[key] = value;
                if(graphData[key]!=0){
                    graphData_value[index]=graphData[key];
                    graphLabels[index]=key;
                    index++;
                }
            })
            $scope.departmentPieLabels = graphLabels;
            $scope.departmentPieData = graphData_value;
            $scope.getCSVHeaderForChart1 = ['Department', 'Number of Injuries'];
            for(var i=0; i<$scope.departmentBarLabels.length; i++){
                $scope.getChartJSONDataForChart1[i] = {
                    'department': $scope.departmentBarLabels[i],
                    'number': graphData_value[i]
                } 
            }
        });
    }
    function buildBarReportByLocation(){ 
        $scope.locationBarLabels = [];
        $scope.locationBarData = [];
        $scope.locationBarColors = [];
        $scope.locationBarOptions = {
            layout: {
                padding: {
                    top: 30,
                    left: 30,
                    right: 30,
                    bottom: 30
                }
            },
            barShowStroke : false,
            plugins: {
                datalabels: {
                    display: function(context) {
                        return context.dataset.data[context.dataIndex] !== 0; // or >= 1 or ...
                    },
                    align: 'center',
                    anchor: 'center',
                    color: '#000'
                }
            },
            scales: {
                xAxes: [{
                    stacked: true,
                    ticks: {
                        autoSkip: false
                    }
                }],
                yAxes: [{
                    stacked: true,
                    ticks: {
                        beginAtZero: true
                    }
                }]
            }
        };
        $scope.locationBarColors = [];
        var graphLabels = [];
        var graphData = [];
        
        var graphData_value = [];
        
        var filter_params = JSON.stringify($scope.param);
        hrmAPIservice.getInjuryReportByLocation(userData, $scope.showMode1, filter_params).then(function(response){ //overdue
            var count_data = response.data.location_report;
            var index=0;
            $.each(count_data, function(key, value){
                if(value==null) value=0;
                graphData[key] = value;
                if(graphData[key]!=0){
                    graphData_value[index]=graphData[key];
                    graphLabels[index]=key;
                    index++;
                }
            })
            $scope.locationBarLabels = graphLabels;
            $scope.locationBarData = graphData_value;
            $scope.getCSVHeaderForChart2 = ['Location', 'Number of Injuries'];
            for(var i=0; i<$scope.locationBarLabels.length; i++){
                $scope.getChartJSONDataForChart2[i] = {
                    'location': $scope.locationBarLabels[i],
                    'number': graphData_value[i]
                } 
            }
        });
    }
    function buildPieReportByLocation(){ 
        $scope.locationPieLabels = [];
        $scope.locationPieData = [];
        $scope.locationPieColors = [];
        $scope.locationPieOptions = {
            layout: {
                padding: {
                    top: 30,
                    left: 30,
                    right: 30,
                    bottom: 30
                }
            },
            plugins: {
                datalabels: false
            },
            legend: {
                display: true,
                labels: {
                    fontColor: 'rgb(0, 0, 0)'
                },
                position: "bottom"
            }
        };
        $scope.locationPieColors = [];
        var graphLabels = [];
        var graphData = [];
        
        var graphData_value = [];
        
        var filter_params = JSON.stringify($scope.param);
        hrmAPIservice.getInjuryReportByLocation(userData, $scope.showMode1, filter_params).then(function(response){ //overdue
            var count_data = response.data.location_report;
            var index=0;
            $.each(count_data, function(key, value){
                if(value==null) value=0;
                graphData[key] = value;
                if(graphData[key]!=0){
                    graphData_value[index]=graphData[key];
                    graphLabels[index]=key;
                    index++;
                }
            })
            $scope.locationPieLabels = graphLabels;
            $scope.locationPieData = graphData_value;
            $scope.getCSVHeaderForChart2 = ['Location', 'Number of Injuries'];
            for(var i=0; i<$scope.locationBarLabels.length; i++){
                $scope.getChartJSONDataForChart2[i] = {
                    'location': $scope.locationBarLabels[i],
                    'number': graphData_value[i]
                } 
            }
        });
    }
    function buildBarLostReportByLocation(){
        $scope.lostBarLabels = [];
        $scope.lostBarData = [];
        $scope.lostBarColors = ["rgba(255,99,132,0.2)"];
        $scope.lostBarOptions = {
            layout: {
                padding: {
                    top: 30,
                    left: 30,
                    right: 30,
                    bottom: 30
                }
            },
            barShowStroke : true,
            plugins: {
                datalabels: {
                    display: function(context) {
                        return context.dataset.data[context.dataIndex] !== 0; // or >= 1 or ...
                    },
                    align: 'center',
                    anchor: 'center',
                    color: '#000',
                    formatter: (value, ctx) =>{
                        const label = value + "h";
                        return label;
                    }
                }
            },
            scales: {
                xAxes: [{
                    stacked: true,
                    ticks: {
                        autoSkip: false
                    }
                }],
                yAxes: [{
                    stacked: true,
                    ticks: {
                        beginAtZero: true
                    }
                }]
            },
            tooltips: {
                //mode: "label",
                callbacks: {
                    label: function(tooltipItem, data) {
                        var legend = new Array();
                        for(var i in data.datasets){
                            var days = parseInt(parseInt($scope.graph3Data[$scope.lostBarLabels[tooltipItem.index]])/24);
                            if(days==0) days="";
                            else if(days==1) days+=" day";
                            else if(days>1) days+=" days";
                            var hours = parseInt($scope.graph3Data[$scope.lostBarLabels[tooltipItem.index]])%24;
                            if(hours==0) hours="";
                            else if(hours==1) hours+=" hour";
                            else if(hours>1) hours+=" hours";
                            legend.push(
                               "Lost Days: " + days+ " "+ hours
                            );
                            legend.push(
                               "Lost Hours: " + $scope.graph3Data[$scope.lostBarLabels[tooltipItem.index]]+" hours"
                            );
                        }
            
                        return legend;
                    }
                }
            },
        };
        var graphLabels = [];
        var graphData = [];
        
        var graphData_value = [];
        
        var filter_params = JSON.stringify($scope.param);
        hrmAPIservice.getInjuryLostReportByLocation(userData, $scope.showMode3, filter_params).then(function(response){ //overdue
            $scope.graph3Data = response.data.location_report;
            var index=0;
            $.each($scope.graph3Data, function(key, value){
                if(value==null) value=0;
                graphData[key] = value;
                if(graphData[key]!=0){
                    graphData_value[index]=graphData[key];
                    graphLabels[index]=key;
                    index++;
                }
            })
            $scope.lostBarLabels = graphLabels;
            $scope.lostBarData = graphData_value;
            $scope.getCSVHeaderForChart3 = ['Location', 'Lost Hours', 'Lost Days'];
            for(var i=0; i<$scope.lostBarLabels.length; i++){
                $scope.getChartJSONDataForChart3[i] = {
                    'location': $scope.lostBarLabels[i],
                    'lost_hours': graphData_value[i],
                    'lost_days': (parseInt(graphData_value[i])/24).toFixed(2)
                } 
            }
        });
    }
    $scope.changeShowModeForGraph1 = function(){
        $scope.showMode1 = 1 - $scope.showMode1;
        buildBarReportByDepartment();
        buildPieReportByDepartment();
    }
    $scope.changeShowModeForGraph2 = function(){
        $scope.showMode2 = 1 - $scope.showMode2;
        buildBarReportByLocation();
        buildPieReportByLocation();
    }
    $scope.changeShowModeForGraph3 = function(){
        $scope.showMode3 = 1 - $scope.showMode3;
        buildBarLostReportByLocation();
    }
}]);
