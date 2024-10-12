/*
 * Leaflet List Markers v0.1.0 - 2017-11-26
 *
 * Copyright 2017 Stefano Cudini
 * stefano.cudini@gmail.com
 * https://opengeo.tech/
 *
 * Licensed under the MIT license.
 *
 * Demo:
 * https://opengeo.tech/maps/leaflet-list-markers/
 *
 * Source:
 * git@github.com:stefanocudini/leaflet-list-markers.git
 *
 */

(function() {

L.Control.ListMarkers = L.Control.extend({

	includes: L.version[0]==='1' ? L.Evented.prototype : L.Mixin.Events,

	options: {
		layer: false,
		maxItems: 20,
		collapsed: false,
		label: 'title',
		itemIcon: L.Icon.Default.imagePath+'/marker-icon.png',
		itemArrow: '&#10148;',	//visit: https://character-code.com/arrows-html-codes.php
		maxZoom: 9,
		position: 'bottomleft'
		//TODO autocollapse
	},

	initialize: function(options) {
		L.Util.setOptions(this, options);
		this._container = null;
		this._list = null;
		this._layer = this.options.layer || new L.LayerGroup();
	},

	onAdd: function (map) {

		this._map = map;

		var s = map.getSize();
		// container.style.height = (s.y)*0.7+'px';
		// container.style.maxWidth = (s.x/2)+'px';

		var style = document.createElement('style');
		style.type = 'text/css';
		style.innerHTML = '.list-markers-x-y { height: '+(s.y)*0.7+'px; maxWidth = '+(s.x/2)+'px;}';
		//document.getElementsByTagName('head')[0].appendChild(style);

		var link = this._container = L.DomUtil.create('a', 'leaflet-control-layers-toggle');
		link.href = '#';
		link.title = 'Layers';
		link.role = "button";
		//this._container.appendChild(link);

		var container = this._container = L.DomUtil.create('div', 'list-markers list-markers-x-y');
		//<a class="leaflet-control-layers-toggle" href="#" title="Layers" role="button"></a>
		this._initToggle();

		this._list = L.DomUtil.create('ul', 'list-markers-ul', container);

		map.on('moveend', this._updateList, this);

		this._updateList();
		console.log (container);
		return container;
	},

	onRemove: function(map) {
		map.off('moveend', this._updateList, this);
		this._container = null;
		this._list = null;
	},

	_createItem: function(layer) {

		var li = L.DomUtil.create('li', 'list-markers-li'),
			a = L.DomUtil.create('a', '', li),
			icon = this.options.itemIcon ? '<img src="'+this.options.itemIcon+'" />' : '',
			that = this;

		a.href = '#';
		L.DomEvent
			.disableClickPropagation(a)
			.on(a, 'click', L.DomEvent.stop, this)
			.on(a, 'click', function(e) {
				//this._moveTo( layer.getLatLng() );
				that.fire('item-click', {layer: layer });
			}, this)
			.on(a, 'mouseover', function(e) {
				that.fire('item-mouseover', {layer: layer });
			}, this)
			.on(a, 'mouseout', function(e) {
				that.fire('item-mouseout', {layer: layer });
			}, this);



		//console.log('_createItem',layer.options);

		if( layer.options.hasOwnProperty(this.options.label) )
		{
			a.innerHTML = icon+'<span>'+layer.options[this.options.label]+'</span> <b>'+this.options.itemArrow+'</b>';
			//TODO use related marker icon!
			//TODO use template for item
		}
		else
			console.log("propertyName '"+this.options.label+"' not found in marker");

		return li;
	},

	_updateList: function() {

		var that = this,
			n = 0;

		this._list.innerHTML = '';
		this._layer.eachLayer(function(layer) {
			if(layer instanceof L.Marker)
				if( that._map.getBounds().contains(layer.getLatLng()) )
					if(++n < that.options.maxItems)
						that._list.appendChild( that._createItem(layer) );
		});
		that._map.fire('update-end');
	},

	_initToggle: function () {

		/* inspired by L.Control.Layers */

		var container = this._container;

		//Makes this work on IE10 Touch devices by stopping it from firing a mouseout event when the touch is released
		container.setAttribute('aria-haspopup', true);

		if (!L.Browser.touch) {
			L.DomEvent
				.disableClickPropagation(container);
				//.disableScrollPropagation(container);
		} else {
			L.DomEvent.on(container, 'click', L.DomEvent.stopPropagation);
		}

		console.log("collapse?",this.options.collapsed);
		if (this.options.collapsed)
		{
			console.log("collapsed");
			this._collapse();

			if (!L.Browser.android) {
				L.DomEvent
					.on(container, 'mouseover', this._expand, this)
					.on(container, 'mouseout', this._collapse, this);
			}
			var link = this._button = L.DomUtil.create('a', 'list-markers-toggle', container);
			link.href = '#';
			link.title = 'List Markers';

			if (L.Browser.touch) {
				L.DomEvent
					.on(link, 'click', L.DomEvent.stop)
					.on(link, 'click', this._expand, this);
			}
			else {
				L.DomEvent.on(link, 'focus', this._expand, this);
			}

			this._map.on('click', this._collapse, this);
			// TODO keyboard accessibility
		}
	},

	_expand: function () {
		console.log("expand vorher",this._container.className);
		// original
		// this._container.className = this._container.className.replace(' list-markers-collapsed', '');

		this._container.className = this._container.className.replace(' list-markers-collapsed', ' list-markers-x-y');

		//this._container.className = this._container.className.replace(' list-markers-collapsed list_collapsed ', 'list-markers ');
		//this._container.className = this._container.className.replace(' leaflet-control-layers-toggle leaflet-control', ' list-markers-x-y');
		// list-markers-toggle
		// L.DomUtil.addClass(this._container, 'leaflet-control-layers-toggle');

		//L.DomUtil.addClass(this._container, 'list-markers-x-y');
		//L.DomUtil.addClass(this._container, 'list-markers');
		//L.DomUtil.removeClass(this._container, 'list-markers-collapsed');
		//L.DomUtil.removeClass(this._container, 'list_collapsed');
		console.log("expand danach",this._container.className);
	},

	_collapse: function () {
		console.log("collapse vorher",this._container.className);
		// original
		L.DomUtil.addClass(this._container, 'list-markers-collapsed');

		//this._container.className = this._container.className.replace(' list-markers-x-y', ' list-markers-collapsed');
		//L.DomUtil.addClass(this._container, 'list-markers-collapsed');
		//this._container.className = this._container.className.replace('list-markers', 'list-markers-collapsed list_collapsed');
		//this._container.className = this._container.className.replace(' list-markers-x-y', ' leaflet-control-layers-toggle leaflet-control');
		//this._container.className = this._container.className.replace(' list-markers-x-y ', '');
		// L.DomUtil.removeClass(this._container, 'leaflet-control-layers-toggle');
		L.DomUtil.removeClass(this._container, 'list-markers-x-y');
		//L.DomUtil.removeClass(this._container, 'list-markers');
		//L.DomUtil.addClass(this._container, 'list-markers-collapsed');
		//L.DomUtil.addClass(this._container, 'list_collapsed');
		console.log("collapse danach",this._container.className);
	},

    _moveTo: function(latlng) {
		if(this.options.maxZoom)
			this._map.setView(latlng, Math.min(this._map.getZoom(), this.options.maxZoom) );
		else
			this._map.panTo(latlng);
		}
});

L.control.listMarkers = function (options) {
    return new L.Control.ListMarkers(options);
};

L.Map.addInitHook(function () {
    if (this.options.listMarkersControl) {
        this.listMarkersControl = L.control.listMarkers(this.options.listMarkersControl);
        this.addControl(this.listMarkersControl);
    }
});

}).call(this);
