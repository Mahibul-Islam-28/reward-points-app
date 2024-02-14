<script src="{{asset('vendors/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('vendors/js/jquery.min.js')}}"></script>
<script src="{{asset('')}}vendors/js/jquery.lightbox.min.js"></script>
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jscroll/2.4.1/jquery.jscroll.min.js"></script> -->
<script src="{{asset('js/script.js')}}"></script>
<script>

    // back to top button
    $(document).ready(function() {
        var progressPath = document.querySelector('.progress-wrap path');
        var pathLength = progressPath.getTotalLength();

        progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
        progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
        progressPath.style.strokeDashoffset = pathLength;
        progressPath.getBoundingClientRect();
        progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';

        var updateProgress = function() {
            var scroll = $(window).scrollTop();
            var height = $(document).height() - $(window).height();
            var progress = pathLength - (scroll * pathLength / height);
            progressPath.style.strokeDashoffset = progress;
        }

        updateProgress();
        $(window).scroll(updateProgress);

        var offset = 50;
        var duration = 50;

        jQuery(window).on('scroll', function() {
            if(jQuery(this).scrollTop() > offset) {
                jQuery('.progress-wrap').addClass('active-progress');
            } else {
                jQuery('.progress-wrap').removeClass('active-progress');
            }
        });

        jQuery('.progress-wrap').on('click', function(event) {
            event.preventDefault();
            jQuery('html, body').animate({scrollTop: 0}, duration);
            return false;
        })
    });

    // light box
    $('.gallery a').lightbox(); 


    $('#activity-form').on('submit', function(event){
        event.preventDefault();

        $.ajax({
            url:"{{ url('activityStore') }}",
            method:"POST",
            data:new FormData(this),
            dataType:'JSON',
            contentType: false, 
            cache: false,
            processData: false,
            success:function(data)
            {
                $('#debugBox').remove();
                $('#activityContent').val('');
                $('#emotion').val('');
                $('#image').val('');
                $('#anonymous').prop("checked", false);
                $('#activity-feed').load(location.href +' #activity-feed');
                $('#current_count').html(0);
                $('#score-card').load(location.href +' #score-card');
                
            }
        })
    });


    // Lazy Loading
    // $('ul.pagination').hide();
    // $(function() {
    //     $('.activity-feed').jscroll({
    //         autoTrigger: true,
    //         padding: 0,
    //         nextSelector: '.pagination li.active + li a',
    //         contentSelector: 'div.activity-feed',
    //         callback: function() {
    //             $('ul.pagination').remove();
    //         }
    //     });
    // });



    $("#activityContent").bind("paste", function(e){

        var url = e.originalEvent.clipboardData.getData('Text');

        $.ajax({
            url: "{{ route('linkPreview') }}",
            method: 'GET',
            data: {
                url: url
            },
            dataType: 'json',
            success: function (data) {
                newUrl = url.indexOf('http') !== 0 ? "https://url" : url;

                result = '<div class="debugBox"> <button id="x" onclick="closePreview()">X</button><div class="debug-box" id="debugBox" onclick="openLink(this);" data-link="'+ 
                newUrl +'"><img src="'+data['image']+'" width="100%"><div class="text-wrapper"><p><strong>'+data['title']+
                '</strong></p><p class="description">'+data['description']+'</p></div></div></div>';
                $('#debugBox').remove();
                $('#linkPreview').append(result);
                $('#linkPreview').show();
            }
        });
    })

    function openLink(source) {
               openInNewTab(source.getAttribute('data-link'));
    }

    function openInNewTab(url) {
        var win = window.open(url, '_blank');
        win.focus();
    }


    // Activity
    // function activitySave(id) {
    //     let content = $("textarea[name=content]").val();
    //     let emotion = $("select[name=emotion]").val();

    //     if($('#anonymous').prop("checked") == true){
    //         anonymous = 1;
    //     }
    //     else if($('#anonymous').prop("checked") == false){
    //         anonymous = 0;
    //     }

    //     $.ajax({
    //         url: "{{ route('activityStore') }}",
    //         method: 'GET',
    //         data: {
    //             files: files
    //         },
    //         dataType: 'json',
    //         success: function (data) {
    //             $('#activityContent').val('');
    //             $('#emotion').val('');
    //             $('#anonymous').prop("checked", false);
    //             $('#activity-feed').load(location.href +' #activity-feed');
    //         }
    //     })
    // }

    function activityEdit(id) {
        data_id = $(id).attr('data-id');
        $.ajax({
            url: "{{ route('activityEdit') }}",
            method: 'GET',
            data: {
                id: data_id
            },
            dataType: 'json',
            success: function (data) {
                images = '';
                activity = '#activity-' + data_id;
                $(activity).append(data['content']);
                meta = '#activity-meta-' + data_id;
                edit = '#edit-' + data_id;
                ancor = '#activity-content-' + data_id; 
                button = '<div class="edit" id="saveEdit-' + data_id + '"><button data-id="' + data_id +
                    '" type="submit" onclick="activityUpdate(this)">Save</button></div> <div id="cancelEdit-' +
                    data_id + '" class="edit"><button data-id="' + data_id +
                    '" onclick="cancelEdit(this)">Cancel</button></div> </form>';
                if(data['images'] != null)
                {
                    jQuery.each(data['images'], function(index, item) {
                    images += '<div class="col-md-2 col-6"><img id="deleteImage-'+index+'" src="http://localhost/wexprez_api/uploads/activity/'+item+'"/></div> <a type="button" data-id="' + data_id +
                        '" class="btn-close" index="'+ index +'" image="'+ item +'" id="crossIcon-' + index + '" onclick="imageDelete(this)" aria-label="Close"></a>';
                    });
                    images = '<div class="row" id="edit-image-'+data_id+'">'+images+'</div>';
                    image = '#activity-image-' + data_id;
                    $(image).hide();
                }
                $(meta).append(button);
                $(activity).append(images);
                $(edit).hide();
                $(ancor).hide();
            }
        })
    }
    function activityHide(id) {
        activity_id = $(id).attr('data-id');

        $.ajax({
            url: "{{ route('activityHide') }}",
            method: 'GET',
            data: {
                activity_id: activity_id
            },
            dataType: 'json',
            success: function (data) {
                btn = '#hideBtn-' + activity_id;
                $(btn).html('Hidden');
            }
        })
    }
    function activityShow(id) {
        activity_id = $(id).attr('data-id');

        $.ajax({
            url: "{{ route('activityShow') }}",
            method: 'GET',
            data: {
                activity_id: activity_id
            },
            dataType: 'json',
            success: function (data) {
                $('#activity-feed').load(location.href +' #activity-feed');
            }
        })
    }

    function activityDelete(id) {
        activity_id = $(id).attr('data-id');

        $.ajax({
            url: "{{ route('activityDelete') }}",
            method: 'GET',
            data: {
                activity_id: activity_id
            },
            dataType: 'json',
            success: function (data) {
                btn = '#delete-' + activity_id;
                $(btn).html('Deleted');
                $('#activity-feed').load(location.href +' #activity-feed');
            }
        })
    }
    
    function imageDelete(id) {
        activity_id = $(id).attr('data-id');
        image = $(id).attr('image');
        index = $(id).attr('index');
        $.ajax({
            url: "{{ route('imageDelete') }}",
            method: 'GET',
            data: {
                id: activity_id,
                image: image
            },
            dataType: 'json',
            success: function (data) {
                icon = '#crossIcon-' + index;
                image = '#deleteImage-' + index;
                $(image).hide();
                $(icon).hide();
            }
        })
    }

    
    function activityShare(id) {
        let text = "Are you want to share?";
        if (confirm(text) == true) {
            $.ajax({
                url: "{{ route('activityShare') }}",
                method: 'GET',
                data: {
                    activity_id: id
                },
                dataType: 'json',
                success: function (data) {
                    $('#activity-feed').load(location.href +' #activity-feed');
                }
            })
        } else {
            text = "You canceled!";
        }
        
    }

    // function activityUpdate(id) {
    //     activity_id = $(id).attr('data-id');
    //     let content = $("textarea[name=edit-content]").val();

    //     $.ajax({
    //         headers: {
    //             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    //         },
    //         url: "{{ route('activityUpdate') }}",
    //         method: 'POST',
    //         data: {
    //             id: activity_id,
    //             content: content,
    //         },
    //         dataType: 'json',
    //         success: function (data) {

    //             newContent = '#activity-' + data_id;
    //             form = '#edit-activity-' + data_id;
    //             ancor = '#activity-content-' + data_id;
    //             edit = '#edit-' + data_id;
    //             save = '#saveEdit-' + data_id;
    //             cancel = '#cancelEdit-' + data_id;
    //             $(ancor).show().html(data);
    //             $(edit).show();
    //             $(save).remove();
    //             $(cancel).remove();
    //             $(form).remove();
    //             $('#activity-feed').load(location.href +' #activity-feed');
    //         }
    //     })
    // }

    function activityUpdate(id) {
        data_id = $(id).attr('data-id');
        form = 'edit-activity-' + data_id;
        formData = new FormData(document.getElementById(form));


        $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            url:"{{ url('activityUpdate') }}",
            method:"POST",
            data: formData,
            dataType:'JSON',
            contentType: false,
            cache: false,
            processData: false,
            success:function(data)
            {
                newContent = '#activity-' + data_id;
                form = '#edit-activity-' + data_id;
                ancor = '#activity-content-' + data_id;
                edit = '#edit-' + data_id;
                save = '#saveEdit-' + data_id;
                cancel = '#cancelEdit-' + data_id;
                $(ancor).show().html(data);
                $(edit).show();
                $(save).remove();
                $(cancel).remove();
                $(form).remove();
                $('#activity-feed').load(location.href +' #activity-feed');
                $('#score-card').load(location.href +' #score-card');
            }
        })
    }

    function cancelEdit(id) {
        data_id = $(id).attr('data-id');
        form = '#edit-activity-' + data_id;
        ancor = '#activity-content-' + data_id;
        save = '#saveEdit-' + data_id;
        cancel = '#cancelEdit-' + data_id;
        edit = '#edit-' + data_id;
        image = '#activity-image-' + data_id;
        eImage = '#edit-image-' + data_id;
        
        $(ancor).show();
        $(edit).show();
        $(image).show();
        $(form).remove();
        $(save).remove();
        $(cancel).remove();
        $(eImage).remove();
    }

    

    //  comment
    function commentCreate(id) {
        data_id = $(id).attr('data-id');
        parent_id = $(id).attr('parent-id');
        
        
        $.ajax({
            url: "{{ route('comment') }}",
            method: 'GET',
            data: {
                id: data_id,
                parent_id: parent_id
            },
            dataType: 'json',
            success: function (data) {

                comments = '#comment-' + parent_id;
                meta = '#comment-form-' + parent_id;
                if(parent_id == 0)
                {
                    meta = '#comment-form-' + data_id;
                    comments = '#comment-' + data_id;
                }

                $(meta).append(data);
                
                button = '<div class="save" id="saveComment-' + data_id + '"><button parent-id="'+ parent_id +'" data-id="' + data_id +
                    '" type="submit" class="save" onclick="commentSave(this)">Save</button></div> <div id="cancelcomment-' +
                    data_id + '" class="cancel"><button data-id="' + data_id +
                    '" onclick="cancelComment(this)">Cancel</button></div> </form>';

                $(meta).append(button);
                $(comments).hide();
            }
        })
    }
    function replyCreate(id) {
        data_id = $(id).attr('data-id');
        parent_id = $(id).attr('parent-id');
        
        form = '#replyForm-' + parent_id;
        save = '#saveReply-' + parent_id;
        cancel = '#cancelReply-' + parent_id;

        $(form).remove();
        $(save).remove();
        $(cancel).remove();

        $.ajax({
            url: "{{ route('comment') }}",
            method: 'GET',
            data: {
                id: data_id,
                parent_id: parent_id
            },
            dataType: 'json',
            success: function (data) {

                reply = '#reply-' + parent_id;
                meta = '#reply-form-' + parent_id;

                $(meta).append(data);
                
                button = '<div class="save" id="saveReply-' + parent_id + '"><button parent-id="'+ parent_id +'" data-id="' + data_id +
                    '" type="submit" class="save" onclick="replySave(this)">Save</button></div> <div id="cancelReply-' +
                    parent_id + '" class="cancel"><button data-id="' + parent_id +'" activity-id="' + 
                    data_id +'" onclick="cancelReply(this)">Cancel</button></div> </form>';

                $(meta).append(button);
                $(reply).toggle();
            }
        })
    }
    function commentSave(id) {
        activity_id = $(id).attr('data-id');
        parent_id = $(id).attr('parent-id');
        
        let comment = $("textarea[name=comment-"+activity_id+"]").val();
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('commentSave') }}",
            method: 'POST',
            data: {
                activity_id: activity_id,
                parent_id: parent_id,
                comment: comment,
            },
            dataType: 'json',
            success: function (data) {
        

                form = '#commentForm-' + activity_id;
                form2 = '#replyForm--' + parent_id;
                
                save = '#saveComment-' + activity_id;
                cancel = '#cancelcomment-' + activity_id;
                cancel2 = '#cancelReply-' + parent_id;
                comments = '#comment-' + activity_id;

                commentInner = '#activity-comment-'+ activity_id;

                $(comments).show();
                $(form).remove();
                $(form2).remove();
                $(save).remove();
                $(cancel).remove();
                $(cancel2).remove();

                content = '<div class="comment-inner" id="comment-inner-'+data['id']+'"><p id="comment-content-'+
                data['id']+'">"'+data['comment']+'"</p></div>';

                $(commentInner).append(content);
                $('#activity-feed').load(location.href +' #activity-feed');
                $('#score-card').load(location.href +' #score-card');

            }
        })
    }
    function replySave(id) {
        activity_id = $(id).attr('data-id');
        parent_id = $(id).attr('parent-id');

        let comment = $("textarea[name=comment-"+activity_id+"]").val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('commentSave') }}",
            method: 'POST',
            data: {
                activity_id: activity_id,
                parent_id: parent_id,
                comment: comment,
            },
            dataType: 'json',
            success: function (data) {
                form = '#replyForm-' + parent_id;
                save = '#saveReply-' + parent_id;
                cancel = '#cancelReply-' + parent_id;
                reply = '#reply-' + parent_id;

                commentInner = '#activity-comment-'+ activity_id;

                $(reply).show();
                $(form).remove();
                $(save).remove();
                $(cancel).remove();

                aMeta = '#activity-comment-'+ activity_id;
                cMeta = '#activity-comment-'+ parent_id;

                content = '<div class="comment-inner" id="comment-inner-'+data['id']+'"><p id="comment-content-'+
                data['id']+'">"'+data['comment']+'"</p></div>';

                $(commentInner).append(content);

                $(cMeta).load(content).fadeIn("slow");
                $('#activity-feed').load(location.href +' #activity-feed');
                $('#score-card').load(location.href +' #score-card');
            }
        })
    }

    function cancelComment(id) {
        data_id = $(id).attr('data-id');
        form = '#commentForm-' + data_id;
        save = '#saveComment-' + data_id;
        cancel = '#cancelcomment-' + data_id;
        comments = '#comment-' + data_id;
        $(comments).show();
        $(form).remove();
        $(save).remove();
        $(cancel).remove();
    }
    function cancelReply(id) {
        data_id = $(id).attr('data-id');
        activity_id = $(id).attr('activity-id');
        form = '#replyForm-' + data_id;
        save = '#saveReply-' + data_id;
        cancel = '#cancelReply-' + data_id;
        reply = '#reply-' + data_id;

        $(reply).toggle();
        $(form).remove();
        $(save).remove();
        $(cancel).remove();
    }
    
    function commentEdit(id) {
        data_id = $(id).attr('data-id');
        parent_id = $(id).attr('parent-id');


        $.ajax({
            url: "{{ route('commentEdit') }}",
            method: 'GET',
            data: {
                id: data_id,
            },
            dataType: 'json',
            success: function (data) {

                comment = '#comment-inner-' + data_id;
                OldComment = '#comment-content-' + data_id;
                btn = '#edit-commentBtn-' + data_id;
                $(comment).append(data);
                meta = '#comment-meta-' + data_id;
                button = '<div class="save" id="updateComment-' + data_id + '"><button parent-id="'+ parent_id +'" data-id="' + data_id +
                    '" type="submit" onclick="commentUpdate(this)">Save</button></div> <div id="cancelcommentEdit-' +
                    data_id + '" class="cancel"><button data-id="' + data_id +
                    '" onclick="cancelCommentEdit(this)">Cancel</button></div> </form>';
                $(meta).append(button);
                $(OldComment).hide();
                $(btn).hide();

            }
        })
    }

    function cancelCommentEdit(id) {
        data_id = $(id).attr('data-id');
        form = '#edit-commentForm-' + data_id;
        update = '#updateComment-' + data_id;
        cancel = '#cancelcommentEdit-' + data_id;
        comment = '#comment-content-' + data_id;
        btn = '#edit-commentBtn-' + data_id;

        $(comment).show();
        $(btn).show();
        $(form).remove();
        $(update).remove();
        $(cancel).remove();
    }

    function commentUpdate(id) {
        comment_id = $(id).attr('data-id');
        let comment = $("textarea[name=edit-comment]").val();

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ route('commentUpdate') }}",
            method: 'POST',
            data: {
                id: comment_id,
                comment: comment,
            },
            dataType: 'json',
            success: function (data) {

                form = '#edit-commentForm-' + data_id;
                update = '#updateComment-' + data_id;
                cancel = '#cancelcommentEdit-' + data_id;
                oldContent = '#comment-content-' + data_id;

                $(oldContent).show().html(data);
                $(update).remove();
                $(cancel).remove();
                $(form).remove();

                $('#activity-feed').load(location.href +' #activity-feed');
                $('#score-card').load(location.href +' #score-card');
            }
        })
    }

    function commentDelete(id) {
        comment_id = $(id).attr('data-id');

        $.ajax({
            url: "{{ route('commentDelete') }}",
            method: 'GET',
            data: {
                comment_id: comment_id
            },
            dataType: 'json',
            success: function (data) {
                $('#score-card').load(location.href +' #score-card');

            }
        })
    }
    
    // Activity Report
    function activityReport(id) {
        activity_id = $(id).attr('data-id');
        
        $.ajax({
            url: "{{ route('activityReport') }}",
            method: 'GET',
            data: {
                activity_id: activity_id
            },
            dataType: 'json',
            success: function (data) {

                report = '#report-' + activity_id;
                form = '#report-form-' + activity_id;

                $(form).append(data);
                $(report).hide();

            }
        })
    }

    function activityReportSave(id) {
        activity_id = $(id).attr('data-id');
        let subject = $("select[name=subject]").val();
        let description = $("textarea[name=description]").val();
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            
            url: "{{ route('activityReportSave') }}",
            method: 'POST',
            data: {
                subject: subject,
                description: description,
                activity_id: activity_id
            },
            dataType: 'json',
            success: function (data) {
                
                report = '#report-' + activity_id;
                form = '#reportForm-' + activity_id;

                $(form).remove();
                $(report).show();

            }
        });
    }

    function activityReportCancel(id) {
        activity_id = $(id).attr('data-id');
        
        report = '#report-' + activity_id;
        form = '#reportForm-' + activity_id;

        $(report).show();
        $(form).remove();
    }

    // Comment Report
    function commentReport(id) {
        comment_id = $(id).attr('data-id');
        activity_id = $(id).attr('activity-id');
        
        $.ajax({
            url: "{{ route('commentReport') }}",
            method: 'GET',
            data: {
                comment_id: comment_id,
                activity_id: activity_id
            },
            dataType: 'json',
            success: function (data) {
                report = '#commentReport-' + comment_id;
                form = '#comment-report-form-' + comment_id;
                
                $(report).hide();
                $(form).append(data);

            }
        })
    }
    function commentReportSave(id) {
        comment_id = $(id).attr('data-id');
        activity_id = $(id).attr('activity-id');
        let subject = $("select[name=subject]").val();
        let description = $("textarea[name=description]").val();
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            
            url: "{{ route('commentReportSave') }}",
            method: 'POST',
            data: {
                subject: subject,
                description: description,
                activity_id: activity_id,
                comment_id: comment_id,
            },
            dataType: 'json',
            success: function (data) {
                
                report = '#commentReport-' + comment_id;
                form = '#comment-report-form-' + comment_id;
                
                $(form).remove();
                $(report).show();

            }
        });
    }

    function commentReportCancel(id) {
        comment_id = $(id).attr('data-id');
        activity_id = $(id).attr('activity-id');
        
        report = '#report-' + comment_id;
        btn = '#commentReport-' + comment_id;
        form = '#commentReportForm-' + comment_id;

        $(report).show();
        $(btn).show();
        $(form).remove();
    }


    // Vote Up
    function activityVoteUp(id) {
        activity_id = $(id).attr('data-id');

        $.ajax({
            url: "{{ route('activityVoteUp') }}",
            method: 'GET',
            data: {
                activity_id: activity_id,

            },
            dataType: 'json',
            success: function (data) {
                counter1 = '#activity-voteUp-'+activity_id;
                counter2 = '#activity-voteDown-'+activity_id;
                img1 = '#activity-voteUp-image-'+activity_id;
                img2 = '#activity-voteDown-image-'+activity_id;
                $(img1).attr("src", '{{asset("")}}'+data['img']);
                $(img2).attr("src", "{{asset("")}}images/reaction/voteDown-off.png");
                $(counter1).html(data['up']);
                $(counter2).html(data['down']);
                $('#score-card').load(location.href +' #score-card');
            }
        })
    }
    // Vote Down
    function activityVoteDown(id) {
        activity_id = $(id).attr('data-id');

        $.ajax({
            url: "{{ route('activityVoteDown') }}",
            method: 'GET',
            data: {
                activity_id: activity_id,

            },
            dataType: 'json',
            success: function (data) {
                counter1 = '#activity-voteUp-'+activity_id;
                counter2 = '#activity-voteDown-'+activity_id;
                img1 = '#activity-voteUp-image-'+activity_id;
                img2 = '#activity-voteDown-image-'+activity_id;
                $(img1).attr("src", "{{asset("")}}images/reaction/voteUp-off.png");
                $(img2).attr("src", '{{asset("")}}'+data['img']);
                $(counter1).html(data['up']);
                $(counter2).html(data['down']);
                $('#score-card').load(location.href +' #score-card');
            }
        })
    }

    // comment Vote Up
    function commentVoteUp(id) {
        comment_id = $(id).attr('data-id');

        $.ajax({
            url: "{{ route('commentVoteUp') }}",
            method: 'GET',
            data: {
                comment_id: comment_id,

            },
            dataType: 'json',
            success: function (data) {
                counter1 = '#comment-voteUp-'+comment_id;
                counter2 = '#comment-voteDown-'+comment_id;
                img1 = '#comment-voteUp-image-'+comment_id;
                img2 = '#comment-voteDown-image-'+comment_id;
                $(img1).attr("src", '{{asset("")}}'+data['img']);
                $(img2).attr("src", "{{asset("")}}images/reaction/voteDown-off.png");
                $(counter1).html(data['up']);
                $(counter2).html(data['down']);
                $('#score-card').load(location.href +' #score-card');
            }
        })
    }

    // comment vote down
    function commentVoteDown(id) {
        comment_id = $(id).attr('data-id');

        $.ajax({
            url: "{{ route('commentVoteDown') }}",
            method: 'GET',
            data: {
                comment_id: comment_id,

            },
            dataType: 'json',
            success: function (data) {
                counter1 = '#comment-voteUp-'+comment_id;
                counter2 = '#comment-voteDown-'+comment_id;
                img1 = '#comment-voteUp-image-'+comment_id;
                img2 = '#comment-voteDown-image-'+comment_id;
                $(img1).attr("src", "{{asset("")}}images/reaction/voteUp-off.png");
                $(img2).attr("src", '{{asset("")}}'+data['img']);
                $(counter1).html(data['up']);
                $(counter2).html(data['down']);
                $('#score-card').load(location.href +' #score-card');
            }
        })
    }

    // Real
    function activityReal(id) {
        activity_id = $(id).attr('data-id');


        $.ajax({
            url: "{{ route('activityReal') }}",
            method: 'GET',
            data: {
                activity_id: activity_id,

            },
            dataType: 'json',
            success: function (data) {
                counter1 = '#activity-real-'+activity_id;
                counter2 = '#activity-fake-'+activity_id;
                img1 = '#activity-real-image-'+activity_id;
                img2 = '#activity-fake-image-'+activity_id;
                $(img1).attr("src", '{{asset("")}}'+data['img']);
                $(img2).attr("src", "{{asset("")}}images/reaction/fake-off.png");
                $(counter1).html(data['real']);
                $(counter2).html(data['fake']);
                $('#score-card').load(location.href +' #score-card');
            }
        })
    }
    // Fake
    function activityFake(id) {
        activity_id = $(id).attr('data-id');

        $.ajax({
            url: "{{ route('activityFake') }}",
            method: 'GET',
            data: {
                activity_id: activity_id,

            },
            dataType: 'json',
            success: function (data) {
                
                counter1 = '#activity-real-'+activity_id;
                counter2 = '#activity-fake-'+activity_id;
                img1 = '#activity-real-image-'+activity_id;
                img2 = '#activity-fake-image-'+activity_id;
                $(img1).attr("src", "{{asset('')}}images/reaction/real-off.png");
                $(img2).attr("src", "{{asset('')}}"+data['img']);
                $(counter1).html(data['real']);
                $(counter2).html(data['fake']);
                $('#score-card').load(location.href +' #score-card');
            }
        })
    }

    // Comment Real
    function commentReal(id) {
        comment_id = $(id).attr('data-id');


        $.ajax({
            url: "{{ route('commentReal') }}",
            method: 'GET',
            data: {
                comment_id: comment_id,

            },
            dataType: 'json',
            success: function (data) {
                counter1 = '#comment-real-'+comment_id;
                counter2 = '#comment-fake-'+comment_id;
                img1 = '#comment-real-image-'+comment_id;
                img2 = '#comment-fake-image-'+comment_id;
                $(img1).attr("src", '{{asset("")}}'+data['img']);
                $(img2).attr("src", "{{asset("")}}images/reaction/fake-off.png");
                $(counter1).html(data['real']);
                $(counter2).html(data['fake']);
                $('#score-card').load(location.href +' #score-card');
            }
        })
    }
    // Comment Fake
    function commentFake(id) {
        comment_id = $(id).attr('data-id');

        $.ajax({
            url: "{{ route('commentFake') }}",
            method: 'GET',
            data: {
                comment_id: comment_id,

            },
            dataType: 'json',
            success: function (data) {
                
                counter1 = '#comment-real-'+comment_id;
                counter2 = '#comment-fake-'+comment_id;
                img1 = '#comment-real-image-'+comment_id;
                img2 = '#comment-fake-image-'+comment_id;
                $(img1).attr("src", "{{asset('')}}images/reaction/real-off.png");
                $(img2).attr("src", "{{asset('')}}"+data['img']);
                $(counter1).html(data['real']);
                $(counter2).html(data['fake']);
                $('#score-card').load(location.href +' #score-card');
            }
        })
    }

    // Vote List
    function activityVoteUpList(id)
    {
        activity_id = $(id).attr('data-id');

        $("#Modal-body li").remove();
        $("#Modal-body li").remove();
        $.ajax({
            url: "{{ route('activityVoteUpList') }}",
            method: 'GET',
            data: {
                activity_id: activity_id,

            },
            dataType: 'json',
            success: function (data) {
                if(data != "")
                {
                    result= '';
                    jQuery.each(data, function(index, item) {
                            result += '<li><img src="'+item["profile_image"]+'"><a>'
                            +item["full_name"]+'</a><button class="btn btn-dark" value="'
                            +item["id"]+'" onclick="follow(this);" id="follow-'+item["id"]+'" data-id="'
                            +item["id"]+'">Follow</button></li>';
                    });
                    $("#Modal-body").append(result);
                    $("#modal-title").html("Liker List");
                    $("#myModal").modal('show');
                
                }
                
            }
        })
    }
    function activityVoteDownList(id)
    {
        activity_id = $(id).attr('data-id');
        
        $("#Modal-body li").remove();
        $.ajax({
            url: "{{ route('activityVoteDownList') }}",
            method: 'GET',
            data: {
                activity_id: activity_id,

            },
            dataType: 'json',
            success: function (data) {

                if(data != "")
                {
                    result= '';
                    jQuery.each(data, function(index, item) {
                        result += '<li><img src="'+item["profile_image"]+'"><a>'
                            +item["full_name"]+'</a><button class="btn btn-dark" value="'
                            +item["id"]+'" onclick="follow(this);" id="follow-'+item["id"]+'" data-id="'
                            +item["id"]+'">Follow</button></li>';
                    });
                    $("#Modal-body").append(result);
                    $("#modal-title").html("Disliker List");
                    $("#myModal").modal('show');
                }
                
            }
        })
    }

    function activityRealList(id)
    {
        activity_id = $(id).attr('data-id');

        $("#Modal-body li").remove();
        $.ajax({
            url: "{{ route('activityRealList') }}",
            method: 'GET',
            data: {
                activity_id: activity_id,

            },
            dataType: 'json',
            success: function (data) {

                if(data != "")
                {
                    result= '';
                    jQuery.each(data, function(index, item) {
                        result += '<li><img src="'+item["profile_image"]+'"><a>'
                            +item["full_name"]+'</a><button class="btn btn-dark" value="'
                            +item["id"]+'" onclick="follow(this);" id="follow-'+item["id"]+'" data-id="'
                            +item["id"]+'">Follow</button></li>';
                    });
                    $("#Modal-body").append(result);
                    $("#modal-title").html("Real Reactor List");
                    $("#myModal").modal('show');
                }
                
            }
        })
    }
    function activityFakeList(id)
    {
        activity_id = $(id).attr('data-id');
        
        $("#Modal-body li").remove();
        $.ajax({
            url: "{{ route('activityFakeList') }}",
            method: 'GET',
            data: {
                activity_id: activity_id,

            },
            dataType: 'json',
            success: function (data) {

                if(data != "")
                {
                    result= '';
                    jQuery.each(data, function(index, item) {
                        result += '<li><img src="'+item["profile_image"]+'"><a>'
                            +item["full_name"]+'</a><button class="btn btn-dark" value="'
                            +item["id"]+'" onclick="follow(this);" id="follow-'+item["id"]+'" data-id="'
                            +item["id"]+'">Follow</button></li>';
                    });
                    $("#Modal-body").append(result);
                    $("#modal-title").html("Fake Reactor List");
                    $("#myModal").modal('show');
                }
                
            }
        })
    }

    // comment
    function commentVoteUpList(id)
    {
        comment_id = $(id).attr('data-id');

        $("#Modal-body li").remove();
        $("#Modal-body li").remove();
        $.ajax({
            url: "{{ route('commentVoteUpList') }}",
            method: 'GET',
            data: {
                comment_id: comment_id,

            },
            dataType: 'json',
            success: function (data) {
                if(data != "")
                {
                    result= '';
                    jQuery.each(data, function(index, item) {
                            result += '<li><img src="'+item["profile_image"]+'"><a>'
                            +item["full_name"]+'</a><button class="btn btn-dark" value="'
                            +item["id"]+'" onclick="follow(this);" id="follow-'+item["id"]+'" data-id="'
                            +item["id"]+'">Follow</button></li>';
                    });
                    $("#Modal-body").append(result);
                    $("#modal-title").html("Liker List");
                    $("#myModal").modal('show');
                
                }
                
            }
        })
    }
    function commentVoteDownList(id)
    {
        comment_id = $(id).attr('data-id');
        
        $("#Modal-body li").remove();
        $.ajax({
            url: "{{ route('commentVoteDownList') }}",
            method: 'GET',
            data: {
                comment_id: comment_id,

            },
            dataType: 'json',
            success: function (data) {

                if(data != "")
                {
                    result= '';
                    jQuery.each(data, function(index, item) {
                        result += '<li><img src="'+item["profile_image"]+'"><a>'
                            +item["full_name"]+'</a><button class="btn btn-dark" value="'
                            +item["id"]+'" onclick="follow(this);" id="follow-'+item["id"]+'" data-id="'
                            +item["id"]+'">Follow</button></li>';
                    });
                    $("#Modal-body").append(result);
                    $("#modal-title").html("Disliker List");
                    $("#myModal").modal('show');
                }
                
            }
        })
    }

    function commentRealList(id)
    {
        comment_id = $(id).attr('data-id');
        // var abc = '{{ Session::get("user")}}';

        $("#Modal-body li").remove();
        $.ajax({
            url: "{{ route('commentRealList') }}",
            method: 'GET',
            data: {
                comment_id: comment_id,

            },
            dataType: 'json',
            success: function (data) {

                if(data != "")
                {
                    result= '';
                    jQuery.each(data, function(index, item) {
                        result += '<li><img src="'+item["profile_image"]+'"><a>'
                            +item["full_name"]+'</a><button class="btn btn-dark" value="'
                            +item["id"]+'" onclick="follow(this);" id="follow-'+item["id"]+'" data-id="'
                            +item["id"]+'">Follow</button></li>';
                    });
                    $("#Modal-body").append(result);
                    $("#modal-title").html("Real Reactor List");
                    $("#myModal").modal('show');
                }
                
            }
        })
    }
    function commentFakeList(id)
    {
        comment_id = $(id).attr('data-id');
        
        $("#Modal-body li").remove();
        $.ajax({
            url: "{{ route('commentFakeList') }}",
            method: 'GET',
            data: {
                comment_id: comment_id,

            },
            dataType: 'json',
            success: function (data) {

                if(data != "")
                {
                    result= '';
                    jQuery.each(data, function(index, item) {
                        result += '<li><img src="'+item["profile_image"]+'"><a>'
                            +item["full_name"]+'</a><button class="btn btn-dark" value="'
                            +item["id"]+'" onclick="follow(this);" id="follow-'+item["id"]+'" data-id="'
                            +item["id"]+'">Follow</button></li>';
                    });
                    $("#Modal-body").append(result);
                    $("#modal-title").html("Fake Reactor List");
                    $("#myModal").modal('show');
                }
                
            }
        })
    }
    
    function memberVoteUpList(user_id)
    {
        $("#Modal-body li").remove();
        $.ajax({
            url: "{{ route('memberVoteUpList') }}",
            method: 'GET',
            data: {
                user_id: user_id,

            },
            dataType: 'json',
            success: function (data) {

                if(data != "")
                {
                    result= '';
                    jQuery.each(data, function(index, item) {
                        result += '<li><img src="'+item["profile_image"]+'"><a>'
                            +item["full_name"]+'</a><button class="btn btn-dark" value="'
                            +item["id"]+'" onclick="follow(this);" id="follow-'+item["id"]+'" data-id="'
                            +item["id"]+'">Follow</button></li>';
                    });
                    $("#Modal-body").append(result);
                    $("#modal-title").html("Liker List");
                    $("#myModal").modal('show');
                }
                
            }
        })
    }
    function memberVoteDownList(user_id)
    {
        $("#Modal-body li").remove();
        $.ajax({
            url: "{{ route('memberVoteDownList') }}",
            method: 'GET',
            data: {
                user_id: user_id,

            },
            dataType: 'json',
            success: function (data) {

                if(data != "")
                {
                    result= '';
                    jQuery.each(data, function(index, item) {
                        result += '<li><img src="'+item["profile_image"]+'"><a>'
                            +item["full_name"]+'</a><button class="btn btn-dark" value="'
                            +item["id"]+'" onclick="follow(this);" id="follow-'+item["id"]+'" data-id="'
                            +item["id"]+'">Follow</button></li>';
                    });
                    $("#Modal-body").append(result);
                    $("#modal-title").html("Disliker List");
                    $("#myModal").modal('show');
                }
                
            }
        })
    }


    //  follow

    function follow(id) {
        data_id = $(id).attr('data-id');

        $.ajax({
            url: "{{ route('follow') }}",
            method: 'GET',
            data: {
                follow_id: data_id,

            },
            dataType: 'json',
            success: function (data) {
                flw = '#follow-' + data_id;
                $(flw).html(data);
                $('#score-card').load(location.href +' #score-card');

            }
        })
    }

    // block
    function block(id) {

        data_id = $(id).attr('data-id');
        blc = '#block-' + data_id;
        $.ajax({
            url: "{{ route('block') }}",
            method: 'GET',
            data: {
                block_id: data_id

            },
            dataType: 'json',
            success: function (data) {

                $(blc).html(data);
            }
        })
    }
    // unblock
    function unblock(id) {
        block_id = $(id).attr('data-id');
        blc = '#block-' + block_id;
        $.ajax({
            url: "{{ route('unblock') }}",
            method: 'GET',
            data: {
                block_id: block_id

            },
            dataType: 'json',
            success: function (data) {
                $(blc).html('Block');
                $('.setting-section').load(location.href +' .setting-section');
            }
        })
    }

    // Report
    function report(id)
    {
        $('.member-report-form').remove();
        data_id = $(id).attr('data-id');
        $.ajax({
            url: "{{ route('memberReport') }}",
            method: 'GET',
            data: {
                report_id: data_id

            },
            dataType: 'json',
            success: function (data) {

                $("#report-body").append(data);
                $("#reportModal").modal('show');

            }
        })
    }
    function reportSave() {
        let subject = $("select[name=subject]").val();
        let description = $("textarea[name=description]").val();
        let reportId = $("input[name=reportId]").val();
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            
            url: "{{ route('memberReportSave') }}",
            method: 'POST',
            data: {
                subject: subject,
                description: description,
                report_id: reportId
            },
            dataType: 'json',
            success: function (data) {
                button = '#report-' + reportId;
                form = '#memberReportForm-' + reportId;
                $(form).remove();
                $(button).html('Reported');
                $("#reportModal").modal('hide');

            }
        });
    }

    // Mention
    function mention()
    {
        content = $("#activityContent").val();
        lastChar = content.substr(content.length - 1);

        var characterCount = $("#activityContent").val().length,
        current_count = $('#current_count'),
        maximum_count = $('#maximum_count'),
        count = $('#count');    
        current_count.text(characterCount); 

        if(content.length < 1)
        {
            $('#debugBox').remove();
            $('#x').remove();
        }

        if(lastChar == '@')
        {
            sessionStorage.setItem("tag", "1");
        }

        if(lastChar == ' ')
        {
            sessionStorage.setItem("tag", "0");
            $("#mention-list li").remove();

        }

        var tag = sessionStorage.getItem("tag");

        if(tag == 1){

            $.ajax({
                url: "{{ route('activityMention') }}",
                method: 'GET',
                data: {
                    content: content

                },
                dataType: 'json',
                success: function (data) {
            

                    $("#mention-list li").remove();

                    if(data != "")
                    {
                        result= '';
                        $.each(data, function(index, item) {
                            result += '<li data-id="'+item["id"]+'" onclick="addMention(this)"><span>@'
                                +item["user_name"]+'</span><span>'+item["full_name"]+'</span><img src="'+item["profile_image"]+'"></li>';

                        });

                        $("#mention-list").append(result);
                        
                    }

                }
            })
        }
    
    }


    $("#mention-list").click(function(e){
        e.stopPropagation();
    });

    $(document).click(function(){
        $("#mention-list li").remove();
    });

    function addMention(e)
    {
        data_id = $(e).attr('data-id');

        $.ajax({
            url: "{{ route('activityAddMention') }}",
            method: 'GET',
            data: {
                id: data_id

            },
            dataType: 'json',
            success: function (data) {

                form = $("#activityContent");

                
                oldVal = form.val();
                afterMentionVal = oldVal.split('@').pop();

                if(afterMentionVal != ''){
                    str = "@"+afterMentionVal; 
                    oldVal2 = oldVal.replace(str, '@');
                    newVal = oldVal2 + data['user_name'] + ' ';
                }
                else{
                    newVal = oldVal + data['user_name'] + ' ';
                }

 
                form.val(newVal);
                form.html(newVal);

                $("#mention-list li").remove();
                
            }

        })    

    }

    function biography()
    {
        $(".edit-count").show()
        var characterCount = $("#bio").val().length,
        current_count = $('#current_count');
        maximum_count = $('#maximum_count');
        count = $('#count');    
        current_count.text(characterCount); 
    }


    // Member Voting
    function voteUpMember(id)
    {
        vote_id = $(id).attr('vote-id');
        user_id = $(id).attr('user-id');

        $.ajax({
            url: "{{ route('memberVoteUp') }}",
            method: 'GET',
            data: {
                vote_id : vote_id,
                user_id : user_id,
            },
            dataType: 'json',
            success: function (data) {
                
                counter1 = '#member-voteUp-count-'+vote_id;
                counter2 = '#member-voteDown-count-'+vote_id;
                img1 = '#member-voteUp-'+vote_id;
                img2 = '#member-voteDown-'+vote_id;
                $(img1).attr("src", "{{asset('')}}"+data['img']);
                $(img2).attr("src", "{{asset('')}}images/reaction/voteDown-off.png");
                $(counter1).html(data['up']);
                $(counter2).html(data['down']);
                $('#score-card').load(location.href +' #score-card');
            }

        })
    }
    function voteDownMember(id)
    {
        vote_id = $(id).attr('vote-id');
        user_id = $(id).attr('user-id');

        $.ajax({
            url: "{{ route('memberVoteDown') }}",
            method: 'GET',
            data: {
                vote_id : vote_id,
                user_id : user_id,
            },
            dataType: 'json',
            success: function (data) {
                
                counter1 = '#member-voteUp-count-'+vote_id;
                counter2 = '#member-voteDown-count-'+vote_id;
                img1 = '#member-voteUp-'+vote_id;
                img2 = '#member-voteDown-'+vote_id;
                $(img1).attr("src", "{{asset('')}}images/reaction/voteUp-off.png");
                $(img2).attr("src", "{{asset('')}}"+data['img']);
                $(counter1).html(data['up']);
                $(counter2).html(data['down']);
                $('#score-card').load(location.href +' #score-card');
            }

        })
    }

    // Score Details
    function scoreDetails(id)
    {
        $("#score-body table").remove();

        $.ajax({
            url: "{{ route('scoreDetails') }}",
            method: 'GET',
            data: {
                id : id
            },
            dataType: 'json',
            success: function (data) {
                var score = "<table class='score-table mx-auto'>"+
                "<tr><td><b>Type:</b></td><td><b>Score</b></td></tr>"+
                "<tr><td>Registration</td><td>"+data['registration']+"</td></tr>"+
                "<tr><td>Member Like/Dislike</td><td>"+data['member_vote']+"</td></tr>"+
                "<tr><td>Follow</td><td>"+data['follow']+"</td></tr>"+
                "<tr><td>Activity</td><td>"+data['activity']+"</td></tr>"+
                "<tr><td>Comment</td><td>"+data['comment']+"</td></tr>"+
                "<tr><td>Activity Vote Up/Down</td><td>"+data['activity_vote']+"</td></tr>"+
                "<tr><td>Activity Fake/Real</td><td>"+data['activity_react']+"</td></tr>"+
                "<tr><td>Comment Vote Up/Down</td><td>"+data['comment_vote']+"</td></tr>"+
                "<tr><td>Comment Fake/Real</td><td>"+data['comment_react']+"</td></tr>"+
                "<tr><td><b>Total Score</b></td><td><b>"+data['score']+"</b></td></tr>"+
                "</table>";

                $("#score-body").append(score);
                $("#scoreModal").modal('show');
            }

        })
    }


    // search
    function search() {
        value = $("input[name=search]").val();
        if(value != '')
        {
            url = "{{route('search','')}}/"+value;
        
            window.location.href = url;
        }
    }

    var enter = document.getElementById("search");
    enter.onkeyup = function(e){
        if(e.keyCode == 13){
            search();
        }
    }


    // Members
    function memberSearch()
    {
        value = $("input[name=member-search]").val();

        if( value.length != 0 )
        {
            $.ajax({
                url: "{{ route('memberSearch') }}",
                method: 'GET',
                data: {
                    value: value

                },
                dataType: 'json',
                success: function (data) {
            
                    $("#member-list").hide();
                    $("#search-list-item").remove();

                    if(data != "")
                    {
                        var result = '<div class="row" id="search-list-item">'+data+'</div>'
                        $("#search-list").append(result);
                        
                    }
                    else{
                        var result = '<h3 class="text-center" id="search-list-item">No xprezer found!</h3>'
                        $("#search-list").append(result);
                    }

                }
            })
        }

        else{
            $("#search-list-item").remove();
            $("#member-list").show();
        }
    }
    var enter = document.getElementById("member-search");
    enter.onkeyup = function(e){
        if(e.keyCode == 13){
            memberSearch();
        }
    }

    function followerSearch()
    {
        value = $("input[name=follower-search]").val();


        if( value.length != 0 )
        {
            $.ajax({
                url: "{{ route('followerSearch') }}",
                method: 'GET',
                data: {
                    value: value
                },
                dataType: 'json',
                success: function (data) {
            
                    $("#follower-list").hide();
                    $("#search-list-item").remove();

                    if(data != "")
                    {
                        var result = '<div class="row" id="search-list-item">'+data+'</div>'
                        $("#search-list").append(result);
                    }
                    else{
                        var result = '<h3 class="text-center" id="search-list-item">No xprezer found!</h3>'
                        $("#search-list").append(result);
                    }
                }
            })
        }

        else{
            $("#search-list-item").remove();
            $("#follower-list").show();
        }
    }

    function followingSearch()
    {
        value = $("input[name=following-search]").val();

        if( value.length != 0 )
        {
            $.ajax({
                url: "{{ route('followingSearch') }}",
                method: 'GET',
                data: {
                    value: value
                },
                dataType: 'json',
                success: function (data) {
            
                    $("#following-list").hide();
                    $("#search-list-item").remove();

                    if(data != "")
                    {
                        var result = '<div class="row" id="search-list-item">'+data+'</div>'
                        $("#search-list").append(result);
                    }
                    else{
                        var result = '<h3 class="text-center" id="search-list-item">No xprezer found!</h3>'
                        $("#search-list").append(result);
                    }
                }
            })
        }

        else{
            $("#search-list-item").remove();
            $("#following-list").show();
        }
    }

    function blockSearch()
    {
        value = $("input[name=block-search]").val();

        if( value.length != 0 )
        {
            $.ajax({
                url: "{{ route('blockSearch') }}",
                method: 'GET',
                data: {
                    value: value
                },
                dataType: 'json',
                success: function (data) {
            
                    $("#block-list").hide();
                    $("#search-list-item").remove();

                    if(data != "")
                    {
                        var result = '<div class="row" id="search-list-item">'+data+'</div>'
                        $("#search-list").append(result);
                    }
                    else{
                        var result = '<h3 class="text-center" id="search-list-item">No xprezer found!</h3>'
                        $("#search-list").append(result);
                    }
                }
            })
        }

        else{
            $("#search-list-item").remove();
            $("#block-list").show();
        }
    }


    // Notification
    function notificationRead(id)
    {
        
        $.ajax({
            url: "{{ route('notificationRead') }}",
            method: 'GET',
            data: {
                    id: id
            },
            dataType: 'json',
            success: function (data) {
                identity = '#read-notification-'+id;
                icon = '<i class="fa-solid fa-eye-slash"></i>';
                $(identity).html(icon);
            }
        })
    }

    // Filter
    function activityFilter(emotion)
    {
        emotion = $(emotion).val();
        $.ajax({
            url: "{{ route('activityFilter') }}",
            method: 'GET',
            data: {
                emotion: emotion
            },
            dataType: 'json',
            success: function (data) {

                //alert(data);
                $('#activity-feed').load(location.href +' #activity-feed');
            } 
        });  
      
    }
    function dateFilter(emotion)
    {
        date = $('#date-filter').val();
        $.ajax({
            url: "{{ route('dateFilter') }}",
            method: 'GET',
            data: {
                date: date
            },
            dataType: 'json',
            success: function (data) {
                $('#activity-feed').load(location.href +' #activity-feed');
            } 
        });  
    }
 

    function share(id) {
        data_id = $(id).attr('data-id');
        option = '#share-option-' + data_id;

        $(option).toggle(300);
    }

    function guestUser()
    {
        var url = "https://wexprez.com/guest";
        $(location).attr('href',url);
    }

    function closePreview()
    {
        $('#linkPreview').hide();
    }

</script>

<!-- Load Facebook SDK for JavaScript -->
<script>(function(d, s, id) {
var js, fjs = d.getElementsByTagName(s)[0];
if (d.getElementById(id)) return;
js = d.createElement(s); js.id = id;
js.src = "https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.0";
fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));</script>

