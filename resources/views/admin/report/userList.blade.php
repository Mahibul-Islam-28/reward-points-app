@extends('admin.layout.main')

@section('title')
User Report list
@endsection

@section('content')
<section class="user-report-section">
    <div class="container py-5">

        <table class="table table-bordered table-striped table-responsive" id="myTable">
            <thead>
                <tr>
                    <th rowspan="2">SL</th>
                    <th colspan="3">Reporter Person Information</th>
                    <th rowspan="2">Cause</th>
                    <th rowspan="2">Description</th>
                    <th colspan="3">Reported Person Information</th>
                    <th rowspan="2">Report Date</th>
                    <th rowspan="2">Status</th>
                    <th rowspan="2">Action</th>
                </tr>
                <tr>
                    <th>Name</th>
                    <th>Photo</th>
                    <th>Email</th>
                    <th>Name</th>
                    <th>Photo</th>
                    <th>Email</th>
                </tr>
            </thead>
            
            <tbody>

                @foreach($reports as $key => $value)
                <tr>
                    <td>{{$value->key + 1}}</td>
                    <td>{{$value->user->full_name}}</td>
                    <td><img src="{{asset('')}}{{$value->user->profile_image}}" alt="img"></td>
                    <td>{{$value->user->email}}</td>
                    <td>{{$value->subject}}</td>
                    <td>{{$value->description}}</td>
                    <td>{{$value->report->full_name}}</td>
                    <td><img src="{{asset('')}}{{$value->report->profile_image}}" alt="img"></td>
                    <td>{{$value->report->email}}</td>
                    <td>{{$value->created_at}}</td>
                    <td>
                        @if($value->report->status == 1)
                        <span class="text-success">Active</span>
                        @elseif($value->report->status == 2)
                        <span class="text-warning">Temporary Banned</span>
                        @elseif($value->report->status == 3)
                        <span class="text-danger">Permanently Banned</span>
                        @else
                        <span class="text-info">Not Verified</span>
                        @endif
                    </td>
                    <td>
                        @if($value->report->status == 1)
                        <button class="temporary-btn" id="temporary-btn-{{$value->id}}" data-id="{{$value->id}}"
                            report-id="{{$value->report_id}}" onclick="temporaryBan(this)">Temporary Ban</button>
                        <button class="permanent-btn mt-1" id="permanent-btn-{{$value->id}}" data-id="{{$value->id}}"
                            report-id="{{$value->report_id}}" onclick="permanentBan(this)">Permanently Ban</button>
                        @elseif($value->report->status == 2)
                        <button class="unblock-btn" id="unban-btn-{{$value->id}}" data-id="{{$value->id}}"
                            unban-id="{{$value->report_id}}" onclick="unban(this)">Unban</button>
                        <button class="permanent-btn mt-1" id="permanent-btn-{{$value->id}}" data-id="{{$value->id}}"
                            report-id="{{$value->report_id}}" onclick="permanentBan(this)">Permanently Ban</button>
                        @elseif($value->report->status == 3)
                        <button class="unblock-btn" id="unban-btn-{{$value->id}}" data-id="{{$value->id}}"
                            unban-id="{{$value->report_id}}" onclick="unban(this)">Unban</button>
                        <button class="temporary-btn" id="temporary-btn-{{$value->id}}" data-id="{{$value->id}}"
                            report-id="{{$value->report_id}}" onclick="temporaryBan(this)">Temporary Ban</button>
                        @else
                        <button class="unblock-btn" id="unban-btn-{{$value->id}}" data-id="{{$value->id}}"
                            unban-id="{{$value->report_id}}" onclick="unban(this)">Unban</button>
                        <button class="temporary-btn" id="temporary-btn-{{$value->id}}" data-id="{{$value->id}}"
                            report-id="{{$value->report_id}}" onclick="temporaryBan(this)">Temporary Ban</button>
                        <button class="permanent-btn mt-1" id="permanent-btn-{{$value->id}}" data-id="{{$value->id}}"
                            report-id="{{$value->report_id}}" onclick="permanentBan(this)">Permanently Ban</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</section>
@endsection
