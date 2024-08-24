@extends('layouts.master')

@section('css')
<!--Chartist Chart CSS -->
<link rel="stylesheet" href="{{ URL::asset('plugins/chartist/css/chartist.min.css') }}">
@endsection

@section('breadcrumb')
<div class="col-sm-6 text-left" >
     <h4 class="page-title">Dashboard</h4>
     <ol class="breadcrumb">
         <li class="breadcrumb-item active">Mirësevini në Sistemin e Menaxhimit të Pjesëmarrjes</li>
     </ol>
     <li id="locationOutput" class="breadcrumb-item"></li>
</div>
@endsection

@section('content')
@php
    $employee = auth()->user()->employee;
    $checkin = $employee->attendances()->where('punch_state', 0)->whereDate('punch_time', now()->format('Y-m-d'))->first();
    $checkout = $employee->attendances()->where('punch_state', 1)->whereDate('punch_time', now()->format('Y-m-d'))->first();
    $breakin = $employee->attendances()->where('punch_state', 3)->whereDate('punch_time', now()->format('Y-m-d'))->first();
    $breakout = $employee->attendances()->where('punch_state', 2)->whereDate('punch_time', now()->format('Y-m-d'))->first();
@endphp
    <div class="row">

        <div class="col-xl-3 col-md-6 col-sm-6 col-xs-6">
            <div class="card text-white {{ (!$checkin ? 'bg-info': '') }} {{ ($checkin && $checkout ? 'bg-info' : '')}} {{ ($checkin && !$checkout ? 'bg-success' : '') }}">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-left mini-stat-img mr-4">
                            <i class=" ti-time " style="font-size: 30px"></i>
                        </div>
                        <h5 class="font-16 text-uppercase mt-0 text-white-50">Checkin <br> Sot</h5>
                        
                            
                    </div>
                    <h1 class="font-500 float-right">
                        <div class="countup" id="checkin">
                            <span class="timeel d-none years">00</span>
                            <span class="timeel d-none timeRefYears">years</span>
                            <span class="timeel d-none days">00</span>
                            <span class="timeel d-none timeRefDays">days</span>
                            <span class="timeel hours">00</span>:
                            <span class="timeel minutes">00</span>:
                            <span class="timeel seconds">00</span>
                        </div> 
                        <i class=" text-success ml-2"></i>
                    </h1>

                </div>
                @if(!$checkin) 
                <button id="toggle-checkin" class="btn btn-success">Fillo</button>
                @elseif($checkin && !$checkout)
                <button @if($breakin && !$breakout) disabled @endif id="toggle-checkin" class="btn btn-danger">Mbaro</button>
                @endif
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-sm-6 col-xs-6">
            <div class="card text-white {{ (!$checkin ? 'bg-info': '') }} {{ ($checkin && $checkout ? 'bg-info' : '')}} {{ ($checkin && !$checkout ? 'bg-success' : '') }}">
                <div class="card-body">
                    <div class="mb-4">
                        <div class="float-left mini-stat-img mr-4">
                            <i class="ti-control-pause" style="font-size: 30px"></i>
                        </div>
                        <h5 class="font-16 text-uppercase mt-0 text-white-50">Pauz <br> Sot</h5>
                        
                            
                    </div>
                    <h1 class="font-500 float-right">
                        <div class="countup" id="pause">
                            <span class="timeel d-none years">00</span>
                            <span class="timeel d-none timeRefYears">years</span>
                            <span class="timeel d-none days">00</span>
                            <span class="timeel d-none timeRefDays">days</span>
                            <span class="timeel hours">00</span>:
                            <span class="timeel minutes">00</span>:
                            <span class="timeel seconds">00</span>
                        </div> 
                        <i class=" text-success ml-2"></i>
                    </h1>

                </div>
                @if(!$breakin)
                <button @if(!$checkin) disabled @endif id="toggle-breakin" class="btn btn-success">Fillo</button>
                @elseif($breakin && !$breakout) 
                <button id="toggle-breakin" class="btn btn-danger">Mbaro</button>
                @endif
            </div>
        </div>
      
    </div>
    <!-- end row -->

    <!-- new row end -->
    <div class="row">
        <div class="col-xl-9">
            <div class="card">
                <!-- <div class="card-body">
                    <h4 class="mt-0 header-title mb-5">Monthly Report</h4>
                    
                </div> -->
            </div>
            <!-- end card -->
        </div>

        <div class="col-xl-3">
            <div class="card">
                <!-- <div class="card-body">
                    <div>
                        <h4 class="mt-0 header-title mb-4">Sales Analytics</h4>
                    </div>
                    
                    
                    </div> -->
            </div>
        </div>
    </div>
    <!-- end row -->
    
    
    <!-- end row -->
