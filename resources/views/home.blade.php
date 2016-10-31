@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h1>Min bokhylla</h1>
            <hr />
            @foreach($my_books as $book)
                <img src="{{ $book->book->cover }}" alt="">
            @endforeach
        </div>
    </div>
</div>
@endsection
