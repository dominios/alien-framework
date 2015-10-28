var app = angular.module('AlienCMS', ['ngResource', 'notifications', 'navbarServices', 'textServices']);


app.controller('navbarCtrl', function ($scope, $notification, NavbarApi) {

    NavbarApi.list().$promise.then(function (response) {
        $scope.links = response['data'];
        $scope.tempLinks = angular.copy($scope.links);
    });

    $scope.isEditing = false;
    $scope.isLinkEditing = false;
    $scope.editingLink = {};
    $scope.tempEditingLink = {};
    $scope.tempLinks = angular.copy($scope.links);

    var cancelEditing = function () {
        $scope.isEditing = false;
        $scope.isLinkEditing = false;
        $scope.links = angular.copy($scope.tempLinks);
        $scope.editingLink = {};
    };

    var startEditing = function () {
        $scope.isEditing = true;
        $scope.tempLinks = angular.copy($scope.links);
    };

    var startLinkEditing = function () {
        startEditing();
        $scope.isLinkEditing = true;
    }

    var cancelLinkEditing = function () {
        $scope.isLinkEditing = false;
        $scope.editingLink = {};
    }

    $scope.createNewLink = function () {
        var newLink = {
            'link': '#',
            'label': 'nový link'
        };
        $scope.links.push(newLink);
        $scope.editLink(newLink);
    };

    $scope.setToEditMode = function () {
        startEditing();
    };

    $scope.cancelEditMode = function () {
        cancelEditing();
    };

    $scope.saveEditing = function () {
        $scope.tempLinks = angular.copy($scope.links);
        cancelEditing();
        NavbarApi.update($scope.links);
        $notification.success("Úspech!", "Zmeny boli úspešne uložené.");
    };

    $scope.editLink = function (link) {
        if(link !== $scope.editingLink) {
            cancelLinkEditing();
        }
        startLinkEditing();
        $scope.editingLink = link;
    };

    $scope.saveLinkEdit = function () {
        $scope.tempLinks = angular.copy($scope.links);
        cancelLinkEditing();
    };

    $scope.cancelLinkEdit = function () {
        cancelLinkEditing();
    };

    $scope.deleteLink = function () {
        $scope.links.splice($scope.links.indexOf($scope.editingLink), 1);
        $scope.saveLinkEdit();
    };

});

app.controller('textCtrl', function($scope, $notification, TextApi) {

    TextApi.one({'id':1}).$promise.then(function (response) {
        $scope.component = response['data']
    });

    $scope.isEditing = false;

    $scope.setToEditMode = function() {
        startEditing();
    };

    $scope.cancelEditMode = function() {
        stopEditing(false);
    };

    $scope.saveEditing = function() {
        stopEditing(true);
    };

    function startEditing() {
        $scope.isEditing = true;
    }

    function stopEditing(save) {
        $scope.isEditing = false;
        if(save) {
            // todo
            console.log($scope.component);
            //TextApi.patch();
            $notification.success("Úspech!", "Zmeny boli úspešne uložené.");
        }
    }

});

app.directive("contenteditable", function() {
    return {
        restrict: "A",
        require: "ngModel",
        link: function(scope, element, attrs, ngModel) {

            function read() {
                ngModel.$setViewValue(element.html());
            }

            ngModel.$render = function() {
                element.html(ngModel.$viewValue || "");
            };

            element.bind("blur keyup change", function() {
                scope.$apply(read);
            });
        }
    };
});