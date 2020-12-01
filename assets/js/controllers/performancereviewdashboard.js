app.controller('performancereviewdashboardController', ['$scope', '$rootScope', 'cookie','uiGridConstants', '$location', 'hrmAPIservice', function ($scope, $rootScope, cookie, uiGridConstants, $location, hrmAPIservice) {
    var userData = cookie.checkLoggedIn();
    cookie.getPermissions();
    
    $scope.isAllowed = false;
    var perm = cookie.getCookie("permissions");
    console.log(perm);
    if (!perm || perm['51'] == null || perm['51']==undefined)      $scope.isAllowed = false;
    else {
        if (perm['51'].r == '1') $scope.isAllowed = true;
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
    var showMode = 0;
    var ownMode = 0;
    var locationMode = 0;
    
    $scope.showMode = 0;
    $scope.ownMode = 0;
    $scope.locationMode = 0;
    
    $scope.pageTitle = "Performance Review Dashboard";
    $scope.formEnabled = 0;
    $scope.master = {};
    
    if(!angular.isDefined($rootScope.perms.performancereviewdashboard)){
        $scope.performancereviewdashboard_read = 0;
        $scope.performancereviewdashboard_write = 0;
        $scope.performancereviewdashboard_delete = 0;
    }else{
        $scope.performancereviewdashboard_read = ($rootScope.perms.performancereviewdashboard.read > 0) ? true : false; //tranning permission
        $scope.performancereviewdashboard_write = ($rootScope.perms.performancereviewdashboard.write > 0) ? true : false; //tranning permission
        $scope.performancereviewdashboard_delete = ($rootScope.perms.performancereviewdashboard.delete > 0) ? true : false; //tranning permission
    }
    
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
          { name: 'person_name', displayName: 'Person Name', width: '15%', cellClass: 'center', enableCellEdit: false },
          { name: 'date_reviewed', displayName: 'Date of Review', width: '15%', cellClass: 'center', enableCellEdit: false },
          { name: 'manager_name', displayName: 'Person Conducting Review', width: '20%', enableFiltering: true, cellClass: 'center',enableCellEdit: false},
          { name: 'site_location', displayName: 'Site Location', width: '15%', enableFiltering: true, cellClass: 'center',enableCellEdit: false},
          { name: 'assessment_date', displayName: 'Date for Next Review', width: '15%', enableFiltering: true, cellClass: 'center',enableCellEdit: false},
          { name: 'score', displayName: 'Score', width: '15%', enableFiltering: true, cellClass: 'center',enableCellEdit: false},
          { name: 'action', enableFiltering: false, width: '5%',  cellClass: 'center', enableCellEdit: false,
              cellTemplate: '<div class="ui-grid-cell-contents grid-center-cell"><span ng-click="grid.appScope.editForm(row.entity)" ng-show="grid.appScope.performancereviewdashboard_read"><span class="glyphicon glyphicon-edit text-edit"></span></div>'
          }
        ]
    };
    $scope.showActiveData = function(){
        $scope.showMode = 0;
        showMode = $scope.showMode;
        hrmAPIservice.getAllFormReviewsForView(userData, showMode, ownMode, locationMode).then(function(response) {
            console.log(response.data);
            $scope.gridOptionsComplex.data = response.data.form_reviews.map(function(review){
                var scores = review.scores.split(',');
                var sum = 0;
                for(var i=0; i<scores.length; i++)
                    if(scores[i]!="")
                        sum+=parseInt(scores[i]);
                console.log(scores);
                return{
                    id: review.id,
                    p_forms_id: review.p_forms_id,
                    questions: review.questions,
                    scores: review.scores,
                    comments: review.comments,
                    person_name: review.person_name,
                    manager_name: review.manager_name,
                    date_reviewed: $scope.formatDate(review.completed_date),
                    assessment_date: $scope.formatDate(review.assessment_date), 
                    site_location: review.site_location,
                    completed_date: $scope.formatDate(review.completed_date),
                    score: sum
                }
            });
            $scope.standardQuestionList = response.data.standard_questions;
        });
    }
    
    $scope.showAllData = function(){
        $scope.showMode = 1;
        showMode = $scope.showMode;
        hrmAPIservice.getAllFormReviewsForView(userData, showMode, ownMode, locationMode).then(function(response) {
            console.log(response.data);
            $scope.gridOptionsComplex.data = response.data.form_reviews.map(function(review){
                var scores = review.scores.split(',');
                var sum = 0;
                for(var i=0; i<scores.length; i++)
                    if(scores[i]!="")
                        sum+=parseInt(scores[i]);
                console.log(scores);
                return{
                    id: review.id,
                    p_forms_id: review.p_forms_id,
                    questions: review.questions,
                    scores: review.scores,
                    comments: review.comments,
                    person_name: review.person_name,
                    manager_name: review.manager_name,
                    date_reviewed: $scope.formatDate(review.completed_date),
                    assessment_date: $scope.formatDate(review.assessment_date), 
                    site_location: review.site_location,
                    completed_date: $scope.formatDate(review.completed_date),
                    score: sum
                }
            });
            $scope.standardQuestionList = response.data.standard_questions;
        });
    }
    $scope.showOwnReviewsData = function(){
        $scope.ownMode = 0;
        ownMode = $scope.ownMode;
        hrmAPIservice.getAllFormReviewsForView(userData, showMode, ownMode, locationMode).then(function(response) {
            console.log(response.data);
            $scope.gridOptionsComplex.data = response.data.form_reviews.map(function(review){
                var scores = review.scores.split(',');
                var sum = 0;
                for(var i=0; i<scores.length; i++)
                    if(scores[i]!="")
                        sum+=parseInt(scores[i]);
                console.log(scores);
                return{
                    id: review.id,
                    p_forms_id: review.p_forms_id,
                    questions: review.questions,
                    scores: review.scores,
                    comments: review.comments,
                    person_name: review.person_name,
                    manager_name: review.manager_name,
                    date_reviewed: $scope.formatDate(review.completed_date),
                    assessment_date: $scope.formatDate(review.assessment_date), 
                    site_location: review.site_location,
                    completed_date: $scope.formatDate(review.completed_date),
                    score: sum
                }   
            });
            $scope.standardQuestionList = response.data.standard_questions;
        });
    }
    $scope.showAllReviewsData = function(){
        $scope.ownMode = 1;
        ownMode = $scope.ownMode;
        hrmAPIservice.getAllFormReviewsForView(userData, showMode, ownMode, locationMode).then(function(response) {
            console.log(response.data);
            $scope.gridOptionsComplex.data = response.data.form_reviews.map(function(review){
                var scores = review.scores.split(',');
                var sum = 0;
                for(var i=0; i<scores.length; i++)
                    if(scores[i]!="")
                        sum+=parseInt(scores[i]);
                console.log(scores);
                return{
                    id: review.id,
                    p_forms_id: review.p_forms_id,
                    questions: review.questions,
                    scores: review.scores,
                    comments: review.comments,
                    person_name: review.person_name,
                    manager_name: review.manager_name,
                    date_reviewed: $scope.formatDate(review.completed_date),
                    assessment_date: $scope.formatDate(review.assessment_date), 
                    site_location: review.site_location,
                    completed_date: $scope.formatDate(review.completed_date),
                    score: sum
                }
            });
            $scope.standardQuestionList = response.data.standard_questions;
        });
    }
    $scope.showSameLocationReviewsData = function(){
        $scope.locationMode = 0;
        locationMode = $scope.locationMode;
        hrmAPIservice.getAllFormReviewsForView(userData, showMode, ownMode, locationMode).then(function(response) {
            console.log(response.data);
            $scope.gridOptionsComplex.data = response.data.form_reviews.map(function(review){
                var scores = review.scores.split(',');
                var sum = 0;
                for(var i=0; i<scores.length; i++)
                    if(scores[i]!="")
                        sum+=parseInt(scores[i]);
                console.log(scores);
                return{
                    id: review.id,
                    p_forms_id: review.p_forms_id,
                    questions: review.questions,
                    scores: review.scores,
                    comments: review.comments,
                    person_name: review.person_name,
                    manager_name: review.manager_name,
                    date_reviewed: $scope.formatDate(review.completed_date),
                    assessment_date: $scope.formatDate(review.assessment_date), 
                    site_location: review.site_location,
                    completed_date: $scope.formatDate(review.completed_date),
                    score: sum
                }
            });
            $scope.standardQuestionList = response.data.standard_questions;
        });
    }
    $scope.showAllLocationsReviewsData = function(){
        $scope.locationMode = 1;
        locationMode = $scope.locationMode;
        hrmAPIservice.getAllFormReviewsForView(userData, showMode, ownMode, locationMode).then(function(response) {
            console.log(response.data);
            $scope.gridOptionsComplex.data = response.data.form_reviews.map(function(review){
                var scores = review.scores.split(',');
                var sum = 0;
                for(var i=0; i<scores.length; i++)
                    if(scores[i]!="")
                        sum+=parseInt(scores[i]);
                console.log(scores);
                return{
                    id: review.id,
                    p_forms_id: review.p_forms_id,
                    questions: review.questions,
                    scores: review.scores,
                    comments: review.comments,
                    person_name: review.person_name,
                    manager_name: review.manager_name,
                    date_reviewed: $scope.formatDate(review.completed_date),
                    assessment_date: $scope.formatDate(review.assessment_date), 
                    site_location: review.site_location,
                    completed_date: $scope.formatDate(review.completed_date),
                    score: sum
                }
            });
            $scope.standardQuestionList = response.data.standard_questions;
        });
    }
    $scope.editForm = function(obj) {
        $scope.formEnabled = 1;
        $scope.specializedQuestionList = [];
        $scope.scoreList = [];
        $scope.commentList = [];
        $scope.questions = obj.questions.split("~#");
        $scope.selected_employee_name = obj.person_name;
        angular.forEach($scope.questions, function (value, key) {
            if(key < $scope.questions.length - 1) $scope.specializedQuestionList.push({id : key + 1, question_text : value});
        });  
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

    hrmAPIservice.getAllFormReviewsForView(userData, showMode, ownMode, locationMode).then(function(response) {
        console.log(response.data);
        $scope.gridOptionsComplex.data = response.data.form_reviews.map(function(review){
            var scores = review.scores.split(',');
            var sum = 0;
            for(var i=0; i<scores.length; i++)
                if(scores[i]!="")
                    sum+=parseInt(scores[i]);
            console.log(scores);
            return{
                id: review.id,
                p_forms_id: review.p_forms_id,
                questions: review.questions,
                scores: review.scores,
                comments: review.comments,
                person_name: review.person_name,
                manager_name: review.manager_name,
                date_reviewed: $scope.formatDate(review.completed_date),
                assessment_date: $scope.formatDate(review.assessment_date), 
                site_location: review.site_location,
                completed_date: $scope.formatDate(review.completed_date),
                score: sum
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
