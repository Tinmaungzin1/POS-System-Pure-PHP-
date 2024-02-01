var app = angular.module("myApp", []);

app.controller("myCtrl", function ($scope, $http) {
  $scope.base_url = base_url;
  $scope.total = 0;

  $scope.init = function (id) {
    var data = {
      id: id,
    };

    var url = $scope.base_url + "api/get_order_detail";
    $http({
      method: "POST",
      url: url,
      data: data,
    }).then(
      function (response) {
        if (response.status === 200) {
          $scope.allOrder = response.data;
          $scope.order = $scope.allOrder[0];
          console.log($scope.allOrder[0].setting);
        }
        $scope.calculateTotalAmount();
      },
      function (error) {
        console.error(error);
      }
    );
  };

  $scope.calculateTotalAmount = function () {
    $scope.total = 0;
    for (i = 0; i < $scope.allOrder.length; i++) {
      $scope.total += $scope.allOrder[i].amount;
    }
  };
});
