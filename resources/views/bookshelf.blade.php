@extends('layouts.app')

@section('content')
<div class="md-overlay"></div>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h1>Min bokhylla</h1>
            <hr />
            @foreach($my_books as $book)
                <a href="/book/{{ $book->book->title_slug }}"><img class="cover dashboard-book-cover" src="{{ $book->cover }}" alt=""></a>
            @endforeach
        </div>
    </div>
</div>
@endsection
