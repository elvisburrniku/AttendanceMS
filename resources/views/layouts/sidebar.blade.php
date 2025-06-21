      <!-- ========== Left Sidebar Start ========== -->
            <div class="left side-menu">
                <div class="slimscroll-menu" id="remove-scroll">

                    <!--- Sidemenu -->
                    <div id="sidebar-menu">
                        @if(auth()->user()->hasRole('admin'))
                        <!-- Left Menu Start -->
                        <ul class="metismenu" id="side-menu">
                            <li class="menu-title">Main</li>
                            <li class="">
                                <a href="{{route('admin')}}" class="waves-effect {{ request()->is("admin") || request()->is("admin/*") ? "mm active" : "" }}">
                                    <i class="ti-home"></i> <span> Fillimi </span>
                                </a>
                            </li>
                            

                            <li>
                                <a href="/employees" class="waves-effect {{ request()->is("employees") || request()->is("/employees/*") ? "mm active" : "" }}"><i class="ti-user"></i><span> Punonjësit </span></a>
                            </li>

                            <li>
                                <a href="/departments" class="waves-effect {{ request()->is("departments") || request()->is("/departments/*") ? "mm active" : "" }}"><i class="ti-user"></i><span> Departamentet </span></a>
                            </li>

                            <li>
                                <a href="/positions" class="waves-effect {{ request()->is("positions") || request()->is("/positions/*") ? "mm active" : "" }}"><i class="ti-user"></i><span> Pozicionet </span></a>
                            </li>
                            
                            <li>
                                <a href="/areas" class="waves-effect {{ request()->is("areas") || request()->is("/areas/*") ? "mm active" : "" }}"><i class="ti-user"></i><span> Zona </span></a>
                            </li>
                            
                            <li class="menu-title">Management</li>

                            <li class="has_sub">
                                <a href="javascript:void(0);" class="waves-effect">
                                    <i class="ti-time"></i> <span> Schedule & Shifts </span> <span class="menu-arrow"></span>
                                </a>
                                <ul class="list-unstyled">
                                    <li><a href="{{ route('calendar.index') }}">Shift Calendar</a></li>
                                    <li><a href="{{ route('schedules.index') }}">Employee Schedules</a></li>
                                    <li><a href="{{ route('shifts.index') }}">Shift Management</a></li>
                                    <li><a href="{{ route('time-intervals.index') }}">Time Intervals</a></li>
                                    <li><a href="{{ route('schedules.bulk') }}">Bulk Assignment</a></li>
                                </ul>
                            </li>
                            <li>
                                <a href="/check" class="waves-effect {{ request()->is("check") || request()->is("check/*") ? "mm active" : "" }}">
                                    <i class="dripicons-to-do"></i> <span> Raporti Checkin/Out </span>
                                </a>
                            </li>

                            <li>
                                <a href="/overtime" class="waves-effect {{ request()->is("overtime") || request()->is("overtime/*") ? "mm active" : "" }}">
                                    <i class="dripicons-to-do"></i> <span> Raporti Overtime </span>
                                </a>
                            </li>
                            <li class="d-none">
                                <a href="/sheet-report" class="waves-effect {{ request()->is("sheet-report") || request()->is("sheet-report/*") ? "mm active" : "" }}">
                                    <i class="dripicons-to-do"></i> <span> Sheet Report </span>
                                </a>
                            </li>

                            <li class="">
                                <a href="/attendance" class="waves-effect {{ request()->is("attendance") || request()->is("attendance/*") ? "mm active" : "" }}">
                                    <i class="ti-calendar"></i> <span> Check In/Out </span>
                                </a>
                            </li>
                            <!-- <li class="">
                                <a href="/latetime" class="waves-effect {{ request()->is("latetime") || request()->is("latetime/*") ? "mm active" : "" }}">
                                    <i class="dripicons-warning"></i><span> Late Time </span>
                                </a>
                            </li> -->
                            <li class="">
                                <a href="/leave" class="waves-effect {{ request()->is("leave") || request()->is("leave/*") ? "mm active" : "" }}">
                                    <i class="dripicons-backspace"></i> <span> Pushimet </span>
                                </a>
                            </li>
                            <li class="">
                                <a href="/holiday" class="waves-effect {{ request()->is("holiday") || request()->is("holiday/*") ? "mm active" : "" }}">
                                    <i class="ti-calendar"></i> <span> Festat </span>
                                </a>
                            </li>
                            <!-- <li class="">
                                <a href="/overtime" class="waves-effect {{ request()->is("overtime") || request()->is("overtime/*") ? "mm active" : "" }}">
                                    <i class="dripicons-alarm"></i> <span> Over Time </span>
                                </a>
                            </li> -->
                            <li class="menu-title">Tools</li>
                            <li class="">
                                <a href="{{ route("finger_device.index") }}" class="waves-effect {{ request()->is("finger_device") || request()->is("finger_device/*") ? "mm active" : "" }}">
                                    <i class="fas fa-fingerprint"></i> <span> Paisjet </span>
                                </a>
                            </li>

                        </ul>
                        @else
                        <ul class="metismenu" id="side-menu">
                            <li class="menu-title">Main</li>
                            <li class="">
                                <a href="{{route('employee')}}" class="waves-effect {{ request()->is("employee") || request()->is("employee/*") ? "mm active" : "" }}">
                                    <i class="ti-home"></i> <span> Fillimi </span>
                                </a>
                            </li>
                        </ul>
                        @endif
                    </div>
                    <!-- Sidebar -->
                    <div class="clearfix"></div>

                </div>
                <!-- Sidebar -left -->

            </div>
            <!-- Left Sidebar End -->
