app.controller('performancereportsController', [
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
        $scope.showMode4 = 0;
        $scope.showMode6 = 0;
        
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
        
        $scope.getChartJSONDataForChart1 = [];
        $scope.getChartJSONDataForChart2 = [];
        $scope.getChartJSONDataForChart3= [];
        
        $scope.getCSVHeaderForChart1 = [];
        $scope.getCSVHeaderForChart2 = [];
        $scope.getCSVHeaderForChart3 = [];
        
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
        $scope.init = function () {
            $scope.userId = cookie.getCookie('user').account_id;
            var perm = cookie.getCookie("permissions");
            if (!perm || perm['45'] == null || perm['45']==undefined)      $scope.isAllowed = false;
            else {
                if (perm['45'].r == '1') $scope.isAllowed = true;
                else                     $scope.isAllowed = false;
            }
            if(!$scope.isAllowed){
                cookie.deleteCookie('user');
                cookie.deleteCookie('permissions');
                $rootScope.isLoggedin = 0;
                $location.path('/');
            }
            
           /* var now = new Date();
            now.setMonth(now.getMonth() - 12);
            $scope.filter.date_from = now;
            $scope.filter.date_to = new Date();*/
            
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
            
            buildGraph1ForPerformanceReviews();//reviews(pending, completed, overdue) by locations
            buildGraph2ForPerformanceReviews();//upcoming reviews(pending) by locations
            buildGraph3ForPerformanceReviews();//selected employee's reviews by questions
            buildGraph4ForPerformanceReviews();//review scores by employees
            buildGraph6ForPerformanceReviews();//in-time completed reviews by location
            updateFreeEmployeesGrid(); //unassigned review employees
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
        
        function buildGraph1ForPerformanceReviews(){ 
            $scope.locationBarLabelsForPerformanceReviews = [];
            $scope.locationBarDataForPerformanceReviews = [];
            $scope.locationBarColorsForPerformanceReviews = [];
            $scope.type = 'StackedBar';
            $scope.locationBarSeriesForPerformanceReviews = ['Completed', 'Pending', 'Overdue'];
            $scope.locationBarOptionsForPerformanceReviews = {
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
            $scope.locationBarColorsForPerformanceReviews = [
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
            var graphData_overdue = [];
            
            var graphData_completed_value = [];
            var graphData_pending_value = [];
            var graphData_overdue_value = [];
            
            var filter_params = JSON.stringify($scope.param);
            hrmAPIservice.getAnalyticsOfPerformanceReviews(userData, $scope.showMode1, "completed", filter_params).then(function(response){ //completed
                var count_data = response.data.location_report;
                $.each(count_data, function(key, value){
                    if(value==null) value=0;
                    graphData_completed[key] = value;
                })
                hrmAPIservice.getAnalyticsOfPerformanceReviews(userData, $scope.showMode1, "pending", filter_params).then(function(response){ //pending
                    var count_data = response.data.location_report;
                    $.each(count_data, function(key, value){
                        if(value==null) value=0;
                        graphData_pending[key] = value;
                    })
                    hrmAPIservice.getAnalyticsOfPerformanceReviews(userData, $scope.showMode1, "overdue", filter_params).then(function(response){ //overdue
                        var count_data = response.data.location_report;
                        var index=0;
                        $.each(count_data, function(key, value){
                            if(value==null) value=0;
                            graphData_overdue[key] = value;
                            if(graphData_completed[key]+graphData_pending[key]+graphData_overdue[key]!=0){
                                graphData_completed_value[index]=graphData_completed[key];
                                graphData_pending_value[index]=graphData_pending[key];
                                graphData_overdue_value[index]=graphData_overdue[key]; 
                                graphLabels[index]=key;
                                index++;
                            }
                        })
                        $scope.locationBarLabelsForPerformanceReviews = graphLabels;
                        $scope.locationBarDataForPerformanceReviews = [
                            graphData_completed_value,
                            graphData_pending_value,
                            graphData_overdue_value
                        ];
                        $scope.getCSVHeaderForChart1 = ['Location', 'Completed', 'Pending', 'Overdue'];
                        for(var i=0; i<$scope.locationBarLabelsForPerformanceReviews.length; i++){
                            $scope.getChartJSONDataForChart1[i] = {
                                'location': $scope.locationBarLabelsForPerformanceReviews[i],
                                'completed': graphData_completed_value[i],
                                'pending': graphData_pending_value[i],
                                'overdue': graphData_overdue_value[i]
                            } 
                        }
                    });
                });
            });
        }
        function buildGraph2ForPerformanceReviews(){ 
            $scope.locationBarChartColorsForPerformanceReviews = [
                {
                    backgroundColor: 'rgba(50, 255, 50, 0.2)',
                    pointBackgroundColor: 'rgba(54, 162, 235, 1)'
                },
                {
                    backgroundColor: 'rgba(255, 50, 50, 0.2)',
                    pointBackgroundColor: 'rgba(255,99,132,1)'
                }
            ];
            $scope.locationBarChartSeriesForPerformanceReviews = ["Review Pending", "Staff"];
            $scope.locationBarChartOptionsForPerformanceReviews = {
                layout: {
                    padding: {
                        top: 30,
                        left: 10,
                        right: 10,
                        bottom: 10
                    }
                },
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
            var barChartDataReviews_pending = [];
            var barChartDataEmployees_pending = [];
            var barChartDataReviews_pending_value = [];
            var barChartDataEmployees_pending_value = [];
            
            var barChartLabels = [];
            
            var filter_params = JSON.stringify($scope.param);
            hrmAPIservice.getAnalyticsOfPerformanceReviews(userData, $scope.showMode2, "pending", filter_params).then(function(response){ //pending
                var count_data = response.data.location_report;
                var index = 0;
                $.each(count_data, function(key, value){
                    if(value==null) value=0;
                    barChartDataReviews_pending[key] = value;
                });
                hrmAPIservice.getAnalyticsOfPerformanceEmployees(userData, $scope.showMode2, "pending", filter_params).then(function(response){ //pending
                    var count_data = response.data.location_report;
                    var index = 0;
                    $.each(count_data, function(key, value){
                        if(value==null) value=0;
                        barChartDataEmployees_pending[key] = value;
                        if(barChartDataReviews_pending[key]+barChartDataEmployees_pending[key]!=0){
                            barChartDataReviews_pending_value[index] = barChartDataReviews_pending[key];
                            barChartDataEmployees_pending_value[index] = barChartDataEmployees_pending[key];
                            barChartLabels[index] = key;
                            index++;
                        }
                    });
                    $scope.locationBarChartLabelsForPerformanceReviews = barChartLabels;
                    $scope.locationBarChartDataForPerformanceReviews = [
                        barChartDataReviews_pending_value,
                        barChartDataEmployees_pending_value
                    ];
                    
                    $scope.getCSVHeaderForChart2 = ["Location", 'Pending Reviews', 'Pending Employees'];
                    for(var i=0; i<$scope.locationBarChartLabelsForPerformanceReviews.length; i++){
                        $scope.getChartJSONDataForChart2[i] = {
                            'location': $scope.locationBarChartLabelsForPerformanceReviews[i],
                            'pending_reviews': barChartDataReviews_pending_value[i],
                            'pending_employee': barChartDataEmployees_pending_value[i]
                        }
                    }
                });
            });
        }
        function buildGraph3ForPerformanceReviews(){ 
            $scope.BarColorsForEmployeePerformanceReviews = [
                {
                    backgroundColor: 'rgba(50, 255, 50, 0.2)'
                },
                {
                    backgroundColor: 'rgba(255, 50, 50, 0.2)'
                },
                {
                    backgroundColor: 'rgba(50, 50, 255, 0.2)'
                },
                {
                    backgroundColor: 'rgba(255, 255, 50, 0.2)'
                },
                {
                    backgroundColor: 'rgba(255, 50, 255, 0.2)'
                },
                {
                    backgroundColor: 'rgba(50, 255, 255, 0.2)'
                },
                {
                    backgroundColor: 'rgba(50, 50, 50, 0.2)'
                },
                {
                    backgroundColor: 'rgba(200, 200, 200, 0.2)'
                }
            ];
            $scope.BarOptionsForEmployeePerformanceReviews = {
                layout: {
                    padding: {
                        top: 30,
                        left: 10,
                        right: 10,
                        bottom: 10
                    }
                },
                maintainAspectRatio: false,
                responsive:true,
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
                            autoSkip: false,
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 1,
                            min: 0,
                            max: 5
                        }
                    }]
                }
            };
            var filter_params = JSON.stringify($scope.param);
            $scope.BarDataForEmployeePerformanceReviews = [];
            $scope.BarLabelsForEmployeePerformanceReviews = [];
            hrmAPIservice.getAnalyticsOfEmployeePerformanceReviews(userData, filter_params).then(function(response){
                if(response.data!=false){
                    console.log(response.data);
                    var reviews = response.data.reviews;
                    var questions = response.data.questions;
                    $scope.BarSeriesForEmployeePerformanceReviews = "";
                    if(questions){
                        for(var i=0; i<reviews[questions[0]].length; i++){
                            var q_review = [];
                            for(var j=0; j<questions.length; j++)
                                q_review[j] = reviews[questions[j]][i].score;
                            $scope.BarDataForEmployeePerformanceReviews.push(q_review);
                        }
                        for(var i=0; i<questions.length; i++){
                            var dates = [];
                            for(var j=0; j<reviews[questions[i]].length; j++)
                                dates.push(reviews[questions[i]][j].date);
                            console.log(dates);
                            $scope.BarLabelsForEmployeePerformanceReviews.push("Q"+(i+1));
                            $scope.BarOptionsForEmployeePerformanceReviews.tooltips= {
                                enabled: true,
                                mode: 'label',
                                callbacks: {
                                    title: function(tooltipItems, data) {
                                        var idx = tooltipItems[0].index;
                                        return questions[idx];//do something with title
                                    },
                                    label: function(tooltipItems, data) {
                                        console.log(tooltipItems);
                                        var idx = tooltipItems.index;
                                        return dates[tooltipItems.datasetIndex]+": "+tooltipItems.yLabel;
                                    }
                                }
                            }
                        }
                        $scope.getCSVHeaderForChart3.push("Questions");
                        for(var i=0; i<dates.length; i++)
                            $scope.getCSVHeaderForChart3.push(dates[i]);
                        for(var i=0; i<questions.length; i++){
                            $scope.getChartJSONDataForChart3[i] = [];
                            $scope.getChartJSONDataForChart3[i].push(questions[i]);
                            for(var j=0; j<dates.length; j++)
                                $scope.getChartJSONDataForChart3[i].push(reviews[questions[i]][j].score);
                        }
                    }
                }
                else console.log("select employee");
            });
        }
        function buildGraph4ForPerformanceReviews(){ 
            $scope.BarColorsForEmployeePerformanceScore = [
                {
                    backgroundColor: 'rgba(50, 255, 50, 0.2)'
                }
            ];
            $scope.BarOptionsForEmployeePerformanceScore = {
                layout: {
                    padding: {
                        top: 30,
                        left: 10,
                        right: 10,
                        bottom: 10
                    }
                },
                maintainAspectRatio: false,
                responsive:true,
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
                            autoSkip: false,
                            minRotation: 90
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: 30
                        }
                    }]
                },
                
            };
            var filter_params = JSON.stringify($scope.param);
            $scope.BarDataForEmployeePerformanceScore = [];
            $scope.BarLabelsForEmployeePerformanceScore = [];
            hrmAPIservice.getAnalyticsOfEmployeePerformanceScore(userData, $scope.showMode4, filter_params).then(function(response){
                if(response.data!=false){
                    var scores = response.data.scores;
                    var employees = response.data.employees;
                    $scope.BarLabelsForEmployeePerformanceScore = employees;
                    if(employees.length>0){
                        for(var i=0; i<employees.length; i++){
                            $scope.BarDataForEmployeePerformanceScore.push(scores[employees[i]]);
                        }
                        if(employees.length>=8)
                            $(".container-4").css('width', 50*employees.length+"px");
                        else $(".container-4").css('width', '400px');
                        $scope.getCSVHeaderForChart4=["Employee Name", "Score"];
                        $scope.getChartJSONDataForChart4 = [];
                        for(var i=0; i<employees.length; i++){
                            $scope.getChartJSONDataForChart4[i] = {
                                "employee_name": employees[i],
                                "score": scores[employees[i]]
                            }
                        }
                    }
                    else $(".container-4").css('width', "400px");
                }
            });
        }
        function buildGraph6ForPerformanceReviews(){
            $scope.PieOptionsForReviews = {
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
            hrmAPIservice.getInTimeCompletedReviews(userData, $scope.showMode6, filter_params).then(function(response){ //completed
                console.log(response.data);
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
                $scope.PieLabelsForReviews = pieLabels;
                $scope.PieDataForReviews = pieData_completed;
                
                $scope.getCSVHeaderForChart6 = ["Location", 'Completed'];
                $scope.getChartJSONDataForChart6 = [];
                for(var i=0; i<$scope.PieLabelsForReviews.length; i++){
                    $scope.getChartJSONDataForChart6[i] = {
                        'location': $scope.PieLabelsForReviews[i],
                        'completed': (parseFloat(pieData_completed[i])*100/parseFloat(total)).toFixed(2) + " %",
                    }
                }
            });
        }
        function updateFreeEmployeesGrid(){
            var param = JSON.stringify($scope.param);
            hrmAPIservice
                .send('noreview_employee_list/'+$scope.userId+"/"+param)
                .then(function(response){
                    if(response.data.res==null) return;
                    $scope.free_employees = response.data.res;
                    $scope.gridOptionsComplexFreeEply.data = $scope.free_employees;
                    
                    $scope.getCSVHeaderForGrid = ['Employee Name', 'Position', 'Department', 'Location'];
                    $scope.getJSONDataForGrid = $scope.free_employees;
                });
        }
        $scope.changeShowModeForGraph1 = function(){
            $scope.showMode1 = 1-$scope.showMode1;
            buildGraph1ForPerformanceReviews();
        }
        $scope.changeShowModeForGraph2 = function(){
            $scope.showMode2 = 1-$scope.showMode2;
            buildGraph2ForPerformanceReviews();
        }
        $scope.changeShowModeForGraph4 = function(){
            $scope.showMode4 = 1-$scope.showMode4;
            buildGraph4ForPerformanceReviews();
        }
        $scope.changeShowModeForGraph6 = function(){
            $scope.showMode6 = 1-$scope.showMode6;
            buildGraph6ForPerformanceReviews();
        }
    }
]);
