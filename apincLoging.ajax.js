var ApincAjaxLog = function(dbName) {
	var data = {};

	this.setFirstLog = function() {
		if (typeof google == "undefined") {
			arel.Debug.Log("fail to loading Google Maps API");
		} else {
			getAddress(userData.l[0], userData.l[1]);
		}
	}

	this.setContentLog = function(requestContent, status) {
		data = {
			dbname: dbName,
			cid: userData.cid,
			uid: userData.uid,
			request: requestContent,
			status: status
		};
		requestSql(data);
	}

	function requestFirstLog(address) {
		data = {
			dbname: dbName,
			first: "true",
			address: address,
			aid :userData.aid
		};
		requestSql(data);
	}

	function getAddress(Lat, Lng, callback) {
		var geocoder = new google.maps.Geocoder();
		var coordinate = new google.maps.LatLng(Lat, Lng);

		geocoder.geocode({"latLng": coordinate}, function(results, status) {
			var address;
			if (status == google.maps.GeocoderStatus.OK) {
				if (results[0]) {
					address = results[0].formatted_address;
					requestFirstLog(address);
				}
			} else {
				arel.Debug.Log("fail to get address " + status );
			}
		})
	}

	function requestSql(data) {
		var xmlhttp = new XMLHttpRequest();

		xmlhttp.open("GET","http://sql.ar-ap-inc.jp/apincLoging.ajax.php?" + EncodeHTMLForm(data), true);
		xmlhttp.addEventListener("readystatechange", callback, false);
		xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencorded");
		xmlhttp.send();
		
		function callback() {
			if (xmlhttp.readyState == 4) {
				if (xmlhttp.status == 200) {
					arel.Debug.Log(xmlhttp.responseText);
				} else {
					arel.Debug.Log("fail to send ajax");
					arel.Debug.Log(xmlhttp.responseText);
				}
			} else {
				arel.Debug.Log("connecting..." + xmlhttp.readyState);
			}
		}

		function EncodeHTMLForm(data) {
			var params = [];

			for (var name in data) {
				var value = data[name];
				var param = encodeURIComponent(name).replace(/%20/g, '+') + '=' + encodeURIComponent(value).replace(/%20/g, '+');
        		params.push( param );
			}

			return params.join('&')
		}
	}
}