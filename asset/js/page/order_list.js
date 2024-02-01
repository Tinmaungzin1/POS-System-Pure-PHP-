var app = angular.module("myApp", []);

app.controller("myCtrl", function ($scope, $http) {
  $scope.allOrder = [];
  $scope.base_url = base_url;

  $scope.init = function () {
    var data = {
      shift_id: shift_id,
    };
    var url = base_url + "api/get_order";
    $http({
      method: "POST",
      url: url,
      data: data,
    }).then(
      function (response) {
        if (response.status === 200) {
          $scope.allOrder = response.data;
          console.log($scope.allOrder);
        }
      },
      function (error) {
        console.error(error);
      }
    );
  };

  $scope.orderCancel = function (order_id, status) {
    var actionText = status === 2 ? "Cancel" : "Activate";
    var confirmButtonText = "Yes, " + actionText;

    Swal.fire({
      title: "Are you sure?",
      text:
        "Once " + actionText.toLowerCase() + "ed, you cannot undo this action!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: confirmButtonText,
      cancelButtonText: "No, go back",
    }).then((result) => {
      if (result.isConfirmed) {
        // User clicked the "Yes, cancel it" button
        var data = {
          order_id: order_id,
          status: status,
        };
        var url = base_url + "api/order_cancel";
        $http({
          method: "POST",
          url: url,
          data: data,
        }).then(
          function (response) {
            if (response.status === 200) {
              // Update $scope.allOrder or perform any necessary actions
              $scope.init();
            }
          },
          function (error) {
            console.error(error);
          }
        );
      }
    });
  };
});

// Include your other scripts or dependencies here
