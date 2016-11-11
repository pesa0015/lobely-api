if (window.location.hash && window.location.hash == '#_=_') {
    if (window.history && history.pushState) {
        window.history.pushState("", document.title, window.location.pathname);
    } else {
        // Prevent scrolling by storing the page's current scroll offset
        var scroll = {
            top: document.body.scrollTop,
            left: document.body.scrollLeft
        };
        window.location.hash = '';
        // Restore the scroll offset, should be flicker free
        document.body.scrollTop = scroll.top;
        document.body.scrollLeft = scroll.left;
    }
}
function save(e) {
    var button = e;
    var book_id = button.getAttribute('data-book-id');
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            console.log(xhttp.responseText);
            var result = JSON.parse(xhttp.responseText);
            if (result.success) {
                $(button).replaceWith('<div id="saved-' + result.book_id + '" class="saved"><div class="is-saved">Finns i min bokhylla</div><span class="remove-from-bookshelf" data-book-id="' + result.book_id + '">Ta bort</span></div>');
                $('.remove-from-bookshelf').click(function(){
                    removeBook(this);
                });
            }
        }
    }
    var token = document.getElementsByTagName('meta')[3].getAttribute('content');
    xhttp.open('POST', '/save-to-bookshelf', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.setRequestHeader('X-CSRF-TOKEN', token);
    xhttp.send('book_id=' + book_id);
}
function removeBook(e) {
    var button = e;
    var book_id = button.getAttribute('data-book-id');
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (xhttp.readyState == 4 && xhttp.status == 200) {
            console.log(xhttp.responseText);
            var result = JSON.parse(xhttp.responseText);
            if (result.success) {
                $('#saved-' + result.book_id).replaceWith('<span class="btn btn-primary save-to-bookshelf" data-book-id="' + result.book_id + '">Spara</span>');
                $('.save-to-bookshelf').click(function(){
                    save(this);
                });
            }
        }
    }
    var token = document.getElementsByTagName('meta')[3].getAttribute('content');
    xhttp.open('POST', '/remove-from-bookshelf', true);
    xhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xhttp.setRequestHeader('X-CSRF-TOKEN', token);
    xhttp.send('book_id=' + book_id);
}
$('.save-to-bookshelf').click(function(){
    save(this);
});
$('.remove-from-bookshelf').click(function(){
    removeBook(this);
});