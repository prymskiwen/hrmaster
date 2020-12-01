app.controller('eventreportsController', ['$scope', '$rootScope', 'cookie', '$location', 'hrmAPIservice', function ($scope, $rootScope, cookie, $location, hrmAPIservice) {
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
    $scope.getChartJSONDataForChart1 = [];
    $scope.getChartJSONDataForChart2 = [];
        
    $scope.showMode1 = 0;
    $scope.showMode2 = 0;
    
    
    $scope.graph2Data = [];
    
    $scope.isAllowed = false;
    var perm = cookie.getCookie("permissions");
    
    console.log(perm);
    if (!perm || perm['54'] == null || perm['54']==undefined)      $scope.isAllowed = false;
    else {
        if (perm['54'].r == '1') $scope.isAllowed = true;
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
        if (perm['54'] == null || perm['54']==undefined)      $scope.isAllowed = false;
        else {
            if (perm['54'].r == '1') $scope.isAllowed = true;
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
        
    }
    $scope.updateReport = function() {
        $scope.param['filter_fromdate'] = $scope.filter.date_from;
        $scope.param['filter_todate'] = $scope.filter.date_to;
        $scope.param['dep'] = $scope.selected_department;
        $scope.param['pos'] = $scope.selected_position;
        $scope.param['loc'] = $scope.selected_location;
        $scope.param['emp'] = $scope.selected_employee;
        
        buildGraph1ForCourseStatus();//reviews(pending, completed, overdue) by locations
        buildGraph2ForCourseCost();//upcoming reviews(pending) by locations
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
    function ReplaceNumberWithCommas(yourNumber) {
        //Seperates the components of the number
        var n= yourNumber.toString().split(".");
        //Comma-fies the first part
        n[0] = n[0].replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        //Combines the two sections
        return n.join(".");
    }
    function updateFreeEmployeesGrid(){
        var param = JSON.stringify($scope.param);
        hrmAPIservice
            .send('noschedule_employee_list/'+$scope.userId+"/"+param)
            .then(function(response){
                if(response.data.res==null) return;
                $scope.free_employees = response.data.res;
                $scope.gridOptionsComplexFreeEply.data = $scope.free_employees;
                
                $scope.getCSVHeaderForGrid = ['User ID', 'Employee Name', 'Location', 'Department', 'Position'];
                $scope.getJSONDataForGrid = $scope.free_employees;
            });
    }
    function buildGraph1ForCourseStatus(){ 
            $scope.locationBarLabelsForCourseStatus = [];
            $scope.locationBarDataForCourseStatus = [];
            $scope.locationBarColorsForCourseStatus = [];
            $scope.type = 'StackedBar';
            $scope.locationBarSeriesForCourseStatus = ['Completed', 'Pending', 'Not attend'];
            $scope.locationBarOptionsForCourseStatus = {
                layout: {
                    padding: {
                        top: 30,
                        left: 10,
                        right: 10,
                        bottom: 10
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
                },
                legend: {
                    display: true,
                    labels: {
                        fontColor: 'rgb(0, 0, 0)'
                    },
                    position: "bottom"
                }
            };
            $scope.locationBarColorsForCourseStatus = [
                {
                    backgroundColor: 'rgba(50, 255, 50, 0.2)'
                },
                {
                    backgroundColor: 'rgba(255, 50, 50, 0.2)'
                },
                {
                    backgroundColor: 'rgba(50, 50, 255, 0.2)'
                },
            ];
            var graphLabels = [];
            var graphData_completed = [];
            var graphData_pending = [];
            var graphData_notAttend = [];
            
            var graphData_completed_value = [];
            var graphData_pending_value = [];
            var graphData_notAttend_value = [];
            
            var filter_params = JSON.stringify($scope.param);
            hrmAPIservice.getAnalyticsOfLQTrainingCourses(userData, $scope.showMode1, 1, filter_params).then(function(response){ //completed
                var count_data = response.data.location_report;
                $.each(count_data, function(key, value){
                    if(value==null) value=0;
                    graphData_completed[key] = value;
                })
                hrmAPIservice.getAnalyticsOfLQTrainingCourses(userData, $scope.showMode1, 0, filter_params).then(function(response){ //pending
                    var count_data = response.data.location_report;
                    $.each(count_data, function(key, value){
                        if(value==null) value=0;
                        graphData_pending[key] = value;
                    })
                    hrmAPIservice.getAnalyticsOfLQTrainingCourses(userData, $scope.showMode1, -1, filter_params).then(function(response){ //overdue
                        var count_data = response.data.location_report;
                        var index=0;
                        $.each(count_data, function(key, value){
                            if(value==null) value=0;
                            graphData_notAttend[key] = value;
                            if(graphData_completed[key]+graphData_pending[key]+graphData_notAttend[key]!=0){
                                graphData_completed_value[index]=graphData_completed[key];
                                graphData_pending_value[index]=graphData_pending[key];
                                graphData_notAttend_value[index]=graphData_notAttend[key]; 
                                graphLabels[index]=key;
                                index++;
                            }
                        })
                        $scope.locationBarLabelsForCourseStatus = graphLabels;
                        $scope.locationBarDataForCourseStatus = [
                            graphData_completed_value,
                            graphData_pending_value,
                            graphData_notAttend_value
                        ];
                        $scope.getCSVHeaderForChart1 = ['Location', 'Completed', 'Pending', 'Not Attend'];
                        for(var i=0; i<$scope.locationBarLabelsForCourseStatus.length; i++){
                            $scope.getChartJSONDataForChart1[i] = {
                                'location': $scope.locationBarLabelsForCourseStatus[i],
                                'completed': graphData_completed_value[i],
                                'pending': graphData_pending_value[i],
                                'not_attend': graphData_notAttend_value[i]
                            } 
                        }
                    });
                });
            });
        }
    function buildGraph2ForCourseCost(){ 
        $scope.locationBarLabelsForCourseCost = [];
        $scope.locationBarDataForCourseCost = [];
        $scope.locationBarColorsForCourseCost = [];
        $scope.locationBarOptionsForCourseCost = {
            layout: {
                padding: {
                    top: 30,
                    left: 10,
                    right: 10,
                    bottom: 10
                }
            },
            maintainAspectRatio: false,
            //responsive:true,
            plugins: {
                datalabels: {
                    display: function(context) {
                        return context.dataset.data[context.dataIndex] !== 0; // or >= 1 or ...
                    },
                    align: 'center',
                    anchor: 'center',
                    color: '#000',
                    formatter: (value, ctx) =>{
                        const label = "$"+ReplaceNumberWithCommas(value);
                        return label;
                    }
                }
            },
            scales: {
                xAxes: [{
                    ticks: {
                        autoSkip: false,
                    }
                }],
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                    }
                }]
            },
            tooltips: {
                //mode: "label",
                callbacks: {
                    label: function(tooltipItem, data) {
                        var legend = new Array();
                        for(var i in data.datasets){
                            legend.push(
                                $scope.graph2Data[$scope.locationBarLabelsForCourseCost[tooltipItem.index]].course_count+ " Courses = $ " + ReplaceNumberWithCommas(parseFloat($scope.graph2Data[$scope.locationBarLabelsForCourseCost[tooltipItem.index]].total_cost).toFixed(2))
                            );
                            legend.push(
                                $scope.graph2Data[$scope.locationBarLabelsForCourseCost[tooltipItem.index]].course_count+ " Courses = " + $scope.graph2Data[$scope.locationBarLabelsForCourseCost[tooltipItem.index]].total_hours + " hours in total"
                            );
                            legend.push(
                                $scope.graph2Data[$scope.locationBarLabelsForCourseCost[tooltipItem.index]].attendee_count+" Staff Attended = $ " + ReplaceNumberWithCommas(parseFloat($scope.graph2Data[$scope.locationBarLabelsForCourseCost[tooltipItem.index]].wasted_cost).toFixed(2))
                            );
                        }
            
                        return legend;
                    }
                }
            },
        };
        $scope.locationBarColorsForCourseCost = [
            {
                fillColor: 'rgba(50, 255, 50, 0.2)'
            }
        ];
        
        var graphLabels = [];
        var graphData = [];
        var graphData_value = [];
        
        var filter_params = JSON.stringify($scope.param);
        hrmAPIservice.getAnalyticsOfLQTrainingCourseCost(userData, $scope.showMode2, filter_params).then(function(response){ //overdue
            var data = response.data.location_report;
            $scope.graph2Data = data;
            
            console.log($scope.graph2Data);
            var index = 0;
            $.each(data, function(key, value){
                graphData[key] = (parseFloat(value.total_cost)+parseFloat(value.wasted_cost)).toFixed(2);
                if(graphData[key]!=0){
                    graphData_value[index]=graphData[key];
                    graphLabels[index]=key;
                    index++;
                }
            })
            $scope.locationBarLabelsForCourseCost = graphLabels;
            $scope.locationBarDataForCourseCost = graphData_value;
            $scope.getCSVHeaderForChart2 = ['Location', 'Total Cost', 'Number of Courses', 'Total Hours', 'Number of Attendees', 'Total Wasted Cost'];
            index = 0;
            $.each($scope.graph2Data, function(key, value){
                $scope.getChartJSONDataForChart2[index++] = {
                    'location': key,
                    'total_cost': '$ '+value.total_cost,
                    'course_count': value.course_count,
                    'total_hours': value.total_hours,
                    'attendee_count': value.attendee_count,
                    'total_wasted_cost': '$ '+value.wasted_cost
                } 
            });
        });
    }
    $scope.changeShowModeForGraph1 = function(){
        $scope.showMode1 = 1-$scope.showMode1;
        buildGraph1ForCourseStatus();
    }
    $scope.changeShowModeForGraph2 = function(){
        $scope.showMode2 = 1-$scope.showMode2;
        buildGraph2ForCourseCost();
    }
}]);
