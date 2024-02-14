<script src="{{asset('vendors/js/bootstrap.min.js')}}"></script>
<script src="{{asset('vendors/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('vendors/js/jquery.min.js')}}"></script>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="{{asset('js/script.js')}}"></script>
<script>

    $('#myTable').DataTable();

    function temporaryBanUser(id)
    {
        data_id = $(id).attr('data-id');                                                                                                                 
        $.ajax({
            url: "{{ route('temporaryBan') }}",
            method: 'GET',
            data: {
                id: data_id

            },
            dataType: 'json',
            success: function (data) {
                $('table').load(location.href +' table');
            }
        })
    }
    function permanentBanUser(id)
    {
        data_id = $(id).attr('data-id');                                                                                                                           
        $.ajax({
            url: "{{ route('permanentBan') }}",
            method: 'GET',
            data: {
                id: data_id

            },
            dataType: 'json',
            success: function (data) {
                $('table').load(location.href +' table');
            }
        })
    }
    function unbanUser(id)
    {
        data_id = $(id).attr('data-id');                                                                                                                           
        $.ajax({
            url: "{{ route('unban') }}",
            method: 'GET',
            data: {
                id: data_id

            },
            dataType: 'json',
            success: function (data) {
                $('table').load(location.href +' table');
            }
        })
    }

    // Report
    function temporaryBan(id)
    {
        user_id = $(id).attr('report-id');                                                                                                                             
        $.ajax({
            url: "{{ route('temporaryBan') }}",
            method: 'GET',
            data: {
                id: user_id

            },
            dataType: 'json',
            success: function (data) {
                $('table').load(location.href +' table');
            }
        })
    }
    function permanentBan(id)
    {
        user_id = $(id).attr('report-id');                                                                                                                             
        $.ajax({
            url: "{{ route('permanentBan') }}",
            method: 'GET',
            data: {
                id: user_id

            },
            dataType: 'json',
            success: function (data) {
                $('table').load(location.href +' table');
            }
        })
    }
    function unban(id)
    {
        user_id = $(id).attr('unban-id');                                                                                                                           
        $.ajax({
            url: "{{ route('unban') }}",
            method: 'GET',
            data: {
                id: user_id

            },
            dataType: 'json',
            success: function (data) {
                $('table').load(location.href +' table');
            }
        })
    }

    function viewActivity(id)
    {                                                                                                              
        activity_id = $(id).attr('activity-id');                                                                                                                 
        $.ajax({
            url: "{{ route('viewActivity') }}",
            method: 'GET',
            data: {
                id: activity_id
            },
            dataType: 'json',
            success: function (data) {
                $('table').load(location.href +' table');
            }
        })
    }
    function hideActivity(id)
    {                                                                                                               
        activity_id = $(id).attr('activity-id');                                                                                                                            
        $.ajax({
            url: "{{ route('hideActivity') }}",
            method: 'GET',
            data: {
                id: activity_id
            },
            dataType: 'json',
            success: function (data) {
                $('table').load(location.href +' table');
            }
        })
    }

    // Comment
    function viewComment(id)
    {                                                                                                              
        comment_id = $(id).attr('comment-id');                                                                                                                 
        $.ajax({
            url: "{{ route('viewComment') }}",
            method: 'GET',
            data: {
                id: comment_id
            },
            dataType: 'json',
            success: function (data) {
                $('table').load(location.href +' table');
            }
        })
    }
    function hideComment(id)
    {                                                                                                                
        comment_id = $(id).attr('comment-id');                                                                                                                            
        $.ajax({
            url: "{{ route('hideComment') }}",
            method: 'GET',
            data: {
                id: comment_id
            },
            dataType: 'json',
            success: function (data) {
                $('table').load(location.href +' table');
            }
        })
    }

    // search
    // $("#searchInput").on("keyup", function() {
    //     var value = $(this).val().toLowerCase();
    //     $("#searchTable tr").filter(function() {
    //     $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
    //     });
    // });
    
</script>