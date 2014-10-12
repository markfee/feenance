@extends('layouts.default')
  @section('content')
  <div>

    <div>

      <div class = "row">

        <div class = "col-lg-12">

          <category-report year="{{{$year}}}" month="{{{$month}}}" end_year="{{{$endYear}}}" end_month="{{{$endMonth}}}" > Category report Here </category-report>

        </div>

      </div>

    </div>

  </div>

@stop