@extends('layout.main')

@section('title')
WeXprez
@endsection

@section('css')
<link rel="stylesheet" href="{{asset('')}}css/works.css">
@endsection

@section('content')
<section class="work-section">
    <div class="container">
        <h1>How It Works?</h1>

        <div class="row">
            <div class="col-12 col-lg-6">
                <div id="myCarousel" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                    <ol class="carousel-indicators">
                        <li data-bs-target="#myCarousel" data-bs-slide-to="0" class="active"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="1"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="2"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="3"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="4"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="5"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="6"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="7"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="8"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="9"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="10"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="11"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="12"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="13"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="14"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="15"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="16"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="17"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="18"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="19"></li>
                        <li data-bs-target="#myCarousel" data-bs-slide-to="20"></li>
                    </ol>
                    <!-- Wrapper for slides -->
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img src="{{asset('')}}images/works/0.png" alt="01" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/0A.png" alt="new" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/1.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/2.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/3.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/4.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/5.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/6.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/7.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/8.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/9.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/10.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/11.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/12.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/13.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/14.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/15.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/16.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/17.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/18.png" alt="New" style="width:100%;">
                        </div>
                        <div class="carousel-item">
                            <img src="{{asset('')}}images/works/19.png" alt="New" style="width:100%;">
                        </div>

                    </div>
                    <!-- Left and right controls -->
                    <a class="carousel-control-prev" data-bs-target="#myCarousel" type="button" data-bs-slide="prev">
                        <i class="fa fa-angle-double-left"></i>
                    </a>

                    <a class="carousel-control-next" data-bs-target="#myCarousel" type="button" data-bs-slide="next">
                        <i class="fa fa-angle-double-right"></i>
                    </a>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <iframe width="100%" height="360" src="https://www.youtube.com/embed/9QfM41nxmSM"
                    title="YouTube video player" frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
            </div>

        </div>
    </div>
</section>
@endsection