@endsection

@section('script')
<!--Chartist Chart-->
<script src="{{ URL::asset('plugins/chartist/js/chartist.min.js') }}"></script>
<script src="{{ URL::asset('plugins/chartist/js/chartist-plugin-tooltip.min.js') }}"></script>
<!-- peity JS -->
<script src="{{ URL::asset('plugins/peity-chart/jquery.peity.min.js') }}"></script>
<script src="{{ URL::asset('assets/pages/dashboard.js') }}"></script>

<script>
function countUpFromTimePause(countFrom, countTo = null, id) {
  countFrom = new Date(countFrom).getTime();
  var now = countTo ? new Date(countTo).getTime() : new Date();
  var countFrom = new Date(countFrom),
    timeDifference = (now - countFrom);
    
  var secondsInADay = 60 * 60 * 1000 * 24,
      secondsInAHour = 60 * 60 * 1000;
    
  days = Math.floor(timeDifference / (secondsInADay) * 1);
  years = Math.floor(days / 365);
  if (years > 1){ days = days - (years * 365) }
  hours = Math.floor((timeDifference % (secondsInADay)) / (secondsInAHour) * 1);
  mins = Math.floor(((timeDifference % (secondsInADay)) % (secondsInAHour)) / (60 * 1000) * 1);
  secs = Math.floor((((timeDifference % (secondsInADay)) % (secondsInAHour)) % (60 * 1000)) / 1000 * 1);
  var idEl = document.getElementById(id);
  idEl.getElementsByClassName('years')[0].innerHTML = isNaN(years) ? '00' : years;
  idEl.getElementsByClassName('days')[0].innerHTML = isNaN(days) ? '00' : days;
  idEl.getElementsByClassName('hours')[0].innerHTML = isNaN(hours) ? '00' : hours;
  idEl.getElementsByClassName('minutes')[0].innerHTML = isNaN(mins) ? '00' : mins;
  idEl.getElementsByClassName('seconds')[0].innerHTML = isNaN(secs) ? '00' : secs;

  clearTimeout(countUpFromTimePause.interval);
  countUpFromTimePause.interval = setTimeout(function(){ countUpFromTimePause(countFrom, countTo, id); }, 1000);
}

function countUpFromTime(countFrom, countTo = null, id) {
  countFrom = new Date(countFrom).getTime();
  var now = countTo ? new Date(countTo).getTime() : new Date();
  var countFrom = new Date(countFrom),
    timeDifference = (now - countFrom);
    
  var secondsInADay = 60 * 60 * 1000 * 24,
      secondsInAHour = 60 * 60 * 1000;
    
  days = Math.floor(timeDifference / (secondsInADay) * 1);
  years = Math.floor(days / 365);
  if (years > 1){ days = days - (years * 365) }
  hours = Math.floor((timeDifference % (secondsInADay)) / (secondsInAHour) * 1);
  mins = Math.floor(((timeDifference % (secondsInADay)) % (secondsInAHour)) / (60 * 1000) * 1);
  secs = Math.floor((((timeDifference % (secondsInADay)) % (secondsInAHour)) % (60 * 1000)) / 1000 * 1);

  var idEl = document.getElementById(id);
  idEl.getElementsByClassName('years')[0].innerHTML = isNaN(years) ? '00' : years;
  idEl.getElementsByClassName('days')[0].innerHTML = isNaN(days) ? '00' : days;
  idEl.getElementsByClassName('hours')[0].innerHTML = isNaN(hours) ? '00' : hours;
  idEl.getElementsByClassName('minutes')[0].innerHTML = isNaN(mins) ? '00' : mins;
  idEl.getElementsByClassName('seconds')[0].innerHTML = isNaN(secs) ? '00' : secs;

  clearTimeout(countUpFromTime.interval);
  countUpFromTime.interval = setTimeout(function(){ countUpFromTime(countFrom, countTo, id); }, 1000);
}

