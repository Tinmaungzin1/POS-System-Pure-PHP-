var app = angular.module("myApp", []);

app.controller("myCtrl", function ($scope, $http, $window) {
  $scope.base_url = base_url;
  $scope.total = 0;
  $scope.kyats = [];
  $scope.selectIndex = [];
  $scope.number = "";
  $scope.balance = "";
  $scope.refund = "";
  $scope.disabled = false;
  $scope.payment_disabled = true;
  $scope.showInvoice = false;

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
          console.log(response.data);
        }
      },
      function (error) {
        console.error(error);
      }
    );
  };

  // $scope.toggleInvoice = function () {
  //   $scope.showInvoice = true; // Show the content when the button is clicked
  //   $scope.printInvoice(); // Call the print function
  // };

  // // Function to handle printing logic
  // $scope.printInvoice = function () {
  //   var printContent = document.getElementById("order_detail"); // ID of the container you want to print

  //   var printWindow = $window.open("", "_blank");
  //   printWindow.document.write("<html><head><title>Print</title></head><body>");
  //   printWindow.document.write(printContent.innerHTML);
  //   printWindow.document.write("</body></html>");
  //   printWindow.document.close();

  //   // Wait for content to be rendered before initiating print
  //   printWindow.onload = function () {
  //     printWindow.print();
  //     printWindow.onafterprint = function () {
  //       printWindow.close();
  //       $scope.showInvoice = false; // Hide the content after printing
  //     };
  //   };
  // };
  // $scope.payCash = function (value) {
  //   var cash = parseInt(value);
  //   const index = Math.max(...$scope.kyats.map((kyat) => kyat.index), 0) + 1;
  //   const quantity = $scope.number === "" ? 1 : parseInt($scope.number);
  //   const total_cash = cash * quantity;
  //   var data = {
  //     cash: cash,
  //     index: index,
  //     quantity: quantity,
  //     total_cash: total_cash,
  //   };
  //   $scope.kyats.push(data);
  //   $scope.clearInput();
  //   $scope.sumTotal();
  // };

  $scope.payCash = function (value) {
    let cash = parseInt(value);
    let index = $scope.kyats.length + 1;
    let quantity = $scope.number === "" ? 1 : parseInt($scope.number);
    let total_cash = cash * quantity;
    const data = {
      cash: cash,
      index: index,
      quantity: quantity,
      total_cash: total_cash,
    };
    $scope.kyats.push(data);
    $scope.clearInput();
    $scope.calculateBalance();
  };
  // $scope.selectCash = function (index) {
  //   if (!$scope.selectIndex.includes(index)) {
  //     $scope.selectIndex.push(index);
  //   } else {
  //     $scope.selectIndex.splice($scope.selectIndex.indexOf(index), 1);
  //   }
  // };

  $scope.selectCash = function (index) {
    let indexPosition = $scope.selectIndex.indexOf(index);
    if (indexPosition !== -1) {
      $scope.selectIndex.splice(indexPosition, 1);
    } else {
      $scope.selectIndex.push(index);
    }
  };

  // $scope.void = function () {
  //   $scope.kyats = $scope.kyats.filter((kyat) => {
  //     return $scope.selectIndex.indexOf(kyat.index) === -1;
  //   });
  //   $scope.selectIndex = [];
  //   $scope.sumTotal();
  // };

  $scope.void = function () {
    $scope.kyats = $scope.kyats.filter(function (kyat) {
      return $scope.selectIndex.indexOf(kyat.index) === -1;
    });
    $scope.selectIndex = [];
    for (i = 0; i < $scope.kyats.length; i++) {
      const index = i + 1;
      $scope.kyats[i].index = index;
    }
    $scope.calculateBalance();
  };
  // $scope.numberFocus = function () {
  //   $scope.focus_input = "number";
  // };

  $scope.numberClick = function (value) {
    let input_num = parseInt(value);
    $scope.number += input_num;
  };

  $scope.clearInput = function () {
    $scope.number = "";
  };

  $scope.calculateBalance = function () {
    // $scope.total = 0;
    // $scope.total = $scope.kyats.reduce(function (total, kyat) {
    //   return total + kyat.total_cash;
    // }, 0);
    let total_cash_result = 0;
    for (i = 0; i < $scope.kyats.length; i++) {
      const cash_result = parseInt($scope.kyats[i].total_cash);
      total_cash_result = parseInt(total_cash_result) + cash_result;
    }
    if (total_cash_result >= $scope.order.total_amount) {
      $scope.payment_disabled = false;
      $scope.disabled = true;
      $scope.refund =
        parseInt(total_cash_result) - parseInt($scope.order.total_amount);
    } else {
      $scope.payment_disabled = true;
      $scope.disabled = false;
      $scope.refund = "";
    }
    $scope.balance = $scope.order.total_amount - total_cash_result;
    $scope.balance = $scope.balance < 0 ? 0 : $scope.balance;
  };

  // $scope.balance =
  //   $scope.total <= $scope.order.total_amount
  //     ? $scope.order.total_amount - $scope.total
  //     : 0;

  // $scope.balance =
  //   $scope.order.total_amount - $scope.total >= 0
  //     ? $scope.total - $scope.order.total_amount
  //     : 0;
  // $scope.refund =
  //   $scope.order.total_amount - $scope.total < 0
  //     ? $scope.order.total_amount - $scope.total
  //     : 0;
  // $scope.refund =
  //   $scope.total > $scope.order.total_amount
  //     ? $scope.total - $scope.order.total_amount
  //     : 0;

  $scope.payment = function () {
    // console.log($scope.kyats);
    let total_customer_pay = 0;
    for (i = 0; i < $scope.kyats.length; i++) {
      const cash_result = parseInt($scope.kyats[i].total_cash);
      total_customer_pay = parseInt(total_customer_pay) + parseInt(cash_result);
    }
    var data = {
      id: $scope.order.id,
      customer_pay: total_customer_pay,
      refund: $scope.refund,
      kyats: $scope.kyats,
    };

    var url = $scope.base_url + "api/store_payment";
    $http({
      method: "POST",
      url: url,
      data: data,
    }).then(
      function (response) {
        if (response.status === 200) {
          // window.location.href = base_url + "order_list";
        }
      },
      function (error) {
        console.error(error);
      }
    );
  };

  $scope.printInvoice = function () {
    var printContents = document.getElementById("order_detail").innerHTML;
    var originalContents = document.body.innerHTML;

    // Open a new window and set the content
    var printWindow = $window.open("", "_blank");
    printWindow.document.open();
    printWindow.document.write("<html><head><title>Print</title>");

    // Check for external stylesheets
    var stylesheets = document.querySelectorAll('link[rel="stylesheet"]');
    stylesheets.forEach(function (stylesheet) {
      printWindow.document.write(stylesheet.outerHTML);
    });

    printWindow.document.write("</head><body>");
    printWindow.document.write(printContents);
    printWindow.document.write("</body></html>");
    printWindow.document.close();

    // Print the content
    printWindow.print();

    // Restore the original content
    document.body.innerHTML = originalContents;
  };
});
