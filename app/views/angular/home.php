<div data-ng-controller="TransactionController" ng-show="items">
  <h3>Transactions</h3>
  <table class="table table-striped">
    <tr>                            <th>Date    </th> <th>Credit</th> <th>Debit</th>   </tr>
    <tr ng-repeat="item in items">  <td>{{item.date | date:'dd/MM/yyyy'}} </td> <td>{{item.credit.amount | currency: "£" }} </td>  <td>{{item.debit.amount | currency: "£" }} </td>
    </tr>
  </table>
</div>

