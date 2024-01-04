jQuery.ajax(
	{
		type: 'POST',
		url: leafext_proxy_ajax.ajaxurl,
		data: {
			action: 'leafext_proxy',
		}
	}
);
