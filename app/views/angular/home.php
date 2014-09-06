<div class="row" xmlns="http://www.w3.org/1999/html">
<div class = "col-lg-12">
    <div data-ng-controller="AccountsController" ng-show="accounts">
      <select ng-model="myAccount" ng-options="account.name for account in accounts" ng-change="change()"> </select>
    </div>
  </div>
</div>

<div class = "row">
  <div class = "col-lg-8">
    <div class = "row">
      <div class = "col-lg-12" ng-include="'transactionstable.html'"></div>
    </div>
  </div>
  <div class = "col-lg-4">
<!--    <div class = "col-lg-12" > <payee-selector> </payee-selector> </div>-->
    <div class = "col-lg-12" ng-include="'view/transaction.html'"></div>
  </div>
</div>


<script type="text/ng-template" id="transactionstable.html">
  <div data-ng-controller="TransactionsController" ng-show="transactions" >
    <h3>{{account.name}}</h3>
    <table class="table table-striped">
      <tr>
        <th>Date</th>
        <th>Payee</th>
        <th>Credit</th>
        <th>Debit</th>
        <th>Balance</th>
      </tr>
      <tr ng-repeat="transaction in transactions">
        <td>{{transaction.date | date}} </td>
        <td><payee payeeid="transaction.payee_id" />           </td>
        <td>
          <span ng-show="transaction.amount>0">{{ transaction.amount  | currency: "£" }}   </span>
          <transaction source="transaction.source" />
        </td>
        <td>
          <span ng-show="transaction.amount<0">{{-transaction.amount  | currency: "£" }}   </span>
          <transaction destination="transaction.destination" />
        </td>
        <td>{{transaction.balance | currency: "£" }}</td>
        <!--
              <td><span ng-show="accountId==transaction.credit.account_id">{{transaction.credit.amount  | currency: "£" }}  <account direction="from" accountid="transaction.credit.transfer_id">  </account> </span></td>
              <td><span ng-show="accountId==transaction.debit.account_id">{{transaction.debit.amount  | currency: "£" }}    <account direction="to" accountid="transaction.credit.transfer_id">  </account> </span></td>
        -->
      </tr>
    </table>
  </div>
</script>



<script type="text/ng-template" id="account.html">
  <span ng-show="account.id">
        ({{direction}} {{account.name}})
    </span>
</script>

<script type="text/ng-template" id="transaction.html">
  <span ng-show="transaction.id">
    {{direction}} {{account.name}}
  </span>
</script>

<script type="text/ng-template" id="payee.html">
  <span ng-show="payee.id">
    {{payee.name}}
  </span>
</script>
