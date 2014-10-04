@extends('layouts.default')
@section('content')
<div>

  <div>

    <account-selector ng-model="account" account-id="2"> </account-selector>

    <div class = "row">

      <div class = "col-lg-8"  ng-include="'view/transactionsTable.html'"> </div>

      <div class = "col-lg-4">
        <new-map accountId=""></new-map>
        <new-transaction accountId=""></new-transaction>
      </div>

    </div>

  </div>

</div>

@stop