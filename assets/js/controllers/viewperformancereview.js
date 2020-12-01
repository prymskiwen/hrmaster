app.controller('viewperformancereviewController', ['$scope', '$rootScope', 'cookie','uiGridConstants', '$location', 'hrmAPIservice', function ($scope, $rootScope, cookie, uiGridConstants, $location, hrmAPIservice) {
    var userData = cookie.checkLoggedIn();
    cookie.getPermissions();
    
    $scope.isAllowed = false;
    var perm = cookie.getCookie("permissions");
    if (!perm || perm['43'] == null || perm['43']==undefined)      $scope.isAllowed = false;
    else {
        if (perm['43'].r == '1') $scope.isAllowed = true;
        else                     $scope.isAllowed = false;
    }
    if(!$scope.isAllowed){
        cookie.deleteCookie('user');
        cookie.deleteCookie('permissions');
        $rootScope.isLoggedin = 0;
        $location.path('/');
    }
    
    var perms = $rootScope.perms;
    $scope.edit_option = 0;
    $scope.pageTitle = "View Performance Review";
    $scope.formEnabled = 0;
    $scope.master = {};
    console.log(userData);
    
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
          { name: 'p_forms_id', visible: false },
          { name: 'questions', visible: false },
          { name: 'scores', visible: false },
          { name: 'comments', visible: false },
          { name: 'manager_name', displayName: 'Manager Conducting Review', width: '25%', cellClass: 'center', enableCellEdit: false },
          { name: 'completed_date', displayName: 'Date of Review', width: '30%', enableFiltering: true, cellClass: 'center',enableCellEdit: false},
          { name: 'assessment_date', displayName: 'Date For Next Review', width: '30%', enableFiltering: true, cellClass: 'center',enableCellEdit: false},
          { name: 'action', enableFiltering: false, width: '15%',  cellClass: 'center', enableCellEdit: false,
              cellTemplate: '<div class="ui-grid-cell-contents grid-center-cell"><span ng-click="grid.appScope.editForm(row.entity)"><span class="glyphicon glyphicon-edit text-edit"></span></div>'
          }
        ]
    };
    
    
    $scope.editForm = function(obj) {
        $scope.formEnabled = 1;
        $scope.specializedQuestionList = [];
        $scope.scoreList = [];
        $scope.commentList = [];
        $scope.questions = obj.questions.split("~#");
        angular.forEach($scope.questions, function (value, key) {
            if(key < $scope.questions.length - 1) $scope.specializedQuestionList.push({id : key + 1, question_text : value});
        });  
        
        $scope.selected_employee_name = obj.manager_name;
        if(obj.scores == ""){
            $scope.edit_option = obj.id;
            return
        }else{
            $scope.scores = obj.scores.split(",");
            angular.forEach($scope.scores, function (value, key) {
                if(key < $scope.scores.length - 1) $scope.scoreList.push({id : key + 1, score : value});
            });
            $scope.comments = obj.comments.split("~#");
            angular.forEach($scope.comments, function (value, key) {
                if(key < $scope.comments.length - 1) $scope.commentList.push({id : key + 1, comment : value});
            });
        }
    }

    hrmAPIservice.getFormReviewsForView(userData).then(function(response) {
        console.log(response.data);
        $scope.gridOptionsComplex.data = response.data.form_reviews.map(function(review){
            return{
                id: review.id,
                p_forms_id: review.p_forms_id,
                questions: review.questions,
                scores: review.scores,
                comments: review.comments,
                manager_name: review.manager_name,
                assessment_date: $scope.formatDate(review.assessment_date), 
                //assessment_date: $scope.formatDate(review.assessment_date), 
                completed_date: $scope.formatDate(review.completed_date)
            }
        });
        $scope.standardQuestionList = response.data.standard_questions;
    });
    
    

    $scope.formatDate = function(date){
        if(date == null) return '';
        var d = date.split("-");
        return d[2] + "-" + d[1] + "-" + d[0];
    }
    
}]);
