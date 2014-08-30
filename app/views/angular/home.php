<div class = "col-lg-12">
  <div data-ng-controller="AccountsController" ng-show="accounts">
    <select ng-model="myAccount" ng-options="account.name for account in accounts" ng-change="change()">    </select>
  </div>
</div>

<div class = "col-lg-8">
  <div ng-include="'transactionstable.html'"></div>
</div>

<div class = "col-lg-4">
  <div ng-include="'newTransaction.html'"></div>
</div>

<script type="text/ng-template" id="newTransaction.html">
  <div data-ng-controller="TransactionsController" >
  <h3>New Transaction<h3/>
      <form novalidate class="simple-form">
        Date <input type="date"  ng-model="transaction.date"> <br />

        Name: <input type="text" ng-model="user.name" /><br />
        E-mail: <input type="email" ng-model="user.email" /><br />
        Gender: <input type="radio" ng-model="user.gender" value="male" />male
        <input type="radio" ng-model="user.gender" value="female" />female<br />
        <button ng-click="reset()">RESET</button>
        <button ng-click="update(user)">SAVE</button>
    </form>
      <pre>form = {{user | json}}</pre>
      <pre>master = {{master | json}}</pre>
</script>


<script type="text/ng-template" id="transactionstable.html">
  <div data-ng-controller="TransactionsController" ng-show="items" >
    <h3>{{account.name}}</h3>
    <table class="table table-striped">
      <tr>
        <th>Date</th>
        <th>Payee</th>
        <th>Credit</th>
        <th>Debit</th>
        <th>Balance</th>
      </tr>
      <tr ng-repeat="item in items">
        <td>{{item.date | date}} </td>
        <td><payee payeeid="item.payee_id" />           </td>
        <td><span ng-show="item.amount>0">{{ item.amount  | currency: "£" }}   </span> <transaction source="item.source" />           </td>
        <td><span ng-show="item.amount<0">{{-item.amount  | currency: "£" }}   </span> <transaction destination="item.destination" /> </td>
        <td>{{item.balance | currency: "£" }}</td>
        <!--
              <td><span ng-show="accountId==item.credit.account_id">{{item.credit.amount  | currency: "£" }}  <account direction="from" accountid="item.credit.transfer_id">  </account> </span></td>
              <td><span ng-show="accountId==item.debit.account_id">{{item.debit.amount  | currency: "£" }}    <account direction="to" accountid="item.credit.transfer_id">  </account> </span></td>
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
