@extends('admin.layout.main')

@section('title')
Activity Report list
@endsection

@section('content')
<section class="notification-section">
    <div class="container p-5">
        @if ($message = Session::get('success'))
        <div class="alert alert-success alert-dismissible d-none d-md-block w-50 mx-auto mt-3">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>{{ $message }}</strong>
        </div>
        @endif


        @if ($message = Session::get('error'))
        <div class="alert alert-danger alert-dismissible d-none d-md-block w-50 mx-auto mt3">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <strong>{{ $message }}</strong>
        </div>
        @endif


        <h3>Notification Create</h3>
        <br>
        <!-- Activity Form -->
        <div class="activity-form">
            <form method="post" id="activity-form" enctype="multipart/form-data">
                @csrf
                <div class="input-area">
                    <textarea name="content" id="content" rows="5" required placeholder="Just Xprez"
                        maxlength="250"></textarea>
                </div>

                <div class="row">
                    <div class="col-lg-4 col-12 mt-2">
                        <select name="emotion" id="emotion" required="required">
                            <option disabled selected value="">Choose an Emotion</option>
                            <option value="Positive">Positive</option>
                            <option value="Negative">Negative</option>
                            <option value="Neutral">Neutral</option>
                        </select>
                    </div>

                    <div class="col-lg-4 col-xxl-4 col-12 mt-3">
                        <input type="checkbox" name="anonymous" id="anonymous" value="1">
                        <label for="anonymous">Post as Anonymous</label>
                    </div>

                    <div class="col-lg-4 col-xxl-4 col-12 mt-3 text-md-end">
                        <button class="btn btn-dark" type="submit">Post Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
