// updated by Alex-cobra variable Upload -20-04-06
app.filter('trustHtml',function($sce){
  return function(html){
    return $sce.trustAsHtml(html)
  }
});
app.controller("coursedetailController", ["$scope", "$rootScope", "cookie", "hrmAPIservice", "$routeParams", "$location", "Upload",  /*"pdfjsViewer",*/  function($scope, $rootScope, cookie, hrmAPIservice, $routeParams, $location, Upload) {
    var userData = cookie.checkLoggedIn();
    var courseCategory = [];
    cookie.getPermissions();
    $scope.isChangedFileName = false;
    $scope.fileNameChanged = function() {
        $scope.isChangedFileName = true;
    };
    $scope.isAllowed = false;
    var perm = cookie.getCookie("permissions");
    if (!perm || perm['13'] == null || perm['13']==undefined)      $scope.isAllowed = false;
    else {
        if (perm['13'].r == '1') $scope.isAllowed = true;
        else                     $scope.isAllowed = false;
    }
    if(!$scope.isAllowed){
        cookie.deleteCookie('user');
        cookie.deleteCookie('permissions');
        $rootScope.isLoggedin = 0;
        $location.path('/');
    }

    $scope.course_id = (angular.isUndefined($routeParams.id)) ? 0 : $routeParams.id;

    $scope.pageTitle = "";
    $scope.course = {
        course_id: 0,
        course_type: 'Multiple Choice',
        status: 1,
        user_id: userData.id,
        time_limit: 0,
        is_randomized: 0,
        //start updated by Alex-cobra-2020-04-17
        is_locked: 0,
        //end updated by Alex-cobra-2020-04-17
        display_error_message: 0,
        reorder: 0,
        is_comeback: 0,
        try_again: 0,
        is_global: 0,
        correct_only: 1,
        question_count: 1,
        is_auto_inactive: 0,
        go_back: 0,
        questions: {}
    };

    $scope.showSave = 0;
    $scope.selectedTab = 0;
    $scope.submitInProgress = 0;

    $scope.isAdmin = (userData['usertype_id'] == 17) ? true : false;
    $scope.course_save_message = '';

    var questionMax = 30;
    var answerMax = 5;

    // cobra
    var selected_file = null;
    $scope.showModal = false;

    $scope.questionNumbers = [];
    $scope.answerNumbers = [];

    $scope.status = {
        isCustomHeaderOpen: false,
        isFirstOpen: true,
        isFirstDisabled: false
    };

    // Setting question and answer numbers.
    for(var i = 0; i < questionMax; i++) {
        $scope.questionNumbers[i] = i + 1;
    }

    for(var i = 0; i < answerMax; i++) {
        $scope.answerNumbers[i] = i + 1;
    }

    // Get Course Category List.
    hrmAPIservice.getCourseCategory().then(function(response) {
        response.data.course_category.forEach(function(element, index){
            var temp = '{ "value" : "' + response.data.course_category[index].course_category_name + '", "label" : "' + response.data.course_category[index].course_category_name + '", "id" : "' + response.data.course_category[index].course_category_id + '" }' ;
            var temp_1 = '{ "value" : "' + response.data.course_category[index].course_category_id + '", "label" : "' + response.data.course_category[index].course_category_name + '" }';
            courseCategory.push(JSON.parse(temp));
        });
        $scope.course_category = courseCategory;

        // Set the first value.
        if($scope.course_category.length > 0) {
            $scope.course.course_category_id = $scope.course_category[0]['id'];
        }
    });

    // updated by Alex-cobra -20-04-03
    $scope.addAnswerStyleChange = function() {
        if($scope.course.course_type == "Questions And Answers") {
            $scope.course.addAnswerDivStyle = {display: 'none'};
        }
        else {
            $scope.course.addAnswerDivStyle = {display: 'block'};
        }
    }
    // // // // //

    $scope.addQuestion = function(index) {
        if (angular.isUndefined($scope.course.questions)) {
            $scope.course.questions = [];
        } else {
            if ($scope.course.questions == null) {
                $scope.course.questions = [];
            }
        }

        if (!angular.isArray($scope.course.questions)) {
            $scope.course.questions = [];
        }

        var i = $scope.course.questions.length;
        $scope.course.questions.push({index: i, name: "Question " + (i + 1), media_type: 99});
    }

    // Update Question Numbers of Course.
    $scope.updateQuestionNumber = function() {

        if (angular.isUndefined($scope.course.questions) || $scope.course.questions == null) {
            $scope.course.questions = [];
        }

        if($scope.course.questions.length == 0) {
            $scope.course.questions = [];
            for(var i = 0; i < $scope.course.question_count; i++) {
                $scope.course.questions[i] = {"index": i, "name": "Question " + (i + 1)};
            }
        } else {
            if($scope.course.questions.length > $scope.course.question_count) {
                var removeCount = $scope.course.questions.length - $scope.course.question_count;
                $scope.course.questions.splice($scope.course.question_count, removeCount);
            } else {
                for(var i = 0; i < $scope.course.question_count; i++) {
                    $scope.course.questions[i] = {"index": i, "name": "Question " + (i + 1)};
                }
            }
        }
    }

    $scope.addAnswer = function(index) {
        if (angular.isUndefined($scope.course.questions[index].answers)) {
            $scope.course.questions[index].answers = [];
        } else {
            if ($scope.course.questions[index].answers == null) {
                $scope.course.questions[index].answers = [];
            }
        }

        var i = $scope.course.questions[index].answers.length;
        if (i == 0) {
            $scope.course.questions[index].answers.push({index: i, title: '', correct_answer_index: 0});
            $scope.course.questions[index].correct_answer_index = 0;
        } else {
            $scope.course.questions[index].answers.push({index: i, title: ''});
        }

    }

    $scope.initQuestion = function(){
        $scope.fileuploadMessage = '';
    }

    // updated by Alex-cobra from LiYin -20-04-06
    $scope.progress = {image:0, video:0, pdf:0};


    $scope.uploadImage = function(index) {

        $scope.imageUploadMessage = 'Uploading, please wait till completed..';

        var isFile = (angular.isDefined($('#imagemedia' + index)[0].files[0])) ? true : false;
        if (!isFile) {
            $scope.imageUploadMessage = '<span class="error">No file selected..</span>';
            return;
        }

        Upload.upload({
            url: 'course/savefile',
            data: {'fileToUpload': $scope.course.questions[index].image, 'type':'image'}
        }).then(function (resp) {
            $scope.imageUploadMessage = '<span class="success">File uploaded successfully!</span>';
            $scope.course.questions[index].image = resp.data.filename;
            $scope.course.questions[index].image_href = resp.data.href;
            $scope.progress.image = 0;
            $scope.isChangedFileName = false;
        }, function (resp) {
            // console.log(resp);
            $scope.imageUploadMessage = '<span class="error">File was not able to be uploaded.</span>';
            $scope.progress.image = 0;
            $scope.isChangedFileName = false;
        }, function (evt) {
            $scope.progress.image = parseInt(100.0 * evt.loaded / evt.total);
        });

    }

    $scope.uploadVideo = function(index) {
        $scope.videoUploadMessage = 'Uploading, please wait till completed..';
        var formData = new FormData();
        var isFile = (angular.isDefined($('#videomedia' + index)[0].files[0])) ? true : false;
        if (!isFile) {
            $scope.videoUploadMessage = '<span class="error">No file selected..</span>';
            return;
        }

        Upload.upload({
            url: 'course/savefile',
            data: {'fileToUpload': $scope.course.questions[index].video, 'type':'video'}
        }).then(function (resp) {
            $scope.videoUploadMessage = '<span class="success">File uploaded successfully!</span>';
            $scope.course.questions[index].video = resp.data.filename;
            $scope.course.questions[index].video_href = resp.data.href;
            $scope.progress.video = 0;
            $scope.isChangedFileName = false;
        }, function (resp) {
            // console.log(resp);
            $scope.videoUploadMessage = '<span class="error">File was not able to be uploaded.</span>';
            $scope.progress.video = 0;
            $scope.isChangedFileName = false;
        }, function (evt) {
            // console.log(evt);
            $scope.progress.video = parseInt(100.0 * evt.loaded / evt.total);
        });

    }

    $scope.uploadPdf = function(index) {
        $scope.pdfUploadMessage = 'Uploading, please wait till completed..';
        var formData = new FormData();
        var isFile = (angular.isDefined($('#pdfmedia' + index)[0].files[0])) ? true : false;
        if (!isFile) {
            $scope.pdfUploadMessage = '<span class="error">No file selected..</span>';
            return;
        }

        Upload.upload({
            url: 'course/savefile',
            data: {'fileToUpload': $scope.course.questions[index].pdf, 'type':'pdf'}
        }).then(function (resp) {
            $scope.pdfUploadMessage = '<span class="success">File uploaded successfully!</span>';
            $scope.course.questions[index].pdf = resp.data.filename;
            $scope.course.questions[index].pdf_href = resp.data.href;
            $scope.progress.pdf = 0;
            $scope.isChangedFileName = false;
        }, function (resp) {
            // console.log(resp);
            $scope.pdfUploadMessage = '<span class="error">File was not able to be uploaded.</span>';
            $scope.progress.pdf = 0;
            $scope.isChangedFileName = false;
        }, function (evt) {
            $scope.progress.pdf = parseInt(100.0 * evt.loaded / evt.total);
        });
    }

    $scope.removeFiles = function() {
      $scope.showModal = false;

      if(selected_file != null) {
        hrmAPIservice.removeFile($scope.course.questions, selected_file, userData).then(function(response) {
            $scope.course.questions[selected_file].pdf = '';
            $scope.course.questions[selected_file].pdf_href = '';
            $scope.course.questions[selected_file].video = '';
            $scope.course.questions[selected_file].video_href = '';
            $scope.course.questions[selected_file].image = '';
            $scope.course.questions[selected_file].image_href = '';
            $scope.course.questions[selected_file].media_type = 99;

            $scope.imageUploadMessage = '';
            $scope.pdfUploadMessage = '';
            $scope.videoUploadMessage = '';
            hrmAPIservice.saveCourse($scope.course, userData).then(function(response) {
            });
        });
      }
    }

    $scope.removeMediaFile = function(index) {
        hrmAPIservice.checkCourseIfAllocated($scope.course, userData).then(function(response) {
            console.log(response.data.result);
            if(response.data.result){
                $scope.showErrorModal = true;
            }else{
                $scope.showModal = true;
                selected_file = index;
            }
        });
    }
    // // // // //

    $scope.uploadFilex = function(type, fid) {
        $scope.fileuploadMessage = 'Uploading, please wait till completed.';
        var fld = type + fid;
        var formData = new FormData();
        var isFile = (angular.isDefined($('#' + fld)[0].files[0])) ? true : false;
        if (!isFile) {
            $scope.fileuploadMessage = 'No file selected..';
            return;
        }

        formData.append('fileToUpload', $('#' + fld)[0].files[0]);
        formData.append('type', type);
        var message = '';
        $.ajax({
               url : 'course/addCoursefile',
               type : 'POST',
               data : formData,
               processData: false,  // tell jQuery not to process the data
               contentType: false,  // tell jQuery not to set contentType
               dataType: 'json',
               async: false,
               success : function(data) {
                    if (data.filename) {
                        message = 'Done!';
                        $("#uploadedfile_" + fid).val(data.filename);
                    } else {
                        message = 'File was not able to be uploaded';
                    }
               },
               complete: function(data) {
                   //$scope.fileuploadMessage = 'File upload successful';
               }
        });

        $scope.fileuploadMessage = message;
    }

    // updated by Alex-cobra -20-04-11
    // Save Course.
    $scope.saveCourse = function() {
        if($scope.course.is_auto_inactive == 1){
            $scope.course.auto_inactive_time = $scope.auto_inactive_time;
        }
        console.log($scope.course);
        hrmAPIservice.saveCourse($scope.course, userData).then(function(response) {
            if (response.data.success == 1) {
                $location.path("/trainingcourses");
            }
        });
    };


    // // // // //

    $scope.cancel = function(){
        location.href = "#/trainingcourses";
    }

    $scope.cancel_remove = function(){
      $scope.showModal = false;
  }

    $scope.cancel_error = function(){
      $scope.showErrorModal = false;
  }
    $scope.nextTab = function() {
        $scope.selectedTab++;
        if ($scope.selectedTab > 1) {
            $scope.showSave = 1;
        }
    }

    if ($scope.course_id > 0) {
        $scope.course = {};
        $scope.course.questions = [];
        hrmAPIservice.getCourseDetail($scope.course_id, userData).then(function(response) {
            console.log(response.data);
            if(!response.data.detail){
                location.href="#/trainingcourses";
                return;
            }
            $scope.course = response.data.detail;
            if($scope.course.is_auto_inactive == 1)
                $scope.auto_inactive_time = new Date($scope.course.auto_inactive_time);
            // updated by Alex-cobra -20-04-03
            if(response.data.detail.course_type == "Questions And Answers") {
                $scope.course.addAnswerDivStyle = {display: 'none'};
            }
            else {
                $scope.course.addAnswerDivStyle = {display: 'block'};
            }
            // // // // //

            $scope.course.questions = [];
            $scope.course.questions = angular.copy(response.data.questions);
            $scope.course.question_count = $scope.course.questions.length;
            
            hrmAPIservice.getInfoTextByType(userData, "course_detail").then(function(response){
                console.log(response);
                $scope.info_course_name = response.data.result.course_name;
                $scope.info_course_description = response.data.result.course_description;
                $scope.info_course_category = response.data.result.course_category;
                $scope.info_course_type = response.data.result.course_type;
                $scope.info_active_course = response.data.result.active_course;
                $scope.options_timelimit_15 = response.data.result.options_timelimit_15;
                $scope.options_timelimit_60 = response.data.result.options_timelimit_60;
                $scope.options_timelimit_300 = response.data.result.options_timelimit_300;
                $scope.options_timelimit_600 = response.data.result.options_timelimit_600;
                $scope.options_timelimit = response.data.result.options_timelimit;
                $scope.options_is_randomized = response.data.result.options_is_randomized;
                $scope.options_is_locked = response.data.result.options_is_locked;
                $scope.options_display_error_messages = response.data.result.options_display_error_messages;
                $scope.options_reorder = response.data.result.options_reorder;
                $scope.options_correct_only = response.data.result.options_correct_only;
                $scope.options_is_comeback = response.data.result.options_is_comeback;
                $scope.options_try_again = response.data.result.options_try_again;
                $scope.options_is_inactive = response.data.result.options_is_inactive;
                $scope.options_go_back = response.data.result.options_go_back;
            });
        });
    }

    // Alex-cobra -20-05-03
    $scope.getPdfSrc = function (pdffile) {
      return 'assets/templates/pdfviewer.html?file=' + pdffile;
    }
    $scope.onTimeoutChanged = function(){
        console.log($scope.course.time_limit);
        if($scope.course.time_limit>0) $scope.course.correct_only=0;
        else $scope.course.correct_only=1;
    }
    $scope.onDisplayErrorMessageOptionChanged = function(){
        if($scope.course.display_error_message == 1) $scope.course.correct_only = 0;
    }
    $scope.onCorrectOnlyOptionChanged = function(){
        if($scope.course.correct_only == 1) $scope.course.display_error_message = 0;
    }
    $scope.onReorderOptionChanged = function(){
        if($scope.course.reorder == 1) $scope.course.go_back = 0;
    }
    $scope.onGoBackOptionChanged = function(){
        if($scope.course.go_back == 1) $scope.course.reorder = 0;
    }
}]);
