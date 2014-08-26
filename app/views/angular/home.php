<div data-ng-controller="AccountsController" ng-show="accounts">
  <select ng-model="myAccount" ng-options="account.title for account in accounts" ng-change="change()">

  </select>

</div>

<script type="text/ng-template" id="account.html">
  <span ng-show="account.id">
        ({{direction}} {{account.title}})
    </span>
</script>



<div data-ng-controller="TransactionsController" ng-show="items">

  <h3>Transactions</h3>
  <table class="table table-striped">
    <tr>                            <th>Date    </th> <th>Credit</th> <th>Debit</th>   </tr>
    <tr ng-repeat="item in items">
      <td>{{item.date | date}} </td>
      <td><span ng-show="item.amount>0">{{item.amount  | currency: "£" }}   </span></td>
      <td><span ng-show="item.amount<0">{{item.amount  | currency: "£" }}   </span></td>
<!--  <td><span ng-show="accountId==item.credit.account_id">{{item.credit.amount  | currency: "£" }}  <account direction="from" accountid="item.credit.transfer_id">  </account> </span></td>
      <td><span ng-show="accountId==item.debit.account_id">{{item.debit.amount  | currency: "£" }}    <account direction="to" accountid="item.credit.transfer_id">  </account> </span></td>-->
    </tr>
  </table>
</div>