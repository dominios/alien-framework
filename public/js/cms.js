var app = angular.module('AlienCMS', ['ngResource', 'navbarServices']);


app.controller('navbarCtrl', function ($scope, NavbarApi) {

    $promise = NavbarApi.getList().$promise.then(function (response) {
        $scope.links = response;
        $scope.tempLinks = angular.copy($scope.links);
    });

    $scope.isEditing = false;
    $scope.isLinkEditing = false;
    $scope.editingLink = {};
    $scope.tempEditingLink = {};
    $scope.tempLinks = angular.copy($scope.links);

    $scope.createNewLink = function () {
        $scope.links.push({
            'link': '#',
            'label': 'nový link'
        })
    }

    $scope.setToEditMode = function () {
        $scope.isEditing = true;
    }

    $scope.cancelEditMode = function () {
        $scope.isEditing = false;
        $scope.links = angular.copy($scope.tempLinks);
    }

    $scope.saveEditing = function () {
        $scope.isEditing = false;
        $scope.tempLinks = angular.copy($scope.links);
    }

    $scope.editLink = function (link) {
        $scope.tempEditingLink = angular.copy(link);
        $scope.editingLink = link;
        $scope.isLinkEditing = true;
    }

    $scope.saveLinkEdit = function () {
        $scope.tempLinks = angular.copy($scope.links);
        $scope.isLinkEditing = false;
    }

    $scope.cancelLinkEdit = function () {
        $scope.links = angular.copy($scope.tempLinks);
        $scope.isLinkEditing = false;
    }

});