<!DOCTYPE html>
<html lang="en" class="h-100">


<!-- Mirrored from FTS-Absensi.dexignlab.com/xhtml/page-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Dec 2023 21:15:59 GMT -->

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<meta name="robots" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="FTS-Absensi : FTS-Absensi Universitas Ibnu Khaldun">
	<meta property="og:title" content="FTS-Absensi : FTS-Absensi Universitas Ibnu Khaldun">
	<meta property="og:description" content="FTS-Absensi : FTS-Absensi Universitas Ibnu Khaldun">
	<meta property="og:image" content="social-image.png">
	<meta name="format-detection" content="telephone=no">

	<!-- PAGE TITLE HERE -->
	<title>FTS Absensi | {{ ucwords($title) }}</title>

	<!-- FAVICONS ICON -->
	<link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/favicon.png') }}">
	<link href="{{ asset('assets/vendor/jquery-nice-select/css/nice-select.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/vendor/jquery-autocomplete/jquery-ui.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

	<!-- sweetalert2 -->
	<link href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">

	<!-- Leaflet -->
	<!-- <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" /> -->
	<link rel="stylesheet" href="{{ asset('assets/leaflet/css/leaflet.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/leaflet/css/MarkerCluster.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/leaflet/css/MarkerCluster.Default.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/leaflet/css/Control.Geocoder.css') }}">
	<link rel="stylesheet" href="{{ asset('assets/leaflet/css/leaflet-routing-machine.css') }}">
	<link rel="stylesheet" href="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.css" />

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />


	<!-- Files from current project -->
	<link rel="stylesheet" href="{{ asset('assets/leaflet/css/Leaflet.PolylineMeasure.css') }}" />
	<link rel="stylesheet" href="{{ asset('assets/leaflet/css/Control.MiniMap.css') }}" />
	<link rel="stylesheet" href="{{ asset('assets/leaflet/css/leaflet-reticle.css') }}" />
	<link rel="stylesheet" type="text/css"
		href="https://rawgit.com/MarcChasse/leaflet.ScaleFactor/master/leaflet.scalefactor.min.css">

	<style>

	</style>

</head>

