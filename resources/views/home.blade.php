@extends('layouts.app')

@section('css')
    <link href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        .like{
            color: red;
        }

        .unlike{
            color : black;
        }
    </style>
@endsection


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <form id='postform' action="/tweet" method="POST" enctype="multipart/form-data">
                <div class="col-md-12">
                    <textarea  name="text" rows="4" cols="130"></textarea>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
        </form>
        @foreach ($timeLines as $timeline)
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                   
                    <div> <img src="{{$timeline->user->profile_image_url_https}}"/>{{$timeline->user->name}} - {{date('d-m-Y H:i:s', strtotime($timeline->created_at))}}</div>
                   

                </div>

                <div class="card-body">
                    <div>
                        {{$timeline->text}}
                    </div>
                    <div>
                        @if(isset($timeline->extended_entities->media))
                            @foreach ($timeline->extended_entities->media as $pic)
                                <img style="max-width: 600px;" src="{{$pic->media_url_https}}"/>
                            @endforeach
                        @endif
                    </div>

                    <div style="font-size: 36px;">
                        @if($timeline->favorited)
                            <i class="fas fa-heart like" tid="{{$timeline->id_str}}"  onclick="unlike(this);"></i>
                        @else
                            <i class="fas fa-heart unlike" tid="{{$timeline->id_str}}" onclick="like(this);"></i>
                        @endif
                        <label>{{$timeline->favorite_count}}</label>
                        
                 
                    </div>
                   

                    
             
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>
@endsection


@section('script')
<script src="https://kit.fontawesome.com/807614c11a.js" crossorigin="anonymous"></script>



<script>

    function like(obj){
        console.log(obj);
        $.ajax({
                type: 'Post',
                url: '/like',
                data: {
                    'id' : $(obj).attr('tid')
                },
                success: function(result){
                    if(result.status==1){
                        $(obj).removeClass('unlike').addClass('like');
                        $(obj).attr("onclick","unlike(this)");
                        $(obj).parent().find('label').text(parseInt($(obj).parent().find('label').text())+1)
                    }
                },
                error: function(){
                    alert('Fail.');
                 
                }
        });
    }

    function unlike(obj){
        $.ajax({
                type: 'Post',
                url: '/unlike',
                data: {
                    'id' : $(obj).attr('tid')
                },
                success: function(result){
                    if(result.status==1){
                        $(obj).removeClass('like').addClass('unlike');
                        $(obj).attr("onclick","like(this)");
                        $(obj).parent().find('label').text(parseInt($(obj).parent().find('label').text())-1)
                    }
                    else{
                        alert('Fail.');
                    }
                   
                },
                error: function(){
                    alert('Fail.');
                    $($obj).attr('disabled', false);
                }
        });
    }


</script>
@endsection