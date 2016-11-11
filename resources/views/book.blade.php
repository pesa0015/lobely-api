@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div id="book-page">
                <h1>{{ $book->title }}</h1>
                <div id="author">{{ $book->author }}</div>
                @if($saved)
                    <div id="saved-{{ $book->id }}" class="saved">
                        <div class="is-saved">Finns i min bokhylla</div>
                        <span class="remove-from-bookshelf" data-book-id="{{ $book->id }}">Ta bort</span>
                    </div>
                @else
                    <span class="btn btn-primary save-to-bookshelf" data-book-id="{{ $book->id }}">Spara</span>
                @endif
                <hr />
                <img id="large-cover" src="{{ $book->cover }}" alt="">
            </div>
        </div>
    </div>
</div>

@endsection
