var app = angular.module("myApp", []);
app.controller("myCtrl", function ($scope, $http) {
  $scope.showCategory = true;
  $scope.showItem = false;
  $scope.categories = [];
  $scope.items = [];
  $scope.allItems = [];
  $scope.itemData = [];
  $scope.total = 0;
  $scope.base_url = base_url;

  $scope.init = function (id) {
    //when page refresh
    var data = {
      id: id,
    };

    var url = $scope.base_url + "api/get-order-items";
    $http({
      method: "POST",
      url: url,
      data: data,
    }).then(
      function (response) {
        if (response.status === 200) {
          $scope.itemData = response.data;
          // $scope.order = $scope.allOrder[0];
          // console.log($scope.allOrder);
        }
        $scope.calculateTotalAmount();
      },
      function (error) {
        console.error(error);
      }
    );
    $scope.fetchCategoryByParent(0);
    $scope.fetchAllItem();
  };

  $scope.getChildCategory = function (parent_id) {
    // click button parent category get child category
    $scope.categories = [];
    $scope.fetchCategoryByParent(parent_id);
  };

  $scope.getParentCategory = function () {
    // top back button
    $scope.fetchCategoryByParent(0);
  };

  $scope.fetchCategoryByParent = function (parent_id) {
    //common function
    $scope.showCategory = true;
    $scope.showItem = false;
    var data = {
      parent_id: parent_id,
    };
    var url = base_url + "api/get_category";
    $http({
      method: "POST",
      url: url,
      data: data,
    }).then(
      function (response) {
        if (response.status == 200) {
          if (response.data.length <= 0) {
            $scope.showCategory = false;
            $scope.showItem = true;
            $scope.fetchItem(parent_id);
          } else {
            $scope.categories = response.data;
          }
        }
      },
      function (error) {
        console.error(error);
      }
    );
  };

  $scope.fetchItem = function (category_id) {
    // get item
    var data = {
      category_id: category_id,
    };
    var url = base_url + "api/get_items";
    $http({
      method: "POST",
      url: url,
      data: data,
    }).then(
      function (response) {
        if (response.status == 200) {
          $scope.items = response.data;
        }
      },
      function (error) {
        console.error(error);
      }
    );
  };

  $scope.fetchAllItem = function () {
    // get item
    var data = {};
    var url = base_url + "api/get_all_items";
    $http({
      method: "POST",
      url: url,
      data: data,
    }).then(
      function (response) {
        if (response.status == 200) {
          $scope.allItems = response.data;
        }
      },
      function (error) {
        console.error(error);
      }
    );
  };

  $scope.calculateTotalAmount = function () {
    $scope.total = 0;
    for (i = 0; i < $scope.itemData.length; i++) {
      $scope.total += $scope.itemData[i].amount;
      // console.log($scope.itemData.amount);
    }
    // $scope.total = $scope.itemData.reduce(function (total, item) {
    //   return total + item.amount;
    // }, 0);
  };

  $scope.getItem = function (item_id) {
    // click item and show item
    var data = {
      item_id: item_id,
    };
    var url = base_url + "api/get_item";
    $http({
      method: "POST",
      url: url,
      data: data,
    }).then(
      function (response) {
        if (response.status == 200) {
          let item_exist = false;
          var updatedItems = $scope.itemData.map((item) => {
            if (item.id === item_id) {
              item_exist = true;
              const updatedQuantity = item.quantity + 1;
              return {
                ...item,
                quantity: updatedQuantity,
                discount: updatedQuantity * item.origin_discount,
                amount: updatedQuantity * item.origin_amount,
              };
            }
            return item;
          });
          if (item_exist) {
            $scope.itemData = updatedItems;
          } else {
            $scope.itemData.push(response.data[0]);
          }
          $scope.calculateTotalAmount();
        }
      },
      function (error) {
        console.error(error);
      }
    );
  };

  $scope.cancelItem = function (item_id) {
    // cancel item

    $scope.itemData = $scope.itemData.filter((item) => item.id != item_id);
    // console.log($scope.itemData);
    $scope.calculateTotalAmount();
  };

  $scope.plusItem = function (item_id) {
    // plus item
    $scope.itemData = $scope.itemData.map((item) => {
      if (item.id === item_id) {
        const updatedQuantity = item.quantity + 1;
        return {
          ...item,
          quantity: updatedQuantity,
          discount: updatedQuantity * item.origin_discount,
          amount: updatedQuantity * item.origin_amount,
        };
      }
      return item;
    });
    $scope.calculateTotalAmount();
  };

  $scope.minusItem = function (item_id) {
    // minus item
    $scope.itemData = $scope.itemData.map((item) => {
      if (item.id === item_id && item.quantity != 1) {
        const updatedQuantity = item.quantity - 1;
        return {
          ...item,
          quantity: updatedQuantity,
          discount: updatedQuantity * item.origin_discount,
          amount: updatedQuantity * item.origin_amount,
        };
      }
      return item;
    });
    $scope.calculateTotalAmount();
  };

  $scope.searchItem = function () {
    if ($scope.search_item === "") {
      $scope.showCategory = true;
      $scope.showItem = false;
    } else {
      $scope.showCategory = false;
      $scope.showItem = true;
      $scope.items = $scope.allItems.filter((result) => {
        return result.code_no
          .toLowerCase()
          .startsWith($scope.search_item.toLowerCase());
      });
    }
  };

  $scope.insertOrder = function (id) {
    // Calculate the total amount
    $scope.calculateTotalAmount();

    // Prepare the data to be sent to the server
    var orderData = {
      id: id,
      items: $scope.itemData,
      total: $scope.total,
      shift_id: shift_id, // Include the total amount
    };
    console.log(orderData);
    var url = base_url + "api/update-order";
    $http
      .post(url, orderData)
      .then(function (response) {
        if (response.status === 200) {
          // window.location.href = base_url + "order_list";
        } else {
          alert(response.data);
        }
      })
      .catch(function (error) {
        // Handle error
        console.log("Error inserting order:", error);
      });
    $scope.itemData = [];
    $scope.total = 0;
  };
  $scope.orderList = function () {
    window.location.href = base_url + "order_list";
  };
});