</script>

<script>
    var checkin = @json($checkin);
    var checkout = @json($checkout);
    var breakin = @json($breakin);
    var breakout = @json($breakout);
        window.onload = function() {
            countUpFromTime(checkin?.punch_time, checkout?.punch_time, 'checkin'); // ****** Change this line!
            countUpFromTimePause(breakin?.punch_time, breakout?.punch_time, 'pause');

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;
                    document.getElementById('locationOutput').textContent = `Latitude: ${latitude}, Longitude: ${longitude}`;
                    // Here you can also send the coordinates to the server using axios or any other method
                }, function(error) {
                    showError(error);
                });
            } else {
                document.getElementById('locationOutput').textContent = "Geolocation is not supported by this browser.";
            }
        };

        function showError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    document.getElementById('locationOutput').textContent = "User denied the request for Geolocation.";
                    break;
                case error.POSITION_UNAVAILABLE:
                    document.getElementById('locationOutput').textContent = "Location information is unavailable.";
                    break;
                case error.TIMEOUT:
                    document.getElementById('locationOutput').textContent = "The request to get user location timed out.";
                    break;
                case error.UNKNOWN_ERROR:
                    document.getElementById('locationOutput').textContent = "An unknown error occurred.";
                    break;
            }
        }


        $("#toggle-checkin").on("click", function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    // Get the latitude and longitude from the position object
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    // Make the axios POST request with the coordinates
                    axios.post('/attendance-tap', {
                        latitude: latitude,
                        longitude: longitude,
                        checkType: 'checkin',

                        // Add any other data you need to send with the request
                    })
                    .then(function(response) {
                        // Handle the response
                        if(response.data.punch_state == '0') {
                            checkin = response.data;
                            $('#toggle-checkin').removeClass('btn-success');
                            $('#toggle-checkin').addClass('btn-danger');
                            $('#toggle-checkin').text('Mbaro');
                            $('#toggle-breakin').removeAttr('disabled');
                        }else {
                            checkout = response.data;
                            $('#toggle-checkin').remove();
                            $('#toggle-breakin').remove();
                        }
                        countUpFromTime(checkin?.punch_time, checkout?.punch_time, 'checkin');
                    })
                    .catch(function(error) {
                        // Handle errors
                        alert(error);
                    });
                }, function(error) {
                    // Handle errors from geolocation
                    alert("Geolocation error: ", error.message);
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        });

        $("#toggle-breakin").on("click", function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    // Get the latitude and longitude from the position object
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    // Make the axios POST request with the coordinates
                    axios.post('/attendance-tap', {
                        latitude: latitude,
                        longitude: longitude,
                        checkType: 'pause',

                        // Add any other data you need to send with the request
                    })
                    .then(function(response) {
                        // Handle the response
                        if(response.data.punch_state == '3') {
                            breakin = response.data;
                            $('#toggle-breakin').removeClass('btn-success');
                            $('#toggle-breakin').addClass('btn-danger');
                            $('#toggle-breakin').text('Mbaro');
                            $('#toggle-checkin').attr('disabled', true);
                        }else {
                            breakout = response.data;
                            $('#toggle-breakin').remove();
                            $('#toggle-checkin').removeAttr('disabled');
                        }
                        countUpFromTimePause(breakin?.punch_time, breakout?.punch_time, 'pause');
                    })
                    .catch(function(error) {
                        // Handle errors
                        alert(error);
                    });
                }, function(error) {
                    // Handle errors from geolocation
                    alert("Geolocation error: ", error.message);
                });
            } else {
                alert("Geolocation is not supported by this browser.");
            }
        });
    </script>
@endsection