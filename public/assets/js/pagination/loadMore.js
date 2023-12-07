function throttle(func, wait) {
    let waiting = false;
    return function () {
        if (waiting) {
            return;
        }

        waiting = true;
        setTimeout(() => {
            func.apply(this, arguments);
            waiting = false;
        }, wait);
    };
}
/*------------------------------------------
       --------------------------------------------
       call infinteLoadMore()
       --------------------------------------------
       --------------------------------------------*/
function infinteLoadMore(page, ENDPOINT) {
    if ($('.no-more-data').hasClass('d-none')) {
        let endpointFinal = page==null? ENDPOINT: ENDPOINT + "?page=" + page
        // window.history.replaceState({}, document.title, endpointFinal);

        $.ajax({
            url: endpointFinal,
            datatype: "html",
            type: "get",
            beforeSend: function () {
        }
        })
            .done(function (response) {
                if (response.html == '') {
                        var noMoreDataElement = document.querySelector('.no-more-data');
                        noMoreDataElement.classList.remove('d-none');
                        noMoreDataElement.style.display = 'flex';
                        noMoreDataElement.style.justifyContent = 'center';
                        noMoreDataElement.textContent = "We don't have more data to display";
                    }

                // <!-- $('.auto-load').hide(); -->
                $(".scrolling-pagination").append(response.html);
            })
            .fail(function (jqXHR, ajaxOptions, thrownError) {
                console.log('Server error occured');
            });
    } else {
        return;
    }
}
