<div class="container">

<p> // Lire  https://www.sitepoint.com/php-authorization-jwt-json-web-tokens/</p>

<code>
$("#btnGetResource").click(function(e){
        e.preventDefault();
        $.ajax({
            url: 'resource/image',
            beforeSend: function(request){
                request.setRequestHeader('Authorization', 'Bearer ' + store.JWT);
            },
            type: 'GET',
            success: function(data) {
                // Decode and show the returned data nicely.
            },
            error: function() {
                alert('error');
            }
        });
    });
</code>

</div>