@extends('admin.layout.main')

@section('title')
Activity Report list
@endsection

@section('content')
<section class="user-manage-section">
    <div class="container p-5">

        <table class="table table-bordered table-striped table-responsive" id="myTable">
            <thead>
                <tr>
                    <th rowspan="2">SL</th>
                    <th colspan="2">Reporter Person Information</th>
                    <th rowspan="2">Content</th>
                    <th colspan="2">Reported Person Information</th>
                    <th rowspan="2">Cause</th>
                    <th rowspan="2">Description</th>
                    <th rowspan="2">Report Date</th>
                    <th rowspan="2">Status</th>
                    <th rowspan="2">Action</th>
                </tr>
                <tr>
                    <th>Name</th>
                    <th>Photo</th>
                    <th>Name</th>
                    <th>Photo</th>
                </tr>
            </thead> 

            <tbody>
                @foreach($reports as $key => $value)
                <tr>
                    <td>{{$key + 1}}</td>
                    <td>{{$value->user->full_name}}</td>
                    <td><img src="{{asset('')}}{{$value->user->profile_image}}" alt="img"></td>
                    <td>{!! $value->activity->content !!}</td>
                    <td>{{$value->activity->full_name}}</td>
                    <td><img src="{{asset('')}}{{$value->activity->profile_image}}" alt="img"></td>
                    <td>{{$value->subject}}</td>
                    <td>{{$value->description}}</td>
                    <td>{{$value->created_at}}</td>
                    <td>
                        @if($value->activity->status == 1)
                        <span class="text-success">Active</span>
                        @else
                        <span class="text-danger">Hidden</span>
                        @endif
                    </td>
                    <td>
                        @if($value->activity->status == 0)
                        <button class="unblock-btn" id="view-btn-{{$value->id}}" data-id="{{$value->id}}"
                            activity-id="{{$value->activity_id}}" onclick="viewActivity(this)">View</button>
                        @else
                        <button class="permanent-btn mt-1" id="hide-btn-{{$value->id}}" data-id="{{$value->id}}"
                            activity-id="{{$value->activity_id}}" onclick="hideActivity(this)">Hide</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
        @endsection
