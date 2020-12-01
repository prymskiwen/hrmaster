app.controller('realtrainingreportsController', [
    '$scope',
    'hrmAPIservice',
    '$rootScope',
    'cookie',
    '$location',
    'uiGridConstants',
    function ($scope, hrmAPIservice, $rootScope, cookie, $location, uiGridConstants) {
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
        
        $scope.isAllowed = false;
        $scope.employees = [];
        $scope.departments = [];
        $scope.locations = [];
        $scope.positions = [];
        $scope.free_employees = [];
        
        $scope.isEmployeeListExist = true;
        $scope.isDepartmentListExist = true;
        $scope.isLocationListExist = true;
        $scope.isPositionListExist = true;
        
        $scope.selected_department = "";
        $scope.selected_location = "";
        $scope.selected_employee = "";
        $scope.selected_position = "";
        
        $scope.filter = {
            date_from: "",
            date_to : ""
        };
        
        $scope.showMode1 = 0;
        $scope.showMode2 = 0;
        $scope.showMode3 = 0;
        $scope.showMode4 = 0;
        
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
                    cellTemplate: '<a ng-click="grid.appScope.updateDepartment(row.entity.department, $event)" class="ListItems">{{row.entity.department}}</a>'
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
                    cellTemplate: '<a ng-click="grid.appScope.updateLocation(row.entity.location, $event)" class="ListItems">{{row.entity.location}}</a>'
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
                    cellTemplate: '<a ng-click="grid.appScope.updateEmployee(row.entity.employee, $event)" class="ListItems">{{row.entity.employee}}</a>'
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
                    cellTemplate: '<a ng-click="grid.appScope.updatePosition(row.entity.position, $event)" class="ListItems">{{row.entity.position}}</a>'
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
        }
        
        $scope.getChartJSONDataForChart1 = [];
        $scope.getChartJSONDataForChart2 = [];
        $scope.getChartJSONDataForChart3 = [];
        $scope.getChartJSONDataForChart4 = [];
        
        $scope.getCSVHeaderForChart1 = [];
        $scope.getCSVHeaderForChart2 = [];
        $scope.getCSVHeaderForChart3 = [];
        $scope.getCSVHeaderForChart4 = [];
        
        $scope.getJSONDataForGrid = [];
        $scope.getCSVHeaderForGrid = [];
        
        $scope.init = function () {
            $scope.userId = cookie.getCookie('user').account_id;
            var perm = cookie.getCookie("permissions");
            if (!perm || perm['50'] == null || perm['50']==undefined)      $scope.isAllowed = false;
            else {
                if (perm['50'].r == '1') $scope.isAllowed = true;
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
                .send('employee_list/' + $scope.userId)
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
            //updateFreeEmployeesGrid();
            
        }
        $scope.updateReport = function() {
            $scope.param['filter_fromdate'] = $scope.filter.date_from;
            $scope.param['filter_todate'] = $scope.filter.date_to;
            $scope.param['dep'] = $scope.selected_department;
            $scope.param['pos'] = $scope.selected_position;
            $scope.param['loc'] = $scope.selected_location;
            $scope.param['emp'] = $scope.selected_employee;
            
            buildGraphForTrainingCourses();
            buildGraphForTrainingEmployees();
            buildPieGraphForTrainingCourses();
            buildBarChartGraphForTrainingCourses();
            
            updateFreeEmployeesGrid();
        }
        $scope.updateDepartment = function(dept, event) {
            console.log(dept, event);
            if($(".grid-departments .ListItems.selected").length>0)
                $(".grid-departments .ListItems.selected").removeClass("selected");
            $(event.target).addClass('selected');
            if(dept=="All Departments") $scope.selected_department = "";
            else $scope.selected_department = dept;
            $scope.updateReport();
        }
        $scope.updateLocation = function(loc, event) {
            
            if($(".grid-locations .ListItems.selected").length>0)
                $(".grid-locations .ListItems.selected").removeClass("selected");
            $(event.target).addClass('selected');
            if(loc=="All Locations") $scope.selected_location = "";
            else $scope.selected_location = loc;
            $scope.updateReport();
        }
        $scope.updateEmployee = function(emp, event) {
            if($(".grid-employees .ListItems.selected").length>0)
                $(".grid-employees .ListItems.selected").removeClass("selected");
            $(event.target).addClass('selected');
            if(emp=="All Employees") $scope.selected_employee = "";
            else $scope.selected_employee = emp;
            $scope.updateReport();
        } 
        $scope.updatePosition = function(pos, event) {
            if($(".grid-positions .ListItems.selected").length>0)
                $(".grid-positions .ListItems.selected").removeClass("selected");
            $(event.target).addClass('selected');
            if(pos=="All Positions") $scope.selected_position = "";
            else $scope.selected_position = pos;
            $scope.updateReport();
        }        
        
        function updateFreeEmployeesGrid(){
            var param = JSON.stringify($scope.param);
            hrmAPIservice
                .send('free_employee_list/'+$scope.userId+"/"+param)
                .then(function(response){
                    if(response.data.res==null) return;
                    $scope.free_employees = response.data.res;
                    $scope.gridOptionsComplexFreeEply.data = $scope.free_employees;
                    
                    $scope.getCSVHeaderForGrid = ['Employee Name', 'Position', 'Department', 'Location'];
                    $scope.getJSONDataForGrid = $scope.free_employees;
                });
        }
        
        const buildGraphForTrainingCourses = function(){ 
            $scope.locationBarLabelsForTrainingCourses = [];
            $scope.locationBarDataForTrainingCourses = [];
            $scope.locationBarColorsForTrainingCourses = [];
            $scope.type = 'StackedBar';
            $scope.locationBarSeriesForTrainingCourses = ['Completed', 'Incomplete', 'Pending', 'Overdue'];
            $scope.locationBarOptionsForTrainingCourses = {
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
                },
                legend: {
                    display: true,
                    labels: {
                        fontColor: 'rgb(0, 0, 0)'
                    },
                    position: "bottom"
                }
            };
            $scope.locationBarColorsForTrainingCourses = [
                {
                    backgroundColor: 'rgba(50, 255, 50, 0.3)',
                    pointBackgroundColor: 'rgba(255,99,132,1)'
                },
                {
                    backgroundColor: 'rgba(50, 50, 255, 0.3)',
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)'
                },
                {
                    backgroundColor: 'rgba(255, 50, 50, 0.3)',
                    pointBackgroundColor: 'rgba(255,99,132,1)'
                },
                {
                    backgroundColor: 'rgba(50, 255, 255, 0.3)',
                    pointBackgroundColor: 'rgba(255,99,132,1)'
                }
            ];
            var graphLabels = [];
            var graphData_completed = [];
            var graphData_pending = [];
            var graphData_incomplete = [];
            var graphData_overdue = [];
            
            var graphData_completed_value = [];
            var graphData_pending_value = [];
            var graphData_incomplete_value = [];
            var graphData_overdue_value = [];
            
            var filter_params = JSON.stringify($scope.param);
            hrmAPIservice.getAnalyticsOfTrainingCourses(userData, $scope.showMode1, 1, filter_params).then(function(response){ //completed
                var count_data = response.data.location_report;
                $.each(count_data, function(key, value){
                    if(value==null) value=0;
                    graphData_completed[key] = value;
                })
                hrmAPIservice.getAnalyticsOfTrainingCourses(userData, $scope.showMode1, 3, filter_params).then(function(response){ //overdue
                    var count_data = response.data.location_report;
                    $.each(count_data, function(key, value){
                        if(value==null) value=0;
                        graphData_incomplete[key] = value;
                    })
                    hrmAPIservice.getAnalyticsOfTrainingCourses(userData, $scope.showMode1, 2, filter_params).then(function(response){ //overdue
                        var count_data = response.data.location_report;
                        $.each(count_data, function(key, value){
                            if(value==null) value=0;
                            graphData_overdue[key] = value;
                        })
                        hrmAPIservice.getAnalyticsOfTrainingCourses(userData, $scope.showMode1, 0, filter_params).then(function(response){ //pending
                            var count_data = response.data.location_report;
                            var index=0;
                            $.each(count_data, function(key, value){
                                if(value==null) value=0;
                                graphData_pending[key] = value;
                                if(graphData_completed[key]+graphData_incomplete[key]+graphData_overdue[key]+graphData_pending[key]!=0){
                                    graphData_completed_value[index]=graphData_completed[key];
                                    graphData_incomplete_value[index]=graphData_incomplete[key];
                                    graphData_overdue_value[index]=graphData_overdue[key];
                                    graphData_pending_value[index]=graphData_pending[key];
                                    graphLabels[index]=key;
                                    index++;
                                }
                            })
                            $scope.locationBarLabelsForTrainingCourses = graphLabels;
                            $scope.locationBarDataForTrainingCourses = [
                                graphData_completed_value,
                                graphData_incomplete_value,
                                graphData_pending_value,
                                graphData_overdue_value
                            ];
                            $scope.getCSVHeaderForChart1 = ['Location', 'Completed', 'Incomplete', 'Pending', 'Overdue'];
                            for(var i=0; i<$scope.locationBarLabelsForTrainingCourses.length; i++){
                                $scope.getChartJSONDataForChart1[i] = {
                                    'location': $scope.locationBarLabelsForTrainingCourses[i],
                                    'completed': graphData_completed_value[i],
                                    'incomplete': graphData_incomplete_value[i],
                                    'pending': graphData_pending_value[i],
                                    'overdue': graphData_overdue_value[i]
                                } 
                            }
                        });
                    });
                });
            });
        }
        const buildGraphForTrainingEmployees = function(){ 
            $scope.locationBarLabelsForTrainingEmployees = [];
            $scope.locationBarDataForTrainingEmployees = [];
            $scope.locationBarColorsForTrainingEmployees = [];
            $scope.type = 'StackedBar';
            $scope.locationBarSeriesForTrainingEmployees = ['Completed', 'Incomplete', 'Pending', 'Overdue'];
            $scope.locationBarOptionsForTrainingEmployees = {
                exportEnabled: "1",
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
                },
                legend: {
                    display: true,
                    labels: {
                        fontColor: 'rgb(0, 0, 0)'
                    },
                    position: "bottom"
                }
            };
            $scope.locationBarColorsForTrainingEmployees = [
                {
                    backgroundColor: 'rgba(50, 255, 50, 0.3)',
                    pointBackgroundColor: 'rgba(255,99,132,1)'
                },
                {
                    backgroundColor: 'rgba(50, 50, 255, 0.3)',
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)'
                },
                {
                    backgroundColor: 'rgba(255, 50, 50, 0.3)',
                    pointBackgroundColor: 'rgba(255,99,132,1)'
                },
                {
                    backgroundColor: 'rgba(50, 255, 255, 0.3)',
                    pointBackgroundColor: 'rgba(255,99,132,1)'
                }
            ];
            
            var graphLabels = [];
            var graphData_completed = [];
            var graphData_pending = [];
            var graphData_incomplete = [];
            var graphData_overdue = [];
            
            var graphData_completed_value = [];
            var graphData_pending_value = [];
            var graphData_incomplete_value = [];
            var graphData_overdue_value = [];
            
            var filter_params = JSON.stringify($scope.param);
            hrmAPIservice.getAnalyticsOfTrainingEmployees(userData, $scope.showMode2, 1, filter_params).then(function(response){ //completed
                var count_data = response.data.location_report;
                $.each(count_data, function(key, value){
                    if(value==null) value=0;
                    graphData_completed[key] = value;
                })
                hrmAPIservice.getAnalyticsOfTrainingEmployees(userData, $scope.showMode2, 3, filter_params).then(function(response){ //overdue
                    var count_data = response.data.location_report;
                    $.each(count_data, function(key, value){
                        if(value==null) value=0;
                        graphData_incomplete[key] = value;
                    })
                    hrmAPIservice.getAnalyticsOfTrainingEmployees(userData, $scope.showMode2, 2, filter_params).then(function(response){ //overdue
                        var count_data = response.data.location_report;
                        $.each(count_data, function(key, value){
                            if(value==null) value=0;
                            graphData_overdue[key] = value;
                        })
                        hrmAPIservice.getAnalyticsOfTrainingEmployees(userData, $scope.showMode2, 0, filter_params).then(function(response){ //pending
                            var count_data = response.data.location_report;
                            var index=0;
                            $.each(count_data, function(key, value){
                                if(value==null) value=0;
                                graphData_pending[key] = value;
                                
                                if(graphData_completed[key]+graphData_incomplete[key]+graphData_overdue[key]+graphData_pending[key]!=0){
                                    graphData_completed_value[index]=graphData_completed[key];
                                    graphData_incomplete_value[index]=graphData_incomplete[key];
                                    graphData_overdue_value[index]=graphData_overdue[key];
                                    graphData_pending_value[index]=graphData_pending[key];
                                    graphLabels[index]=key;
                                    index++;
                                }
                            })
                            $scope.locationBarLabelsForTrainingEmployees = graphLabels;
                            $scope.locationBarDataForTrainingEmployees = [
                                graphData_completed_value,
                                graphData_incomplete_value,
                                graphData_pending_value,
                                graphData_overdue_value
                            ];
                            $scope.getCSVHeaderForChart2 = ["Location", 'Completed', 'Incomplete', 'Pending', 'Overdue'];
                            for(var i=0; i<$scope.locationBarLabelsForTrainingEmployees.length; i++){
                                $scope.getChartJSONDataForChart2[i] = {
                                    'location': $scope.locationBarLabelsForTrainingEmployees[i],
                                    'completed': graphData_completed_value[i],
                                    'incomplete': graphData_incomplete_value[i],
                                    'pending': graphData_pending_value[i],
                                    'overdue': graphData_overdue_value[i]
                                }
                            }
                        });
                    });
                });
            });
        }
        const buildPieGraphForTrainingCourses = function(){ 
            $scope.locationPieOptionsForTrainingCourses = {
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
            var pieLabels = [];
            var pieData_completed = [];
            
            var filter_params = JSON.stringify($scope.param);
            hrmAPIservice.getAnalyticsOfTrainingCourses(userData, $scope.showMode3, 1, filter_params).then(function(response){ //completed
                var count_data = response.data.location_report;
                var total = 0;
                var index = 0;
                $.each(count_data, function(key, value){
                    if(value!=null) {
                        pieData_completed[index] = value;
                        total+=value;
                        pieLabels[index] = key;
                        index++;
                    }
                });
                $scope.locationPieLabelsForTrainingCourses = pieLabels;
                $scope.locationPieDataForTrainingCourses = pieData_completed;
                
                $scope.getCSVHeaderForChart3 = ["Location", 'Completed'];
                for(var i=0; i<$scope.locationPieLabelsForTrainingCourses.length; i++){
                    $scope.getChartJSONDataForChart3[i] = {
                        'location': $scope.locationPieLabelsForTrainingCourses[i],
                        'completed': (parseFloat(pieData_completed[i])*100/parseFloat(total)).toFixed(2) + " %",
                    }
                }
            });
        }
        const buildBarChartGraphForTrainingCourses = function(){ 
            $scope.locationBarChartColorsForTrainingCourses = [
                {
                    backgroundColor: 'rgba(50, 255, 50, 0.2)',
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)'
                },
                {
                    backgroundColor: 'rgba(255, 50, 50, 0.2)',
                    pointBackgroundColor: 'rgba(255,99,132,1)'
                }
            ];
            $scope.locationBarChartSeriesForTrainingCourses = ["Course Pending", "Staff"];
            $scope.locationBarChartOptionsForTrainingCourses = {
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
                        ticks: {
                            autoSkip: false
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                },
                legend: {
                    display: true,
                    labels: {
                        fontColor: 'rgb(0, 0, 0)'
                    },
                    position: "bottom"
                }
            };
            var barChartDataCourses_pending = [];
            var barChartDataEmployees_pending = [];
            var barChartDataCourses_pending_value = [];
            var barChartDataEmployees_pending_value = [];
            
            var barChartLabels = [];
            
            var filter_params = JSON.stringify($scope.param);
            hrmAPIservice.getAnalyticsOfTrainingCourses(userData, $scope.showMode4, 0, filter_params).then(function(response){ //pending
                var count_data = response.data.location_report;
                var index = 0;
                $.each(count_data, function(key, value){
                    if(value==null) value=0;
                    barChartDataCourses_pending[key] = value;
                });
                hrmAPIservice.getAnalyticsOfTrainingEmployees(userData, $scope.showMode4, 0, filter_params).then(function(response){ //pending
                    var count_data = response.data.location_report;
                    var index = 0;
                    $.each(count_data, function(key, value){
                        if(value==null) value=0;
                        barChartDataEmployees_pending[key] = value;
                        if(barChartDataCourses_pending[key]+barChartDataEmployees_pending[key]!=0){
                            barChartDataCourses_pending_value[index] = barChartDataCourses_pending[key];
                            barChartDataEmployees_pending_value[index] = barChartDataEmployees_pending[key];
                            barChartLabels[index] = key;
                            index++;
                        }
                    });
                    $scope.locationBarChartLabelsForTrainingCourses = barChartLabels;
                    $scope.locationBarChartDataForTrainingCourses = [
                        barChartDataCourses_pending_value,
                        barChartDataEmployees_pending_value
                    ];
                    
                    $scope.getCSVHeaderForChart4 = ["Location", 'Pending Courses', 'Pending Employees'];
                    for(var i=0; i<$scope.locationBarChartLabelsForTrainingCourses.length; i++){
                        $scope.getChartJSONDataForChart4[i] = {
                            'location': $scope.locationBarChartLabelsForTrainingCourses[i],
                            'pending_courses': barChartDataCourses_pending[i],
                            'pending_employee': barChartDataEmployees_pending[i]
                        }
                    }
                });
            });
        }
        
        $scope.changeShowModeForGraph1 = function(){
            $scope.showMode1 = 1-$scope.showMode1;
            buildGraphForTrainingCourses();
        }
        $scope.changeShowModeForGraph2 = function(){
            $scope.showMode2 = 1-$scope.showMode2;
            buildGraphForTrainingEmployees();
        }
        $scope.changeShowModeForGraph3 = function(){
            $scope.showMode3 = 1-$scope.showMode3;
            buildPieGraphForTrainingCourses();
        }
        $scope.changeShowModeForGraph4 = function(){
            $scope.showMode4 = 1-$scope.showMode4;
            buildBarChartGraphForTrainingCourses();
        }
    }
]);
