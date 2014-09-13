@extends('layouts.default')
@section('content')
<div>

  {{ Form::open( array('action' => 'api\TransactionsController@upload', 'class'=>'form-horizontal', 'files' => true)) }}

  {{Form::file('file')}}

  {{ Form::submit('Submit', array('class' => 'btn btn-info')) }}

  {{ Form::close() }}

</div>

  <p>

  </p>

@stop