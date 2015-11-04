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

app.controller('textCtrl', function($scope, $notification, TextApi, $sce) {

    TextApi.one({'id':1}).$promise.then(function (response) {
        $scope.component = response['data'];
        $scope.escapedContent = $sce.trustAsHtml($scope.component.content);
        //$scope.ckEditor = {value:$scope.component['content']};
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
        $scope.escapedContent = $sce.trustAsHtml($scope.component.content);
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

app.directive('ckEditor', [function () {
    return {
        require: '?ngModel',
        link: function ($scope, elm, attr, ngModel) {

            var ck = CKEDITOR.inline(elm[0]);

            ck.on('pasteState', function () {
                $scope.$apply(function () {
                    ngModel.$setViewValue(ck.getData());
                });
            });

            ngModel.$render = function (value) {
                ck.setData(ngModel.$modelValue);
            };
        }
    };
}])