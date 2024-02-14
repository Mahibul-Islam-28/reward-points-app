<section class="admin-header">

<a class="navbar-brand" href="{{route('dashboard')}}" style="font-size: 30px;">
	<img src="{{asset('')}}images/logo/logo.png">
</a>

@if (session()->has('admin'))
<ul class="navbar-nav ms-auto" style="font-weight: bold;">
        <li class="nav-item px-2 {{Request::path() === 'wxadmin' ? 'active' : '' }}" id="home">
            <a class="nav-link" href="{{route('dashboard')}}">Dashboard</a>
        </li>
        <li class="nav-item px-2 {{Request::path() === 'wxadmin/userManage' ? 'active' : '' }}">
            <a class="nav-link" href="{{route('userManage')}}">User Manage</a>
        </li>
        <li class="nav-item px-2 {{Request::path() === 'wxadmin/deletedUser' ? 'active' : '' }}">
            <a class="nav-link" href="{{route('deletedUser')}}">Deleted User</a>
        </li>
        <li class="nav-item px-2 {{Request::path() === 'wxadmin/report/userList' ? 'active' : '' }}">
            <a class="nav-link" href="{{route('userReportList')}}">User Report</a>
        </li>
        <li class="nav-item px-2 {{Request::path() === 'wxadmin/report/activityList' ? 'active' : '' }}">
            <a class="nav-link" href="{{route('activityReportList')}}">Activity Report</a>
        </li>
        <li class="nav-item px-2 {{Request::path() === 'wxadmin/report/commentList' ? 'active' : '' }}">
            <a class="nav-link" href="{{route('commentReportList')}}">Comment Report</a>
        </li>
        <li class="nav-item px-2 {{Request::path() === 'wxadmin/notification/create' ? 'active' : '' }}">
            <a class="nav-link" href="{{route('notificationCreate')}}">Notification Create</a>
        </li>
        <li class="nav-item px-2 {{Request::path() === 'aboutUs' ? 'active' : '' }}">
            <a class="nav-link" href="{{route('adminLogout')}}">LOGOUT</a>
        </li>
</ul>
@endif
</section>