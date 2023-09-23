jQuery.ajax({
    type: 'POST',
    url: elevation_proxy_ajax.ajaxurl,
    data: {
        action: 'leafext_elevation_proxy',
    }
});
