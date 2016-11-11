@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <h1>Böcker ({{ count($books) }})</h1>
            <hr />
            @foreach($books as $book)
                <div class="book">
                    <a href="/book/{{ $book->title_slug }}"><div class="cover"><img src="test.jpg" alt=""></div>
                    <div class="title">{{ $book->title }}</div></a>
                    @if($book->user_id == Auth::user()->id)
                        <div id="saved-{{ $book->id }}" class="saved">
                            <div class="is-saved">Finns i min bokhylla</div>
                            <span class="remove-from-bookshelf" data-book-id="{{ $book->id }}">Ta bort</span>
                        </div>
                    @else
                        <span class="btn btn-primary save-to-bookshelf" data-book-id="{{ $book->id }}">Spara</span>
                    @endif
                </div>
                <hr />
            @endforeach
        </div>
    </div>
</div>

@endsection
