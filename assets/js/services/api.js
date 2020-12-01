'use strict';
app.service("hrmAPIservice", function ($http, cookie) {
        var hrmAPI = {};
        return hrmAPI.doLogin = function (usr, pw) {
            return $http({
                method: "POST",
                data: {
                    username: usr,
                    password: pw
                },
                url: "auth/login"
            })
        },
        hrmAPI.send = function (param) {
            return cookie.resetCookie(),
            $http({method: "GET", data: {}, url: param})
        },
        hrmAPI.forgotPassword = function (email) {
            return $http({
                method: "POST",
                data: {
                    email: email
                },
                url: "auth/forgotpassword"
            })
        },
        hrmAPI.resetPassword = function (u, p) {
            return $http({
                method: "POST",
                data: {
                    username: u,
                    password: p
                },
                url: "auth/resetpassword"
            })
        },
        hrmAPI.getEmailFromHash = function (hash) {
            return $http({
                method: "POST",
                data: {
                    hash: hash
                },
                url: "auth/getemailfromhash"
            })
        },
        hrmAPI.getPermissionData = function () {
            return cookie.resetCookie(),
            $http({method: "POST", data: {}, url: "auth/getpermissiondata"})
        },
        hrmAPI.savePermissions = function (role, modules) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    role: role,
                    modules: modules
                },
                url: "auth/savepermissions"
            })
        },
        hrmAPI.getPermissions = function (role) {
            return cookie.resetCookie(),
            $http({
                method: "GET",
                data: {},
                url: "auth/getpermissions/" + role
            })
        },
        hrmAPI.getRoles = function () {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    action: "getRoles"
                },
                url: "assets/php/ajax.php"
            })
        },
        hrmAPI.sendEmailToAttendants = function(userData, event_id, email_attendees) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    currUser: userData,
                    event_id: event_id,
                    email_attendees: email_attendees
                },
                url: "event/sendEmailToAttendants"
            })
        },
        hrmAPI.getLQDetailInfo = function(LQName) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    LQName: LQName,
                },
                url: "event/getLQDetailInfo"
            })
        },
        hrmAPI.getEventNames = function (userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    currUser: userData,
                },
                url: "event/getEventNames"
            })
        },
        hrmAPI.getAllocatedEvents = function (userData, selected_emp, showMode) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    userData: userData,
                    selected_emp: selected_emp,
                    showMode: showMode
                },
                url: "event/getAllocatedEvents"
            })
        },
        hrmAPI.getEventTypes = function () {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    action: "getLicenseTypes"
                },
                url: "assets/php/ajax.php"
            })
        },
        hrmAPI.getStates = function () {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    action: "getStates"
                },
                url: "assets/php/ajax.php"
            })
        },
        hrmAPI.getEmployeeData = function (userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    currUser: userData
                },
                url: "employee/getdata"
            })
        },
        //newly added
        hrmAPI.getPerformanceForms = function (account_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                   account_id: account_id
                },
                url: "admin/getPerformanceForms"
            })
        },
        //newly added
        hrmAPI.deleteReminder = function (reminder) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                   reminder: reminder
                },
                url: "reminders/deleteReminder"
            })
        },
        //newly added
        hrmAPI.startAlert = function (id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                   id:id
                },
                url: "reminders/startAlert"
            })
        },
        //newly added
        hrmAPI.stopAlert = function (id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                   id:id
                },
                url: "reminders/stopAlert"
            })
        },
        //newly added
        hrmAPI.getAllReminders = function (account_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                   account_id: account_id
                },
                url: "reminders/getAllReminders"
            })
        },
        //newly added
        hrmAPI.emailUpdate = function (id, email_con, days_prior) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    id: id,
                    email_con: email_con,
                    days_prior: days_prior
                },
                url: "reminders/emailUpdate"
            })
        },
        //newly added
        hrmAPI.updateReminder = function (cloned_reminder) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    cloned_reminder: cloned_reminder
                },
                url: "reminders/updateReminder"
            })
        },
        //newly added
        hrmAPI.saveReminder = function (cloned_reminder) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    cloned_reminder: cloned_reminder
                },
                url: "reminders/saveReminder"
            })
        },
        //newly added
        hrmAPI.getEmailNames = function () {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {

                },
                url: "reminders/getEmailNames"
            })
        },
        //newly added
        hrmAPI.getPosition = function (id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    emp_id: id,
                },
                url: "reminders/getPosition"
            })
        },
        //newly added
        hrmAPI.getOnlyEmployeeList = function (userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    userData: userData,
                },
                url: "employee/getemployeelist"
            })
        },
        //newly added
        hrmAPI.getLogHistory = function (userData, employee_id) {
            //console.log(userData);
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    userData: userData,
                    employee_id: employee_id
                },
                url: "employee/getloghistory"
            })
        },
        //newly added
        hrmAPI.getLQ = function (userData, employee_id) {
            //console.log(userData);
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    userData: userData,
                    employee_id: employee_id
                },
                url: "employee/getLQ"
            })
        },
        //newly added
        hrmAPI.removeLog = function (log_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    log_id: log_id,
                },
                url: "employee/removeLog"
            })
        },
        //newly added
        hrmAPI.removeLQ = function (log_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    log_id: log_id,
                },
                url: "employee/removeLQ"
            })
        },
        //newly added
        hrmAPI.removeConf = function (log_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    log_id: log_id,
                },
                url: "employee/removeConf"
            })
        },
        //newly added
        hrmAPI.updateFlag = function (log_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    log_id: log_id,
                },
                url: "employee/updateFlag"
            })
        },
         //newly added
         hrmAPI.saveForm = function (form, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    form: form,
                    userData: userData
                },
                url: "admin/saveForm"
            })
        },
         //newly added
         hrmAPI.deleteForm = function (form, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    form: form,
                    userData: userData
                },
                url: "admin/deleteForm"
            })
        },
        //newly added
        hrmAPI.updateForm = function (form, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    form: form,
                    userData: userData
                },
                url: "admin/updateForm"
            })
        },
        //newly added
        hrmAPI.getFormReviews = function (userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    userData: userData
                },
                url: "performance/getFormReviews"
            })
        },
        //newly added
        hrmAPI.saveFormReview = function (scores, comments, id, p_form_id,  userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    scores: scores,
                    comments: comments,
                    id: id,
                    p_form_id: p_form_id,
                    userData: userData
                },
                url: "performance/saveFormReview"
            })
        },
        //newly added
        hrmAPI.deleteFormReview = function (fdetail, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    id: fdetail.id,
                    userData: userData
                },
                url: "performance/deleteFormReview"
            })
        },

        //newly added
        hrmAPI.getScoresByYear = function (year, userId) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    year: year,
                    userId: userId
                },
                url: "report/getScoresByYear"
            })
        },
        //newly added
        hrmAPI.getScoresByPosition = function (year, account_id, user_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    year: year,
                    account_id: account_id,
                    user_id: user_id
                },
                url: "report/getScoresByPosition"
            })
        },
        //newly added
        hrmAPI.getScoresBySitelocation = function (year, account_id, user_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    year: year,
                    account_id: account_id,
                    user_id: user_id
                },
                url: "report/getScoresBySitelocation"
            })
        },
        //newly added
        hrmAPI.getFormReviewsForView = function (userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    userData: userData
                },
                url: "profile/getFormReviewsForView"
            })
        },
        hrmAPI.getAllFormReviewsForView = function (userData, showMode, ownMode, locationMode) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    userData: userData,
                    showMode: showMode,
                    ownMode: ownMode,
                    locationMode: locationMode
                },
                url: "performance/getAllFormReviewsForView"
            })
        },
        hrmAPI.getUserData = function (userData, isAdmin) {
            return cookie.resetCookie(),
            isAdmin = angular.isDefined(isAdmin)
                ? isAdmin
                : 0,
            $http({
                method: "POST",
                data: {
                    currUser: userData,
                    admin: isAdmin
                },
                url: "user/getdata"
            })
        },
        hrmAPI.getScheduleByID = function(userData, allocatedEvent_id){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    currUser: userData,
                    allocatedEvent_id: allocatedEvent_id
                },
                url: "event/getScheduleByID"
            })
        },
        hrmAPI.getEvents = function (userData, isAdmin) {
            return cookie.resetCookie(),
            isAdmin = angular.isDefined(isAdmin)
                ? isAdmin
                : 0,
            $http({
                method: "POST",
                data: {
                    currUser: userData,
                    admin: isAdmin
                },
                url: "event/getlist"
            })
        },
        hrmAPI.deleteAllocatedEvent = function(userData, del_id){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    currUser: userData,
                    del_id: del_id
                },
                url: "event/deleteAllocatedEvent"
            })
        },
        hrmAPI.saveAllocatedEvent = function (userData, alloc_event) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    currUser: userData,
                    alloc_event: alloc_event
                },
                url: "event/saveAllocatedEvent"
            })
        },
        hrmAPI.getUsers = function (userData, isAdmin) {
            return cookie.resetCookie(),
            isAdmin = angular.isDefined(isAdmin)
                ? isAdmin
                : 0,
            $http({
                method: "POST",
                data: {
                    currUser: userData,
                    admin: isAdmin
                },
                url: "user/getlist"
            })
        },

        hrmAPI.getActiveUsers = function (userData, isAdmin) {
            return cookie.resetCookie(),
            isAdmin = angular.isDefined(isAdmin)
                ? isAdmin
                : 0,
            $http({
                method: "POST",
                data: {
                    currUser: userData,
                    admin: isAdmin
                },
                url: "user/getactivelist"
            })
        },

        hrmAPI.getUserGlobalData = function () {
            return cookie.resetCookie(),
            $http({method: "POST", url: "user/getglobaldata"})
        },

        hrmAPI.getUserById = function (user_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_id: user_id
                },
                url: "user/getuser"
            })
        },

        hrmAPI.getCoursesByUser = function (userId) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    userId: userId
                },
                url: "course/getcoursesbyuser"
            })
        },
        hrmAPI.getSchedulerByUser = function (userData, showMode, locationMode) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    userData: userData,
                    showMode: showMode,
                    locationMode: locationMode
                },
                url: "event/getSchedulerByUser"
            })
        },

        hrmAPI.searchUser = function (keyword, userData, usertype) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    keyword: keyword,
                    userData: userData,
                    usertype: usertype
                },
                url: "user/search"
            })
        },

        hrmAPI.searchEmployee = function (keyword, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    keyword: keyword,
                    userData: userData
                },
                url: "employee/search"
            })
        },

        hrmAPI.searchEmployeeUser = function (keyword, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    keyword: keyword,
                    userData: userData
                },
                url: "employeeuser/search"
            })
        },

        hrmAPI.searchData = function (keyword, type, id) {
            if (typeof id === 'undefined') {
                id = 0;
            }
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    keyword: keyword,
                    type: type,
                    account_id: id
                },
                url: "data/search"
            })
        },
        //newly added for upload file
        hrmAPI.uploadFileToUrl = function (file, uploadUrl){
            var fileFormData = new FormData();
            fileFormData.append('file', file);
            return cookie.resetCookie(),
            $http.post(uploadUrl, fileFormData, {
                transformRequest: angular.identity,
                headers: {'Content-Type' : undefined, 'Process-Data' : false}
            }).then(function(res){
                console.log(res);
                return res.data;
            })
        },
        //newly added
        hrmAPI.saveEmployeeNotes = function(employee_notes){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    action: "saveEmployeeNotes",
                    emp_notes: employee_notes,
                },
                url: "employee/saveEmployeeNotes"
            })
        },
        hrmAPI.getSortedEmployeeNotes = function(sort_key, reverse, empId, userData){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    sort_key: sort_key,
                    reverse: reverse,
                    employee_id: empId,
                    userData: userData
                },
                url: "employee/getSortedEmployeeNotes"
            })
        },
        
        //newly added
        hrmAPI.saveLQ = function(lq){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    action: "saveLQ",
                    lq: lq,
                },
                url: "employee/saveLQ"
            })
        },
        //newly added
        hrmAPI.updateEmployeeNotes = function(employee_notes, employee_notes_id){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    action: "updateEmployeeNotes",
                    emp_notes: employee_notes,
                    id: employee_notes_id
                },
                url: "employee/updateEmployeeNotes"
            })
        },
        //newly added
        hrmAPI.updateLQ = function(lq, employee_notes_id){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    action: "updateLQ",
                    lq: lq,
                    id: employee_notes_id
                },
                url: "employee/updateLQ"
            })
        },
        //newly added
        hrmAPI.getSpecificReminder = function(id){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    id: id,

                },
                url: "reminders/getSpecificReminder"
            })
        },
        //newly added
        hrmAPI.getSystemLogs = function(account_id){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    action: "getSystemLogs",
                    account_id: account_id
                },
                url: "user/systemlogs"
            })
        },
        hrmAPI.saveEmployee = function (emp, empwork, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    action: "saveEmployee",
                    emp: emp,
                    empwork: empwork,
                    currUser: userData
                },
                url: "employee/save"
            })
        },
        hrmAPI.delete = function (detail, userData, type) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    typeDetail: detail,
                    currUser: userData,
                    type: type
                },
                url: "delete"
            })
        },
        hrmAPI.get = function (id, type) {
            return cookie.resetCookie(),
            $http({
                method: "GET",
                data: {},
                url: "get/" + type + "/" + id
            })
        },
        hrmAPI.getUserLoginDetail = function (id, type) {
            return cookie.resetCookie(),
            $http({
                method: "GET",
                data: {},
                url: "user/getlogindetail/" + id
            })
        },
        hrmAPI.saveUser = function (user, userData, newaccount) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user: user,
                    userData: userData,
                    newaccount: newaccount
                },
                url: "user/save"
            })
        },
        hrmAPI.saveEvent = function (event, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    event: event,
                    userData: userData,
                },
                url: "event/save"
            })
        },
        hrmAPI.getEventById = function (event_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    event_id: event_id
                },
                url: "event/getevent"
            })
        },
        hrmAPI.saveChildUser = function (user, userData, newaccount) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user: user,
                    userData: userData
                },
                url: "user/save_child_user"
            })
        },

        hrmAPI.updateUser = function (user, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user: user,
                    userData: userData
                },
                url: "user/update"
            })
        },
        hrmAPI.getUserData = function () {
            return cookie.resetCookie(),
            $http({method: "POST", data: {}, url: "user/getdata"})
        },
        hrmAPI.getEmpList = function(userData, showMode){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    userData: userData,
                    showMode: showMode
                },
                url: "user/getEmpList"
            })
        },
        hrmAPI.getEmployeeList = function (user_id, usertype) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_id: user_id,
                    usertype: usertype
                },
                url: "user/get_employee_list"
            })
        },
        hrmAPI.releaseLock = function (userId) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    userId: userId
                },
                url: "user/releaselock"
            })
        },
        hrmAPI.activateEmployee = function (employeeId, status) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    action: "activateEmployee",
                    employeeId: employeeId,
                    status: status
                },
                url: "assets/php/ajax.php"
            })
        },

        hrmAPI.activateUser = function (userId, status) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    userId: userId,
                    status: status
                },
                url: "user/activateuser"
            })
        },
        hrmAPI.activateCourse = function (courseId, status) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    courseId: courseId,
                    status: status
                },
                url: "course/activatecourse"
            })
        },

        hrmAPI.getCourseData = function (user_id, incStatus) {
            var params = {};
            params.user_id = user_id;
            if (angular.isDefined(incStatus)) {
                params.activeOnly = 1;
            }
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: params,
                url: "course/getdata"
            })
        },

        hrmAPI.getCourse = function (course_id, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    currUser: userData,
                    course_id: course_id
                },
                url: "course/getCourseSingle"
            })
        },
        hrmAPI.getCourseById = function (course_id, employee_id) {
            let userData = cookie.checkLoggedIn();
            let id = userData?userData.id:null;
            return cookie.resetCookie(),
             $http({
                method: "POST",
                data: {
                    user_id: id,
                    currUser: 'userData',
                    course_id: course_id,
                    employee_id: employee_id
                },
                url: "course/getCourse"
            })
        },
        
        hrmAPI.getIncompleteCourseAnswers = function (course_id, employee_id){
            let userData = cookie.checkLoggedIn();
            let id = userData?userData.id:null;
            return cookie.resetCookie(),
             $http({
                method: "POST",
                data: {
                    user_id: id,
                    currUser: 'userData',
                    course_id: course_id,
                    employee_id: employee_id
                },
                url: "course/getIncompleteCourseAnswers"
            })
        },
        hrmAPI.getEmployeeCourseLastAttempt = function (course_id, employee_id) {
            let userData = cookie.checkLoggedIn();
            let id = userData?userData.id:null;
            return cookie.resetCookie(),
             $http({
                method: "POST",
                data: {
                    user_id: id,
                    currUser: 'userData',
                    course_id: course_id,
                    employee_id: employee_id
                },
                url: "course/getEmployeeCourseLastAttempt"
            })
        },
        hrmAPI.getCourseCategory = function () {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    currUser: 'userData'
                },
                url: "course/getCate"
            })
        },

        // updated by Alex-cobra -20-04-11
        /*
        hrmAPI.saveCourse = function (courseData, userData) { // update course
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    courseData: courseData,
                    currUser: userData
                },
                url: "course/saveCourse"
            })
        },
        */
        // // // // //
        hrmAPI.addCourse = function (courseAddData) { // add course
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    courseData: courseAddData
                },
                url: "course/addCourse"
            })
        },
        hrmAPI.delCourse = function (course_id, user_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    course_id: course_id,
                    user_id: user_id
                },
                url: "course/delCourse"
            })
        },
        hrmAPI.searchCourse = function (keyword) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    keyword: keyword
                },
                url: "course/searchCourse"
            })
        },

        // updated by Alex-cobra -20-04-11
        hrmAPI.allocCourse = function (allocCourseData, user_data) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    allocCourseData: allocCourseData,
                    userData: user_data
                },
                url: "course/allocCourse"
            })
        },
        // // // // //

        hrmAPI.getAllocCourseData = function (user_data, showMode, ownMode, locationMode) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_data: user_data,
                    showMode: showMode,
                    ownMode: ownMode,
                    locationMode: locationMode
                },
                url: "course/getAllocCourses"
            })
        },
        hrmAPI.getInjuryReportByDepartment = function(user_data, showMode, filter_params){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_data: user_data,
                    showMode: showMode,
                    filter_params: filter_params
                },
                url: "getInjuryReportByDepartment"
            })
        },
        hrmAPI.getInjuryReportByLocation = function(user_data, showMode, filter_params){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_data: user_data,
                    showMode: showMode,
                    filter_params: filter_params
                },
                url: "getInjuryReportByLocation"
            })
        },
        hrmAPI.getInjuryLostReportByLocation = function(user_data, showMode, filter_params){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_data: user_data,
                    showMode: showMode,
                    filter_params: filter_params
                },
                url: "getInjuryLostReportByLocation"
            })
        },
        hrmAPI.getAnalyticsOfLQTrainingCourses = function(user_data, showMode, status, filter_params){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_data: user_data,
                    showMode: showMode,
                    course_status: status,
                    filter_params: filter_params
                },
                url: "training_course/getAnalyticsOfLQTrainingCourses"
            })
        },
        hrmAPI.getAnalyticsOfLQTrainingCourseCost = function(user_data, showMode, filter_params){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_data: user_data,
                    showMode: showMode,
                    filter_params: filter_params
                },
                url: "training_course/getAnalyticsOfLQTrainingCourseCost"
            })
        },
        hrmAPI.getAnalyticsOfTrainingCourses = function(user_data, showMode, status, filter_params){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_data: user_data,
                    showMode: showMode,
                    course_status: status,
                    filter_params: filter_params
                },
                url: "course/getAnalyticsOfTrainingCourses"
            })
        },
        hrmAPI.getAnalyticsOfTrainingEmployees = function(user_data, showMode, status, filter_params){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_data: user_data,
                    showMode: showMode,
                    course_status: status,
                    filter_params: filter_params
                },
                url: "course/getAnalyticsOfTrainingEmployees"
            })
        },
        hrmAPI.getAnalyticsOfPerformanceReviews = function(user_data, showMode, status, filter_params){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_data: user_data,
                    showMode: showMode,
                    review_status: status,
                    filter_params: filter_params
                },
                url: "performance/getAnalyticsOfPerformanceReviews"
            })
        },
        hrmAPI.getAnalyticsOfPerformanceEmployees = function(user_data, showMode, status, filter_params){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_data: user_data,
                    showMode: showMode,
                    review_status: status,
                    filter_params: filter_params
                },
                url: "performance/getAnalyticsOfPerformanceEmployees"
            })
        },
        hrmAPI.getAnalyticsOfEmployeePerformanceReviews = function(user_data,  filter_params){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_data: user_data,
                    filter_params: filter_params
                },
                url: "performance/getAnalyticsOfEmployeePerformanceReviews"
            })
        },
        hrmAPI.getAnalyticsOfEmployeePerformanceScore = function(user_data,  show_mode, filter_params){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_data: user_data,
                    show_mode: show_mode,
                    filter_params: filter_params
                },
                url: "performance/getAnalyticsOfEmployeePerformanceScore"
            })
        },

        hrmAPI.getInTimeCompletedReviews = function(user_data, showMode, filter_params){
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user_data: user_data,
                    showMode: showMode,
                    filter_params: filter_params
                },
                url: "performance/getInTimeCompletedReviews"
            })
        },

        hrmAPI.delAllocCourse = function (alloc_course_id, user_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    alloc_course_id: alloc_course_id,
                    user_id: user_id
                },
                url: "course/delAllocCourse"
            })
        },
        hrmAPI.getAllocCourseById = function (alloc_course_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    alloc_course_id: alloc_course_id
                },
                url: "course/getAllocCourseById"
            })
        },
        hrmAPI.updateAllocCourse = function (allocCourseData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    allocCourseData: allocCourseData
                },
                url: "course/updateAllocCourse"
            })
        },

        hrmAPI.removeMedia = function (type, qid) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    type: type,
                    questionId: qid
                },
                url: "media/remove"
            })

        },

        hrmAPI.getHSData = function (userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user: userData
                },
                url: "hs/getdata"
            })
        },

        hrmAPI.saveHS = function (hs, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    hs: hs,
                    user: userData
                },
                url: "hs/savehs"
            })
        },

        hrmAPI.getInfoTexts = function (userData, account_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user: userData,
                    account_id: account_id
                },
                url: "infotexts"
            })
        },
        hrmAPI.getInfoTextById = function (id, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    id: id
                },
                url: "getInfoTextById"
            })
        },
        hrmAPI.getInfoTextByType = function (userData, type) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    userData: userData,
                    type: type
                },
                url: "getInfoTextByType"
            })
        },

        hrmAPI.saveInfoText = function (it, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    it: it,
                    userData: userData
                },
                url: "saveinfotexts"
            })
        },

        hrmAPI.deleteInfoText = function (id, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    id: id,
                    userData: userData
                },
                url: "deleteInfoText"
            })
        },

        hrmAPI.getSiteData = function (userData, account_id) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user: userData,
                    account_id: account_id
                },
                url: "get/sitedata"
            })
        },

        hrmAPI.saveData = function (obj, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user: userData,
                    sitedata: obj
                },
                url: "site/savedata"
            })
        },

        hrmAPI.submitAnswer = function (courseId, employeeId, questionId, answerId, ip_address) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    course_id: courseId,
                    employee_id: employeeId,
                    question_id: questionId,
                    answer_id: answerId,
                    ip_address: ip_address
                },
                url: "course/submitanswer"
            })
        },

        // updated by Alex-cobra  submit answer in questions and answers -20-04-03
        // submit of course's Answer in question and answer method
        hrmAPI.submitAnswer1 = function (courseId, employeeId, questionId, answer, ip_address) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    course_id: courseId,
                    employee_id: employeeId,
                    question_id: questionId,
                    answer: answer,
                    ip_address: ip_address
                },
                url: "course/submitanswer1"
            })
        },

        // submit of course's review in question and answer method
        hrmAPI.submitReview = function (courseId, employeeId, questionId, reviewId) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    course_id: courseId,
                    employee_id: employeeId,
                    question_id: questionId,
                    review_id: reviewId
                },
                url: "course/submitreview"
            })
        },

        hrmAPI.getCourseByAllocId = function (allocId) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    alloc_id: allocId
                },
                url: "course/getCourseByAllocId"
            })
        },


        // // // // //

        hrmAPI.getARData = function (userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    user: userData
                },
                url: "assetregister/getdata"
            })
        },

        hrmAPI.saveAR = function (ar, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    ar: ar,
                    user: userData
                },
                url: "assetregister/save"
            })
        },

        hrmAPI.startCourse = function (courseId, employeeId) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    course_id: courseId,
                    employee_id: employeeId,
                    //started_date: started_date
                },
                url: "course/start"
            })
        },

        hrmAPI.getIRData = function (userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    userData: userData
                },
                url: "injuryregister/getdata"
            })
        },
        hrmAPI.getInjury = function (userData) {
            return cookie.resetCookie(),
                $http({
                    method: "POST",
                    data: {
                        userData: userData
                    },
                    url: "injuryregister/getdata"
                })
        },
        hrmAPI.printInjuryHistory = function(emp, filter_params) {
            return cookie.resetCookie(),
                $http({
                    method: 'POST',
                    data: {
                        emp: emp,
                        filter_params: filter_params
                    },
                    url: 'printInjuryHistory'
                })
       },
        hrmAPI.deleteIRData = function (data, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    data: data,
                    userData: userData
                },
                url: "injuryregister/delete"
            })
        },
        hrmAPI.saveIR = function (data, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    data: data,
                    userData: userData
                },
                url: "injuryregister/save"
            })
        },
        hrmAPI.saveCourse = function (course, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    data: course,
                    userData: userData
                },
                url: "course/save"
            })
        },
        hrmAPI.removeFile = function (course, index, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    course: course,
                    index: index,
                    userData: userData
                },
                // url: "question/remove/file"
                url: "course/removefile"
            })
        },

        hrmAPI.checkCourseIfAllocated = function (course, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {
                    course: course,
                    userData: userData
                },
                url: "course/checkCourseIfAllocated"
            })
        },
        
        hrmAPI.getCourseDetail = function (courseId, userData) {
            return cookie.resetCookie(),
            $http({
                method: "POST",
                data: {

                    course_id: courseId,
                    userData: userData
                },
                url: "course/detail/get"
            })
        },

        hrmAPI.getAdminChecklists = function(userData) {
           return $http({
               method: 'POST',
               data: { userData: userData },
               url: 'auditchecklist/get'
           });
        },

        hrmAPI.updateAuditChecklistItem = function(clname, cl, userData) {
           return $http({
               method: 'POST',
               data: { name: clname, checklist: cl, userData: userData },
               url: 'auditchecklist/item/update'
           });
        },

        hrmAPI.updateAuditChecklistName = function(clname, cl, userData) {
           return $http({
               method: 'POST',
               data: { name: clname, checklist: cl, userData: userData },
               url: 'auditchecklist/update'
           });
        },

        hrmAPI.deleteAuditChecklist = function(cl, userData) {
           return $http({
               method: 'POST',
               data: { checklist: cl, userData: userData },
               url: 'auditchecklist/delete'
           });
        },

        hrmAPI.newAuditChecklist = function(cl, userData) {
           return $http({
               method: 'POST',
               data: { checklist: cl, userData: userData },
               url: 'auditchecklist/new'
           });
        },

        hrmAPI.newAuditChecklistGroup = function(checklist_id, groupname, userData) {
           return $http({
               method: 'POST',
               data: { checklist_id: checklist_id, name: groupname,  userData: userData },
               url: 'auditchecklist/group/new'
           });
        },

        hrmAPI.newAuditChecklistItem = function(groupDetail, name, userData) {
           return $http({
               method: 'POST',
               data: { group: groupDetail, name: name,  userData: userData },
               url: 'auditchecklist/group/item/new'
           });
        },

        hrmAPI.reorderAuditChecklistItem = function(direction, obj, userData) {
           return $http({
               method: 'POST',
               data: { direction: direction, item: obj,  userData: userData },
               url: 'auditchecklist/item/reorder'
           });
        },

        hrmAPI.deleteChecklistItem = function(item, userData) {
           return $http({
               method: 'POST',
               data: { item: item,  userData: userData },
               url: 'auditchecklist/item/delete'
           });
        },

        hrmAPI.duplicateAuditChecklist = function(cl, userData) {
           return $http({
               method: 'POST',
               data: { checklist: cl,  userData: userData },
               url: 'auditchecklist/duplicate'
           });
        },

       hrmAPI.getChecklist = function(id) {
            return $http({
                method: 'GET',
                data: {},
                url: 'checklist/get/' + id
            });
       },

       hrmAPI.printChecklist = function(id) {
            return $http({
                method: 'GET',
                data: {},
                url: 'printchecklist/' + id
            });
       },

       hrmAPI.getActionChecklists = function(userData) {
            return $http({
                method: 'POST',
                data: {userData: userData },
                url: 'auditaction/get'
            });
       },

       hrmAPI.createAudit = function(id, userData) {
            return $http({
                method: 'POST',
                data: { checklist_id: id,  userData: userData },
                url: 'auditaction/create'
            });
       },

       hrmAPI.getActionChecklist = function(id) {
            return $http({
                method: 'GET',
                data: {},
                url: 'auditaction/get/' + id
            });
       },

       hrmAPI.getActionReportChecklist = function(id) {
            return $http({
                method: 'GET',
                data: {},
                url: 'report/auditaction/get/' + id
            });
       },

       hrmAPI.saveActionItemResult = function(item, chk, fld, userData) {
            return $http({
                method: 'POST',
                data: {item: item, obj: chk, field: fld, userData: userData},
                url: 'auditaction/item/save'
            });
       },

       hrmAPI.saveAuditActionResult = function(item, data, userData) {
            return $http({
                method: 'POST',
                data: {item: item, data: data, userData: userData},
                url: 'auditaction/checklistresult/save'
            });
       },

       hrmAPI.archiveAuditAction = function(item, userData) {
            return $http({
                method: 'POST',
                data: {item: item, userData: userData},
                url: 'auditaction/checklist/archive'
            });
       },

       hrmAPI.deleteAuditAction = function(item, userData) {
            return $http({
                method: 'POST',
                data: {item: item, userData: userData},
                url: 'auditaction/checklist/delete'
            });
       },

       hrmAPI.getWHSReportData = function(userData) {
            return $http({
                method: 'POST',
                data: {userData: userData},
                url: 'whsreport/getdata'
            });
       },
       hrmAPI.getTrainingReportData = function(userData) {
            return $http({
                method: 'POST',
                data: {userData: userData},
                url: 'trainingreport/getdata'
            });
       },

        hrmAPI
    })
