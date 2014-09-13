<div data-ng-controller="FeenanceController">
  <account-selector ng-model="account" account-id="2"> </account-selector>
  <button ng-click="toggleDebug()">Debug</button>

  <div class = "row">
    <div class = "col-lg-8">
      <div class = "row">
        <div class = "col-lg-12" ng-include="'view/transactionsTable.html'"></div>
      </div>
    </div>
    <div class = "col-lg-4">
      <new-transaction accountId=""></new-transaction>
      <transaction-uploader ng-model="transaction.uploadFile" ></transaction-uploader>

    </div>
  </div>
</div>
