jQuery.ajax({
    type: 'POST',
    url: elevation_proxy_ajax.ajaxurl,
    data: {
        action: 'leafext_elevation_proxy',
    },
    success: function (data, textStatus, XMLHttpRequest) {
        console.log(data);
    },
    error: function (XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
    }
});
