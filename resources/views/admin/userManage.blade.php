@extends('admin.layout.main')

@section('title')
User Manage
@endsection

@section('css')
</script>
@endsection

@section('content')


<section class="user-manage-section">
    <div class="container p-5">
        <table class="table table-bordered table-striped table-responsive pt-3" id="myTable">
            <thead>
                <tr>
                    <th>SL</th>
                    <th>User Id</th>
                    <th>User Name</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Otp Verify</th>
                    <th>Email Verify</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>

                @foreach($users as $index => $value)
                <tr>
                    <td>{{$index + 1}}</td>
                    <td>{{$value->id}}</td>
                    <td>{{$value->user_name}}</td>
                    <td>{{$value->full_name}}</td>
                    <td>{{$value->mobile_no}}</td>
                    <td>{{$value->email}}</td>
                    <td>
                        @if($value->otp_verify == 1)
                        <span class="text-success">Verified</span>
                        @else
                        <span class="text-danger">Not Verified</span>
                        @endif
                    </td>
                    <td>
                        @if($value->email_verify == 1)
                        <span class="text-success">Verified</span>
                        @else
                        <span class="text-danger">Not Verified</span>
                        @endif
                    </td>
                    <td>
                        @if($value->status == 1)
                        <span class="text-success">Active</span>
                        @elseif($value->status == 2)
                        <span class="text-warning">Temporary Banned</span>
                        @elseif($value->status == 3)
                        <span class="text-danger">Permanently Banned</span>
                        @else
                        <span class="text-info">Not Verified</span>
                        @endif
                    </td>
                    <td>
                        @if($value->status == 1)
                        <button class="temporary-btn" id="temporary-btn-{{$value->id}}" data-id="{{$value->id}}"
                            onclick="temporaryBanUser(this)">Temporary Ban</button>
                        <button class="permanent-btn mt-1" id="permanent-btn-{{$value->id}}" data-id="{{$value->id}}"
                            onclick="permanentBanUser(this)">Permanently Ban</button>

                        @elseif($value->status == 2)
                        <button class="unblock-btn" id="unban-btn-{{$value->id}}" data-id="{{$value->id}}"
                            onclick="unbanUser(this)">Unban</button>
                        <button class="permanent-btn mt-1" id="permanent-btn-{{$value->id}}" data-id="{{$value->id}}"
                            onclick="permanentBanUser(this)">Permanently Ban</button>

                        @elseif($value->status == 3)
                        <button class="unblock-btn" id="unban-btn-{{$value->id}}" data-id="{{$value->id}}"
                            onclick="unbanUser(this)">Unban</button>
                        <button class="temporary-btn" id="temporary-btn-{{$value->id}}" data-id="{{$value->id}}"
                            onclick="temporaryBanUser(this)">Temporary Ban</button>

                        @else
                        <button class="unblock-btn" id="unban-btn-{{$value->id}}" data-id="{{$value->id}}"
                            onclick="unbanUser(this)">Unban</button>
                        <button class="temporary-btn" id="temporary-btn-{{$value->id}}" data-id="{{$value->id}}"
                            onclick="temporaryBanUser(this)">Temporary Ban</button>
                        <button class="permanent-btn mt-1" id="permanent-btn-{{$value->id}}" data-id="{{$value->id}}"
                            onclick="permanentBanUser(this)">Permanently Ban</button>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
    </div>
</section>
@endsection
