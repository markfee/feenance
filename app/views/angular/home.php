
<div class="row">
  <div class = "col-lg-12">
    <div data-ng-controller="AccountsController" ng-show="accounts">
      <select ng-model="myAccount" ng-options="account.name for account in accounts" ng-change="change()"> </select>
    </div>
    <div ngModel="CurrentAccount"></div>
  </div>
</div>

<div class = "row">
  <div class = "col-lg-8">
    <div class = "row">
      <div class = "col-lg-12" ng-include="'view/transactionsTable.html'"></div>
    </div>
  </div>
  <div class = "col-lg-4">
    <new-transaction accountId=""></new-transaction>
  </div>
</div>
