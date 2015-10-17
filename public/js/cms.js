var app = angular.module('AlienCMS', []);

app.controller('navbarCtrl', function($scope) {

    $scope.isEditing = false;
    $scope.isLinkEditing = false;
    $scope.editingLink = {};
    $scope.tempEditingLink = {};
    $scope.links = [
        {
            'label' : 'Home',
            'link' : 'home'
        },
        {
            'label' : 'Projects',
            'link' : 'projects'
        },
        {
            'label' : 'Services',
            'link' : 'services'
        },
        {
            'label' : 'Downloads',
            'link' : 'downloads'
        },
        {
            'label' : 'About',
            'link' : 'about'
        },
        {
            'label' : 'Contact',
            'link' : 'contact'
        }
    ];
    $scope.tempLinks = angular.copy($scope.links);

    $scope.createNewLink = function()
    {
        $scope.links.push({
            'link' : '#',
            'label' : 'nový link'
        })
    }

    $scope.setToEditMode = function() {
        $scope.isEditing = true;
    }

    $scope.cancelEditMode = function() {
        $scope.isEditing = false;
        $scope.links = angular.copy($scope.tempLinks);
    }

    $scope.saveEditing = function() {
        $scope.isEditing = false;
        $scope.tempLinks = angular.copy($scope.links);
    }

    $scope.editLink = function(link) {
        $scope.tempEditingLink = angular.copy(link);
        $scope.editingLink = link;
        $scope.isLinkEditing = true;
    }

    $scope.saveLinkEdit = function() {
        $scope.tempLinks = angular.copy($scope.links);
        $scope.isLinkEditing = false;
    }

    $scope.cancelLinkEdit = function() {
        $scope.links = angular.copy($scope.tempLinks);
        $scope.isLinkEditing = false;
    }

});