<body class="body  h-100">
	<div class="animation">
		<span class="circle one"></span>
		<span class="circle two"></span>
		<span class="circle three"></span>
		<span class="circle four"></span>
		<span class="line-1 ">
			<svg width="1920" height="450" viewBox="0 0 1920 450" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path opacity="0.3"
					d="M0 155L95.4613 293.923C106.459 309.928 131.116 305.943 136.512 287.289L209.86 33.7127C215.892 12.8576 244.803 11.2033 253.175 31.2341L344.838 250.546C352.224 268.217 376.708 269.648 386.102 252.958L519.839 15.3693C529.061 -1.01332 552.975 -0.0134089 560.797 17.0818L716.503 357.389C724.454 374.766 748.899 375.43 757.782 358.51L902.518 82.8223C911.524 65.6685 936.406 66.653 944.028 84.4648L1093.06 432.731C1101.14 451.601 1128.01 451.247 1135.58 432.172L1291.33 39.9854C1298.27 22.5135 1322.1 20.2931 1332.14 36.1824L1473.74 260.126C1482.47 273.922 1502.38 274.494 1511.88 261.221L1667.88 43.3025C1678.17 28.9257 1700.16 31.0533 1707.5 47.1365L1844.91 348.06C1853.69 367.287 1881.58 365.486 1887.81 345.29L1970 79"
					stroke="url(#paint0_linear_332_3757)" stroke-opacity="0.4" stroke-width="6"
					stroke-linecap="round" />
				<defs>
					<linearGradient id="paint0_linear_332_3757" x1="1946.24" y1="352.062" x2="-1.52124" y2="345.607"
						gradientUnits="userSpaceOnUse">
						<stop offset="" stop-color="#6E4AFF" />
						<stop offset="0.479167" stop-color="#E43BFF" />
						<stop offset="1" stop-color="#6E4AFF" />
					</linearGradient>
				</defs>
			</svg>
		</span>
		<span class="line-2">
			<svg width="1920" height="459" viewBox="0 0 1920 459" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path
					d="M0 89L103.191 296.201C112.034 313.958 137.703 312.941 145.114 294.54L224.847 96.5574C232.264 78.141 257.962 77.1423 266.786 94.9275L352.649 267.995C360.863 284.553 384.264 285.148 393.31 269.03L516.226 50.0159C525.164 34.0902 548.205 34.4325 556.666 50.6167L713.497 350.608C721.71 366.318 743.86 367.222 753.326 352.234L901.462 117.684C911.188 102.286 934.102 103.763 941.771 120.282L1091.14 442.062C1099.38 459.816 1124.62 459.817 1132.86 442.064L1303.17 75.2544C1310.64 59.1685 1332.73 57.2308 1342.89 71.7713L1469.94 253.703C1479.15 266.893 1498.71 266.794 1507.78 253.511L1671.82 13.4627C1681.74 -1.05968 1703.63 0.478486 1711.42 16.2459L1844.42 285.267C1853.64 303.905 1880.89 301.723 1887.02 281.857L1970 13"
					stroke="url(#paint0_linear_332_3758)" stroke-opacity="0.4" stroke-width="6"
					stroke-linecap="round" />
				<defs>
					<linearGradient id="paint0_linear_332_3758" x1="1946.24" y1="286.062" x2="-1.52105" y2="279.607"
						gradientUnits="userSpaceOnUse">
						<stop offset="" stop-color="#6E4AFF" />
						<stop offset="0.479167" stop-color="#E43BFF" />
						<stop offset="1" stop-color="#6E4AFF" />
					</linearGradient>
				</defs>
			</svg>
		</span>

	</div>
	<div class="container h-100">
		<div class="row h-100 align-items-center justify-contain-center">
			<div class="col-xl-12">
				<div class="card">
					<div class="card-body ">
						<div class="row m-0">
							<div class="col-xl-6 col-md-6 sign text-center">
								<div>
									<div class="text-center my-5">
										<div class="logo">
											<svg class="logo-abbr" width="43" height="34" viewBox="0 0 43 34"
												fill="none" xmlns="http://www.w3.org/2000/svg">
												<rect x="22.6154" width="19.6154" height="6.53846" rx="3.26923"
													fill="white" />
												<rect x="22.6154" y="9.15387" width="19.6154" height="6.53846"
													rx="3.26923" fill="white" />
												<rect x="22.6154" y="18.3077" width="19.6154" height="6.53846"
													rx="3.26923" fill="white" />
												<rect x="0.384583" y="18.3077" width="19.6154" height="6.53846"
													rx="3.26923" fill="white" />
												<rect x="22.6154" y="27.4615" width="19.6154" height="6.53846"
													rx="3.26923" fill="white" />
												<rect x="0.384583" y="27.4615" width="19.6154" height="6.53846"
													rx="3.26923" fill="white" />
											</svg>
											<div class="text-start d-grid" style="margin-top: -8px;">
												<h1 class="ms-2"
													style="font-size: 22px; letter-spacing: 10px; font-weight: 700;">FTS
												</h1>
												<span class="ms-2" style="font-size: 17px; margin-top: -15px;">Scan
													Absensi Pembelajaran</span>
											</div>
										</div>
									</div>
									<div id="map" style="height: 200px;"></div>
								</div>
							</div>
							<div class="col-xl-6 col-md-6">
								<div class="sign-in-your" style="margin-top: 25%;">
									<form class="form mt-5" method="post" enctype="multipart/form-data">

										<div class="mb-3">
											<label class="mb-1"><strong>NPM</strong></label>
											<input type="text" name="npm" class="form-control" placeholder="NPM"
												required>
										</div>

										<div class="mb-3 mb-0">
											<label class="radio-inline me-3"><input value="0" type="radio"
													name="status_absen">Tidak Hadir</label>
											<label class="radio-inline me-3"><input checked value="1" type="radio"
													name="status_absen">Hadir</label>
											<label class="radio-inline me-3"><input checked value="2" type="radio"
													name="status_absen">Sakit/Izin</label>
										</div>

										<div id="reader" style="width: 100%"></div>

										<div class="text-center mt-3">
											<button id="generate" type="submit"
												class="btn btn-primary btn-block">Generate</button>
										</div>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<!--**********************************
        Scripts
    ***********************************-->
	<!-- Required vendors -->
	<script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
	<script src="{{ asset('assets/vendor/jquery-autocomplete/jquery-ui.js') }}"></script>
	<script src="{{ asset('assets/js/custom.min.js') }}"></script>
	<script src="{{ asset('assets/js/dlabnav-init.js') }}"></script>
	<script src="{{ asset('assets/js/demo.js') }}"></script>
	<!-- <script src="{{ asset('assets/js/styleSwitcher.js') }}"></script> -->

	<!-- sweetalert2 -->
	<script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

	<!-- Leaflet -->
	<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
		integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
		crossorigin=""></script>
	<script src="{{ asset('assets/leaflet/js/leaflet.markercluster.js') }}"></script>

	<script src="{{ asset('assets/leaflet/js/Control.Geocoder.js') }}"></script>
	<script src="{{ asset('assets/leaflet/js/leaflet-routing-machine.js') }}"></script>
	<script src="https://unpkg.com/@geoman-io/leaflet-geoman-free@latest/dist/leaflet-geoman.min.js"></script>

	<script src='https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js'></script>

	<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v1.5.0/mapbox-gl.js'></script>
	<script src="https://unpkg.com/mapbox-gl-leaflet/leaflet-mapbox-gl.js"></script>

	<!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAEY9jbE_zL8SV7c6meCf7-lV3JLcbKnlY" async defer></script> -->
	<script src="https://unpkg.com/leaflet.gridlayer.googlemutant@latest/dist/Leaflet.GoogleMutant.js"></script>

	<script src="{{ asset('assets/leaflet/js/leaflet.shpfile.js') }}"></script>
	<script src="{{ asset('assets/leaflet/js/leaflet.ajax.js') }}"></script>
	<script src="{{ asset('assets/leaflet/js/shp.js') }}"></script>


	<script src="{{ asset('assets/leaflet/js/Leaflet.PolylineMeasure.js') }}"></script>
	<script src="{{ asset('assets/leaflet/js/Control.MiniMap.js') }}"></script>

	<script src="{{ asset('assets/leaflet/js/leaflet-reticle.js') }}"></script>
	<script src="https://rawgit.com/MarcChasse/leaflet.ScaleFactor/master/leaflet.scalefactor.min.js"></script>
	<script src="{{ asset('assets/leaflet/js/leaflet.print.js') }}"></script>



	<!-- Load Esri Leaflet from CDN -->
	<script src="https://unpkg.com/esri-leaflet@3.0.10/dist/esri-leaflet.js"></script>

	<!-- Load Esri Leaflet Vector from CDN -->
	<script src="https://unpkg.com/esri-leaflet-vector@4.1.0/dist/esri-leaflet-vector.js" crossorigin=""></script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.2.0/html5-qrcode.min.js"></script>

	<script>
		const MAPBOX_TOKEN = @json(env('MAPBOX_TOKEN'));
	</script>

	<script>

		var lokasikita = null;
		var initialCenter = [-3.2181547, 117.1137118];
		var initialZoom = 5.5;

		// SATELITE
		var MapBox = L.tileLayer(
			`https://api.mapbox.com/styles/v1/mapbox/satellite-streets-v10/tiles/256/{z}/{x}/{y}?access_token=${MAPBOX_TOKEN}`,
			{
				attribution: 'Imagery from <a href="http://mapbox.com/about/maps/">MapBox</a> &mdash; Map data &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
				id: 'vzett'
			}
		).bringToBack();

		// WORLD
		var Imagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
		}).bringToBack();

		var StreetArc = L.tileLayer(
			'https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {})
			.bringToBack();

		// STREET MAP
		var worldmap = L.tileLayer(
			`https://api.mapbox.com/styles/v1/mapbox/light-v9/tiles/256/{z}/{x}/{y}?access_token=${MAPBOX_TOKEN}`,
			{
				attribution: 'Imagery from <a href="http://mapbox.com/about/maps/">MapBox</a> &mdash; Map data &copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
				id: 'vzett'
			}
		).bringToBack();

		// Bathymetry 
		var bathymetry = L.tileLayer('https://bathy.s3.amazonaws.com/{z}/{x}/{y}.png', {
			attribution: 'NOAA Bathymetry',
			maxZoom: 17,
			tms: true,
			opacity: 1
		}).bringToBack();

		// StART MAP SECTION
		var mapContainer = L.map('map', {
			maxZoom: 20,
			minZoom: 1,
			zoomSnap: 0.25,
			zoomControl: false,
			layers: [StreetArc]
		}).setView(initialCenter, initialZoom);

		// LOCATION KITA 
		var current_position = null;
		function onLocationFound(e) {
			// if position defined, then remove the existing position marker and accuracy circle from the map
			lokasikita = `${e['latlng']['lat']}, ${e['latlng']['lng']}`;
			console.log("masuk", lokasikita, e.latlng);
		}
		function onLocationError(e) {

		}

		mapContainer.on('locationfound', onLocationFound);
		mapContainer.on('locationerror', onLocationError);
		// wrap mapContainer.locate in a function 

		function locate() {
			mapContainer.locate({
				// setView: true,
				maxZoom: 16
			});
		}

		locate();




		// When scan is successful fucntion will produce data
		function onScanSuccess(qrCodeMessage) {
			// document.getElementById("result").innerHTML =
			// '<span class="result">' + qrCodeMessage + "</span>";
			console.log(qrCodeMessage, lokasikita, $("[name=npm]").val());
			$.ajax({
				type: "POST",
				url: "{{url('api/absensi/scan-qr')}}",
				data: {
					"token": qrCodeMessage,
					"coordinate": lokasikita,
					"status_absen": $("[name=status_absen]").val(),
					"npm": $("[name=npm]").val()
				},
				dataType: "JSON",
				success: function (result) {
					if (result['message'] == "success") {
						swal(
							`Berhasil Scan`,
							'',
							'success'
						).then(function () {

						});
					} else {
						swal(
							`Gagal Scan`,
							`${result['message']}`,
							'error'
						).then(function () {
						});
					}
				}
			});
		}

		// When scan is unsuccessful fucntion will produce error message
		function onScanError(errorMessage) {
			// Handle Scan Error
		}

		// Setting up Qr Scanner properties
		var html5QrCodeScanner = new Html5QrcodeScanner("reader", {
			fps: 10,
			qrbox: 250
		});

		// in
		html5QrCodeScanner.render(onScanSuccess, onScanError);


		// $(".form").submit(function(e) { 
		//     e.preventDefault();

		// 	html5QrCodeScanner.render(onScanSuccess, onScanError);
		// });

	</script>

</body>

<!-- Mirrored from FTS-Absensi.dexignlab.com/xhtml/page-login.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Dec 2023 21:16:03 GMT -->

</html>