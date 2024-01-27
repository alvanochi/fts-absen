
<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from dolab.dexignlab.com/xhtml/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Dec 2023 21:14:09 GMT -->
<head>
    <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="keywords" content="">
	<meta name="author" content="">
	<meta name="robots" content="">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Dolab : Dolab Personal Banking Admin Bootstrap 5 Template">
	<meta property="og:title" content="Dolab : Dolab Personal Banking Admin Bootstrap 5 Template">
	<meta property="og:description" content="Dolab : Dolab Personal Banking Admin Bootstrap 5 Template">
	<meta property="og:image" content="social-image.png">
	<meta name="format-detection" content="telephone=no">
	
	<!-- PAGE TITLE HERE -->
	<title>FTS Absensi | {{ ucwords($title) }}</title>
	
	<!-- FAVICONS ICON -->
	<link rel="shortcut icon" type="image/png" href="images/favicon.png">

    <!-- Datatable -->
    <link href="{{ asset('assets/vendor/datatables/css/jquery.dataTables.min.css') }}" rel="stylesheet">

	<!-- sweetalert2 -->
	<link href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}" rel="stylesheet">

	<!-- select2 -->
	<link rel="stylesheet" href="{{ asset('assets/vendor/select2/css/select2.min.css') }}">

	<link href="{{ asset('assets/vendor/jquery-nice-select/css/nice-select.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css') }}" rel="stylesheet">
	<link href="{{ asset('assets/vendor/jquery-autocomplete/jquery-ui.css') }}" rel="stylesheet">
	
	<!-- Style css -->
	
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

	<style>
		/* START TOOLTIP STYLES */
		[tooltip] {
		position: relative; /* opinion 1 */
		}

		/* Applies to all tooltips */
		[tooltip]::before,
		[tooltip]::after {
		text-transform: none; /* opinion 2 */
		font-size: 0.9em; /* opinion 3 */
		line-height: 1;
		user-select: none;
		pointer-events: none;
		position: absolute;
		display: none;
		opacity: 0;
		}
		[tooltip]::before {
		content: "";
		border: 5px solid transparent; /* opinion 4 */
		z-index: 1001; /* absurdity 1 */
		}
		[tooltip]::after {
		content: attr(tooltip); /* magic! */

		/* most of the rest of this is opinion */
		font-family: Helvetica, sans-serif;
		text-align: center;

		/* 
			Let the content set the size of the tooltips 
			but this will also keep them from being obnoxious
			*/
		min-width: 3em;
		max-width: 21em;
		white-space: nowrap;
		overflow: hidden;
		text-overflow: ellipsis;
		padding: 1ch 1.5ch;
		border-radius: 0.3ch;
		box-shadow: 0 1em 2em -0.5em rgba(0, 0, 0, 0.35);
		background: #333;
		color: #fff;
		z-index: 1000; /* absurdity 2 */
		}

		/* Make the tooltips respond to hover */
		[tooltip]:hover::before,
		[tooltip]:hover::after {
		display: block;
		}

		/* don't show empty tooltips */
		[tooltip=""]::before,
		[tooltip=""]::after {
		display: none !important;
		}

		/* FLOW: UP */
		[tooltip]:not([flow])::before,
		[tooltip][flow^="up"]::before {
		bottom: 100%;
		border-bottom-width: 0;
		border-top-color: #333;
		}
		[tooltip]:not([flow])::after,
		[tooltip][flow^="up"]::after {
		bottom: calc(100% + 5px);
		}
		[tooltip]:not([flow])::before,
		[tooltip]:not([flow])::after,
		[tooltip][flow^="up"]::before,
		[tooltip][flow^="up"]::after {
		left: 50%;
		transform: translate(-50%, -0.5em);
		}

		/* FLOW: DOWN */
		[tooltip][flow^="down"]::before {
		top: 100%;
		border-top-width: 0;
		border-bottom-color: #333;
		}
		[tooltip][flow^="down"]::after {
		top: calc(100% + 5px);
		}
		[tooltip][flow^="down"]::before,
		[tooltip][flow^="down"]::after {
		left: 50%;
		transform: translate(-50%, 0.5em);
		}

		/* FLOW: LEFT */
		[tooltip][flow^="left"]::before {
		top: 50%;
		border-right-width: 0;
		border-left-color: #333;
		left: calc(0em - 5px);
		transform: translate(-0.5em, -50%);
		}
		[tooltip][flow^="left"]::after {
		top: 50%;
		right: calc(100% + 5px);
		transform: translate(-0.5em, -50%);
		}

		/* FLOW: RIGHT */
		[tooltip][flow^="right"]::before {
		top: 50%;
		border-left-width: 0;
		border-right-color: #333;
		right: calc(0em - 5px);
		transform: translate(0.5em, -50%);
		}
		[tooltip][flow^="right"]::after {
		top: 50%;
		left: calc(100% + 5px);
		transform: translate(0.5em, -50%);
		}

		/* KEYFRAMES */
		@keyframes tooltips-vert {
		to {
			opacity: 0.9;
			transform: translate(-50%, 0);
		}
		}

		@keyframes tooltips-horz {
		to {
			opacity: 0.9;
			transform: translate(0, -50%);
		}
		}

		/* FX All The Things */
		[tooltip]:not([flow]):hover::before,
		[tooltip]:not([flow]):hover::after,
		[tooltip][flow^="up"]:hover::before,
		[tooltip][flow^="up"]:hover::after,
		[tooltip][flow^="down"]:hover::before,
		[tooltip][flow^="down"]:hover::after {
		animation: tooltips-vert 300ms ease-out forwards;
		}

		[tooltip][flow^="left"]:hover::before,
		[tooltip][flow^="left"]:hover::after,
		[tooltip][flow^="right"]:hover::before,
		[tooltip][flow^="right"]:hover::after {
		animation: tooltips-horz 300ms ease-out forwards;
		}
		.dataTables_wrapper .dataTables_paginate .paginate_button.previous.disabled, .dataTables_wrapper .dataTables_paginate .paginate_button.next.disabled {
			font-size: 11px;
		} 
		.dataTables_wrapper .dataTables_paginate span .paginate_button {
			height: 25px;
    		width: 25px;
			line-height: 25px;
			font-size: 12px;
		}
		.dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_processing, .dataTables_wrapper .dataTables_paginate {
			margin-top: 10px;
		}

		/* .select2-container {
			z-index: 1060;
		} */

		.cat {
            /* margin: 4px; */
            background-color: white;
            border-radius: 7px !important;
            /* border: 1px solid #fff; */
            /* overflow: hidden; */
            float: left;
        }

        .cat label {
            float: left;
            line-height: 1.7em;
            width: auto;
            height: 1.5em;
            cursor: pointer;
        }

        .cat label span {
            text-align: center;
            padding: 3px 0;
            display: block;
            font-size: 13px;
            padding-left: 10px;
            padding-right: 10px;
        }

        .cat label input {
            position: absolute;
            display: none;
            color: #000 !important;
        }

        /* selects all of the text within the input element and changes the color of the text */
        .cat label input+span {
            color: #000;
        }


        /* This will declare how a selected input will look giving generic properties */
        .cat input:checked+span {
            color: #1967d2;
            /* text-shadow: 0 0  6px rgba(0, 0, 0, 0.8); */
        }
	</style>

    @yield('css')
	
</head>
<body>

    <!--*******************
        Preloader start
    ********************-->
	<div id="preloader">
        <div class="inner">
            <span>Loading </span>
            <div class="loading">  
            </div>
        </div>
    </div>
    <!--*******************
        Preloader end
    ********************-->

    <!--**********************************
        Main wrapper start
    ***********************************-->
    <div id="main-wrapper">
		<div class="animation">
			<span class="circle one"></span>
			<span class="circle two"></span>
			<span class="circle three"></span>
			<span class="circle four"></span>
			<span class="line-1 ">
				<svg width="1920" height="450" viewBox="0 0 1920 450" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path opacity="0.3" d="M0 155L95.4613 293.923C106.459 309.928 131.116 305.943 136.512 287.289L209.86 33.7127C215.892 12.8576 244.803 11.2033 253.175 31.2341L344.838 250.546C352.224 268.217 376.708 269.648 386.102 252.958L519.839 15.3693C529.061 -1.01332 552.975 -0.0134089 560.797 17.0818L716.503 357.389C724.454 374.766 748.899 375.43 757.782 358.51L902.518 82.8223C911.524 65.6685 936.406 66.653 944.028 84.4648L1093.06 432.731C1101.14 451.601 1128.01 451.247 1135.58 432.172L1291.33 39.9854C1298.27 22.5135 1322.1 20.2931 1332.14 36.1824L1473.74 260.126C1482.47 273.922 1502.38 274.494 1511.88 261.221L1667.88 43.3025C1678.17 28.9257 1700.16 31.0533 1707.5 47.1365L1844.91 348.06C1853.69 367.287 1881.58 365.486 1887.81 345.29L1970 79" stroke="url(#paint0_linear_332_3757)" stroke-opacity="0.4" stroke-width="6" stroke-linecap="round"/>
					<defs>
					<linearGradient id="paint0_linear_332_3757" x1="1946.24" y1="352.062" x2="-1.52124" y2="345.607" gradientUnits="userSpaceOnUse">
					<stop offset="0" stop-color="#6E4AFF"/>
					<stop offset="0.479167" stop-color="#E43BFF"/>
					<stop offset="1" stop-color="#6E4AFF"/>
					</linearGradient>
					</defs>
				</svg>
			</span>
			<span class="line-2">
				<svg width="1920" height="459" viewBox="0 0 1920 459" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M0 89L103.191 296.201C112.034 313.958 137.703 312.941 145.114 294.54L224.847 96.5574C232.264 78.141 257.962 77.1423 266.786 94.9275L352.649 267.995C360.863 284.553 384.264 285.148 393.31 269.03L516.226 50.0159C525.164 34.0902 548.205 34.4325 556.666 50.6167L713.497 350.608C721.71 366.318 743.86 367.222 753.326 352.234L901.462 117.684C911.188 102.286 934.102 103.763 941.771 120.282L1091.14 442.062C1099.38 459.816 1124.62 459.817 1132.86 442.064L1303.17 75.2544C1310.64 59.1685 1332.73 57.2308 1342.89 71.7713L1469.94 253.703C1479.15 266.893 1498.71 266.794 1507.78 253.511L1671.82 13.4627C1681.74 -1.05968 1703.63 0.478486 1711.42 16.2459L1844.42 285.267C1853.64 303.905 1880.89 301.723 1887.02 281.857L1970 13" stroke="url(#paint0_linear_332_3758)" stroke-opacity="0.4" stroke-width="6" stroke-linecap="round"/>
					<defs>
					<linearGradient id="paint0_linear_332_3758" x1="1946.24" y1="286.062" x2="-1.52105" y2="279.607" gradientUnits="userSpaceOnUse">
					<stop offset="0" stop-color="#6E4AFF"/>
					<stop offset="0.479167" stop-color="#E43BFF"/>
					<stop offset="1" stop-color="#6E4AFF"/>
					</linearGradient>
					</defs>
				</svg>	
			</span>
			
		</div>

        <!--**********************************
            Nav header start
        ***********************************-->
		<div class="nav-header">
            <a href="{{url('dashboard')}}" class="brand-logo">
				<div class="logo">
					<svg  class="logo-abbr" width="43" height="34" viewBox="0 0 43 34" fill="none" xmlns="http://www.w3.org/2000/svg">
						<rect x="22.6154" width="19.6154" height="6.53846" rx="3.26923" fill="white"/>
						<rect x="22.6154" y="9.15387" width="19.6154" height="6.53846" rx="3.26923" fill="white"/>
						<rect x="22.6154" y="18.3077" width="19.6154" height="6.53846" rx="3.26923" fill="white"/>
						<rect x="0.384583" y="18.3077" width="19.6154" height="6.53846" rx="3.26923" fill="white"/>
						<rect x="22.6154" y="27.4615" width="19.6154" height="6.53846" rx="3.26923" fill="white"/>
						<rect x="0.384583" y="27.4615" width="19.6154" height="6.53846" rx="3.26923" fill="white"/>
					</svg>
					<svg class="brand-title" width="124" height="33" viewBox="0 0 124 33" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M11.65 2.516C14.758 2.516 17.474 3.132 19.798 4.364C22.122 5.596 23.914 7.332 25.174 9.572C26.462 11.784 27.106 14.346 27.106 17.258C27.106 20.142 26.462 22.704 25.174 24.944C23.914 27.184 22.108 28.92 19.756 30.152C17.432 31.384 14.73 32 11.65 32H0.604V2.516H11.65ZM11.188 25.784C13.904 25.784 16.018 25.042 17.53 23.558C19.042 22.074 19.798 19.974 19.798 17.258C19.798 14.542 19.042 12.428 17.53 10.916C16.018 9.404 13.904 8.648 11.188 8.648H7.786V25.784H11.188ZM41.7876 32.336C39.4916 32.336 37.4196 31.846 35.5716 30.866C33.7516 29.886 32.3096 28.486 31.2456 26.666C30.2096 24.846 29.6916 22.718 29.6916 20.282C29.6916 17.874 30.2236 15.76 31.2876 13.94C32.3516 12.092 33.8076 10.678 35.6556 9.698C37.5036 8.718 39.5756 8.228 41.8716 8.228C44.1676 8.228 46.2396 8.718 48.0876 9.698C49.9356 10.678 51.3916 12.092 52.4556 13.94C53.5196 15.76 54.0516 17.874 54.0516 20.282C54.0516 22.69 53.5056 24.818 52.4136 26.666C51.3496 28.486 49.8796 29.886 48.0036 30.866C46.1556 31.846 44.0836 32.336 41.7876 32.336ZM41.7876 26.12C43.1596 26.12 44.3216 25.616 45.2736 24.608C46.2536 23.6 46.7436 22.158 46.7436 20.282C46.7436 18.406 46.2676 16.964 45.3156 15.956C44.3916 14.948 43.2436 14.444 41.8716 14.444C40.4716 14.444 39.3096 14.948 38.3856 15.956C37.4616 16.936 36.9996 18.378 36.9996 20.282C36.9996 22.158 37.4476 23.6 38.3436 24.608C39.2676 25.616 40.4156 26.12 41.7876 26.12ZM65.0438 0.92V32H57.8618V0.92H65.0438ZM68.8205 20.24C68.8205 17.832 69.2685 15.718 70.1645 13.898C71.0885 12.078 72.3345 10.678 73.9025 9.698C75.4705 8.718 77.2205 8.228 79.1525 8.228C80.8045 8.228 82.2465 8.564 83.4785 9.236C84.7385 9.908 85.7045 10.79 86.3765 11.882V8.564H93.5585V32H86.3765V28.682C85.6765 29.774 84.6965 30.656 83.4365 31.328C82.2045 32 80.7625 32.336 79.1105 32.336C77.2065 32.336 75.4705 31.846 73.9025 30.866C72.3345 29.858 71.0885 28.444 70.1645 26.624C69.2685 24.776 68.8205 22.648 68.8205 20.24ZM86.3765 20.282C86.3765 18.49 85.8725 17.076 84.8645 16.04C83.8845 15.004 82.6805 14.486 81.2525 14.486C79.8245 14.486 78.6065 15.004 77.5985 16.04C76.6185 17.048 76.1285 18.448 76.1285 20.24C76.1285 22.032 76.6185 23.46 77.5985 24.524C78.6065 25.56 79.8245 26.078 81.2525 26.078C82.6805 26.078 83.8845 25.56 84.8645 24.524C85.8725 23.488 86.3765 22.074 86.3765 20.282ZM105.936 11.882C106.608 10.79 107.574 9.908 108.834 9.236C110.094 8.564 111.536 8.228 113.16 8.228C115.092 8.228 116.842 8.718 118.41 9.698C119.978 10.678 121.21 12.078 122.106 13.898C123.03 15.718 123.492 17.832 123.492 20.24C123.492 22.648 123.03 24.776 122.106 26.624C121.21 28.444 119.978 29.858 118.41 30.866C116.842 31.846 115.092 32.336 113.16 32.336C111.508 32.336 110.066 32.014 108.834 31.37C107.602 30.698 106.636 29.816 105.936 28.724V32H98.7544V0.92H105.936V11.882ZM116.184 20.24C116.184 18.448 115.68 17.048 114.672 16.04C113.692 15.004 112.474 14.486 111.018 14.486C109.59 14.486 108.372 15.004 107.364 16.04C106.384 17.076 105.894 18.49 105.894 20.282C105.894 22.074 106.384 23.488 107.364 24.524C108.372 25.56 109.59 26.078 111.018 26.078C112.446 26.078 113.664 25.56 114.672 24.524C115.68 23.46 116.184 22.032 116.184 20.24Z" fill="white"/>
				</svg>
				</div>

            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span>
					<span class="line"></span>
					<span class="line"></span>
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path opacity="0.4" d="M16.7548 0.333313H20.7051C22.341 0.333313 23.6667 1.67014 23.6667 3.31994V7.30359C23.6667 8.95339 22.341 10.2902 20.7051 10.2902H16.7548C15.1188 10.2902 13.7932 8.95339 13.7932 7.30359V3.31994C13.7932 1.67014 15.1188 0.333313 16.7548 0.333313Z" fill="white"/>
						<path fill-rule="evenodd" clip-rule="evenodd" d="M3.29492 0.333313H7.24522C8.8812 0.333313 10.2068 1.67014 10.2068 3.31994V7.30359C10.2068 8.95339 8.8812 10.2902 7.24522 10.2902H3.29492C1.65894 10.2902 0.333313 8.95339 0.333313 7.30359V3.31994C0.333313 1.67014 1.65894 0.333313 3.29492 0.333313ZM3.29492 13.7097H7.24522C8.8812 13.7097 10.2068 15.0466 10.2068 16.6964V20.68C10.2068 22.3287 8.8812 23.6666 7.24522 23.6666H3.29492C1.65894 23.6666 0.333313 22.3287 0.333313 20.68V16.6964C0.333313 15.0466 1.65894 13.7097 3.29492 13.7097ZM20.705 13.7097H16.7547C15.1188 13.7097 13.7931 15.0466 13.7931 16.6964V20.68C13.7931 22.3287 15.1188 23.6666 16.7547 23.6666H20.705C22.341 23.6666 23.6666 22.3287 23.6666 20.68V16.6964C23.6666 15.0466 22.341 13.7097 20.705 13.7097Z" fill="white"/>
					</svg> 
                </div>
            </div>
		
        </div>
        <!--**********************************
            Nav header end
        ***********************************-->
		
		 
		
		<!--**********************************
            Header start
        ***********************************-->
        <div class="header">
            <div class="header-content">
                <nav class="navbar navbar-expand">
                    <div class="collapse navbar-collapse justify-content-between">
                        <div class="header-left">
							<div class="dashboard_bar">
                                {{ ucwords($title) }}
                            </div>
                        </div>
                        <!-- <ul class="navbar-nav header-right">
							<li class="nav-item d-flex align-items-start">
								<div class="input-group search-area">
									<input type="text" class="form-control" id="search" placeholder="Search here...">
									<span class="input-group-text"><a href="javascript:void(0)"><svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path opacity="1" d="M16.6751 19.4916C16.2194 19.036 16.2194 18.2973 16.6751 17.8417C17.1307 17.3861 17.8694 17.3861 18.325 17.8417L22.9916 22.5084C23.4473 22.964 23.4473 23.7027 22.9916 24.1583C22.536 24.6139 21.7973 24.6139 21.3417 24.1583L16.6751 19.4916Z" fill="white"/>
										<path d="M12.8333 18.6667C16.055 18.6667 18.6667 16.055 18.6667 12.8334C18.6667 9.61169 16.055 7.00002 12.8333 7.00002C9.61166 7.00002 6.99999 9.61169 6.99999 12.8334C6.99999 16.055 9.61166 18.6667 12.8333 18.6667ZM12.8333 21C8.323 21 4.66666 17.3437 4.66666 12.8334C4.66666 8.32303 8.323 4.66669 12.8333 4.66669C17.3436 4.66669 21 8.32303 21 12.8334C21 17.3437 17.3436 21 12.8333 21Z" fill="white"/>
										</svg>
										</a></span>
								</div>
							</li>
                        </ul> -->
						<div class="header-profile2 ">
							<ul class="navbar-nav header-right me-sm-4"> 
								<li class="nav-item">
									<!-- <a class="nav-link bell-link" onClick="changeTheme('dar')" href="javascript:void(0);">
										<i class="fa fa-moon" style="color: white; font-size: 20px; padding: 5px;"></i>
									</a> -->
									<div class="cat nav-link bell-link"> 
										<label>
											<input type="checkbox" name="themecolor" id="themecolor">
											<span id="iconTheme">
												<i class="fa fa-moon" style="color: white; font-size: 20px; padding: 5px;"></i>
											</span>
										</label>  
									</div>
								</li>	 
							</ul>
							<a class="nav-link user-profile" href="javascript:void(0);"  role="button" data-bs-toggle="dropdown">
								<div class="header-info2 d-flex align-items-center">
									<!-- <img src="{{ asset('assets/images/placeholder.jpg') }}" alt=""> -->
									<div class="d-flex align-items-center sidebar-info">
										<div class="user-info">
											<span class="font-w500 d-block  fs-5 text-white" id="nameUser">Adam Joe</span>
											<small class="text-end font-w400" id="emailUser">Admin</small>
										</div>
										<svg width="14" height="8" viewBox="0 0 14 8" fill="none" xmlns="http://www.w3.org/2000/svg">
											<path d="M12.8334 1.08331L7.00002 6.91665L1.16669 1.08331" stroke="#FFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
										</svg>
											
									</div>
									
								</div>
							</a>
							<div class="dropdown-menu profile dropdown-menu-end">
								<!-- <a href="app-profile.html" class="dropdown-item ai-icon ">
									<svg  xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
									<span class="ms-2">Profile </span>
								</a>  -->
								<a href="javascript:void(0);" onClick="logouts()" class="dropdown-item ai-icon">
									<svg  xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
									<span class="ms-2">Logout </span>
								</a>
							</div>
						
						</div>
                    </div>
				</nav>
			</div>
		</div>
        <!--**********************************
            Header end ti-comment-alt
        ***********************************-->

        <!--**********************************
            Sidebar start
        ***********************************-->
		<div class="dlabnav">
			<div class="dlabnav-scroll">
				
				<ul class="metismenu" id="menu">
					<li><a class="has-arrow " href="{{ url('dashboard') }}" aria-expanded="false">
						<div class="menu-icon">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g id="IconlyHome"><g id="Home">
								<path id="Home_2" d="M9.13478 20.7733V17.7156C9.13478 16.9351 9.77217 16.3023 10.5584 16.3023H13.4326C13.8102 16.3023 14.1723 16.4512 14.4393 16.7163C14.7063 16.9813 14.8563 17.3408 14.8563 17.7156V20.7733C14.8539 21.0978 14.9821 21.4099 15.2124 21.6402C15.4427 21.8705 15.7561 22 16.0829 22H18.0438C18.9596 22.0023 19.8388 21.6428 20.4872 21.0008C21.1356 20.3588 21.5 19.487 21.5 18.5778V9.86686C21.5 9.13246 21.1721 8.43584 20.6046 7.96467L13.934 2.67587C12.7737 1.74856 11.1111 1.7785 9.98539 2.74698L3.46701 7.96467C2.87274 8.42195 2.51755 9.12064 2.5 9.86686V18.5689C2.5 20.4639 4.04738 22 5.95617 22H7.87229C8.55123 22 9.103 21.4562 9.10792 20.7822L9.13478 20.7733Z" fill="#130F26"/>
								</g></g>
							</svg>
						</div>	
							<span class="nav-text">Dashboard</span>
						</a> 
					</li> 

                    <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
						<div class="menu-icon">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<g id="IconlyActivity">
								<g id="Activity">
								<path id="Activity_2" fill-rule="evenodd" clip-rule="evenodd" d="M17.1799 4.41C17.1799 3.08 18.2599 2 19.5899 2C20.9199 2 21.9999 3.08 21.9999 4.41C21.9999 5.74 20.9199 6.82 19.5899 6.82C18.2599 6.82 17.1799 5.74 17.1799 4.41ZM13.3298 14.7593L16.2198 11.0303L16.1798 11.0503C16.3398 10.8303 16.3698 10.5503 16.2598 10.3003C16.1508 10.0503 15.9098 9.8803 15.6508 9.8603C15.3798 9.8303 15.1108 9.9503 14.9498 10.1703L12.5308 13.3003L9.75976 11.1203C9.58976 10.9903 9.38976 10.9393 9.18976 10.9603C8.99076 10.9903 8.81076 11.0993 8.68976 11.2593L5.73076 15.1103L5.66976 15.2003C5.49976 15.5193 5.57976 15.9293 5.87976 16.1503C6.01976 16.2403 6.16976 16.3003 6.33976 16.3003C6.57076 16.3103 6.78976 16.1893 6.92976 16.0003L9.43976 12.7693L12.2898 14.9103L12.3798 14.9693C12.6998 15.1393 13.0998 15.0603 13.3298 14.7593ZM15.4498 3.7803C15.4098 4.0303 15.3898 4.2803 15.3898 4.5303C15.3898 6.7803 17.2098 8.5993 19.4498 8.5993C19.6998 8.5993 19.9398 8.5703 20.1898 8.5303V16.5993C20.1898 19.9903 18.1898 22.0003 14.7898 22.0003H7.40076C3.99976 22.0003 1.99976 19.9903 1.99976 16.5993V9.2003C1.99976 5.8003 3.99976 3.7803 7.40076 3.7803H15.4498Z" fill="#130F26"/>
								</g></g>
								</svg>	
						</div>		
							<span class="nav-text">Nilai</span>
						</a> 
						<ul aria-expanded="false">
							<li><a href="{{ url('nilai/add') }}">Tambah Nilai</a></li>
							<li><a href="{{ url('nilai_cpl') }}">Nilai CPL</a></li>
							<li><a href="{{ url('nilai') }}">Nilai CPMK</a></li> 
						</ul>
					</li>

                    <li>
						<a class="has-arrow " href="javascript:void()" aria-expanded="false">
							<div class="menu-icon">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path opacity="0.4" d="M2.00024 11.0785C2.05024 13.4165 2.19024 17.4155 2.21024 17.8565C2.28124 18.7995 2.64224 19.7525 3.20424 20.4245C3.98624 21.3675 4.94924 21.7885 6.29224 21.7885C8.14824 21.7985 10.1942 21.7985 12.1812 21.7985C14.1762 21.7985 16.1122 21.7985 17.7472 21.7885C19.0712 21.7885 20.0642 21.3565 20.8362 20.4245C21.3982 19.7525 21.7592 18.7895 21.8102 17.8565C21.8302 17.4855 21.9302 13.1445 21.9902 11.0785H2.00024Z" fill="#763ed0"/>
								<path d="M11.2455 15.3842V16.6782C11.2455 17.0922 11.5815 17.4282 11.9955 17.4282C12.4095 17.4282 12.7455 17.0922 12.7455 16.6782V15.3842C12.7455 14.9702 12.4095 14.6342 11.9955 14.6342C11.5815 14.6342 11.2455 14.9702 11.2455 15.3842Z" fill="#763ed0"/>
								<path fill-rule="evenodd" clip-rule="evenodd" d="M10.2114 14.5564C10.1114 14.9194 9.76237 15.1514 9.38437 15.1014C6.83337 14.7454 4.39537 13.8404 2.33737 12.4814C2.12637 12.3434 2.00037 12.1074 2.00037 11.8554V8.3894C2.00037 6.2894 3.71237 4.5814 5.81737 4.5814H7.78437C7.97237 3.1294 9.20237 2.0004 10.7044 2.0004H13.2864C14.7874 2.0004 16.0184 3.1294 16.2064 4.5814H18.1834C20.2824 4.5814 21.9904 6.2894 21.9904 8.3894V11.8554C21.9904 12.1074 21.8634 12.3424 21.6544 12.4814C19.5924 13.8464 17.1444 14.7554 14.5764 15.1104C14.5414 15.1154 14.5074 15.1174 14.4734 15.1174C14.1344 15.1174 13.8314 14.8884 13.7464 14.5524C13.5444 13.7564 12.8214 13.1994 11.9904 13.1994C11.1484 13.1994 10.4334 13.7444 10.2114 14.5564ZM13.2864 3.5004H10.7044C10.0314 3.5004 9.46937 3.9604 9.30137 4.5814H14.6884C14.5204 3.9604 13.9584 3.5004 13.2864 3.5004Z" fill="#B9A8FF"/>
								</svg>
							</div>	
							<span class="nav-text">Master Data</span>
						</a>
						<ul aria-expanded="false">
							<li><a href="{{ url('profile_lulusan') }}">Profile Lulusan</a></li>
							<li><a href="{{ url('cpl') }}">Capaian Profile Lulusan</a></li>
							<li><a href="{{ url('bahan_kajian') }}">Bahan Kajian</a></li>
							<li><a href="{{ url('matkul') }}">Mata Kuliah</a></li> 
							<li><a href="{{ url('capaian_matkul') }}">Capaian Pembelajaran Mata Kuliah</a></li>
							<li><a href="{{ url('kriteria_penilaian') }}">Soal</a></li>
						</ul>
					</li>
					
					<li><a class="has-arrow " href="{{ url('mahasiswa') }}" aria-expanded="false">
						<div class="menu-icon">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M9.34933 14.8577C5.38553 14.8577 2 15.47 2 17.9174C2 20.3666 5.364 21 9.34933 21C13.3131 21 16.6987 20.3877 16.6987 17.9404C16.6987 15.4911 13.3347 14.8577 9.34933 14.8577Z" fill="#B9A8FF"/>
								<path opacity="0.4" d="M9.34935 12.5248C12.049 12.5248 14.2124 10.4062 14.2124 7.76241C14.2124 5.11865 12.049 3 9.34935 3C6.65072 3 4.48633 5.11865 4.48633 7.76241C4.48633 10.4062 6.65072 12.5248 9.34935 12.5248Z" fill="#763ed0"/>
								<path opacity="0.4" d="M16.1734 7.84875C16.1734 9.19507 15.7605 10.4513 15.0364 11.4948C14.9611 11.6021 15.0276 11.7468 15.1587 11.7698C15.3407 11.7995 15.5276 11.8177 15.7184 11.8216C17.6167 11.8704 19.3202 10.6736 19.7908 8.87118C20.4885 6.19676 18.4415 3.79543 15.8339 3.79543C15.5511 3.79543 15.2801 3.82418 15.0159 3.87688C14.9797 3.88454 14.9405 3.90179 14.921 3.93246C14.8955 3.97174 14.9141 4.02253 14.9395 4.05607C15.7233 5.13216 16.1734 6.44207 16.1734 7.84875Z" fill="#763ed0"/>
								<path d="M21.7791 15.1693C21.4317 14.444 20.5932 13.9466 19.3172 13.7023C18.7155 13.5586 17.0853 13.3545 15.5697 13.3832C15.5472 13.3861 15.5344 13.4014 15.5325 13.411C15.5295 13.4263 15.5364 13.4493 15.5658 13.4656C16.2663 13.8048 18.9738 15.2805 18.6333 18.3928C18.6186 18.5289 18.7292 18.6439 18.8671 18.6247C19.5335 18.5318 21.2478 18.1705 21.7791 17.0475C22.0736 16.4534 22.0736 15.7635 21.7791 15.1693Z" fill="#B9A8FF"/>
								</svg>
						</div>	
							
							<span class="nav-text">User Management</span>
						</a> 
						<!-- <ul aria-expanded="false">
							<li><a href="{{ url('mahasiswa') }}">Mahasiswa</a></li>
							<li><a href="{{url('userManagement')}}">Pengguna Sistem</a></li>  
						</ul> -->
					</li>
					
 
					
				</ul>
				
				<div class="copyright">
					<p class="fs-14"><strong>doLab Personal Banking Admin</strong> © 2022 All Rights Reserved</p>
					<p class="fs-14">Made with <span class="heart"></span> by DexignLab</p>
				</div>
			</div>
		</div>
		
        <!--**********************************
            Sidebar end
        ***********************************-->
		
		<!--**********************************
            Content body start
        ***********************************-->
        <div class="content-body">
            <!-- row -->
			<div class="container-fluid">
				
                @yield('content')
            
                <!--**********************************
                    Footer start
                ***********************************-->
                    <div class="footer">
                        <div class="copyright">
                            <p>Copyright © Designed &amp; Developed by <a href="javascript:void(0);">Guido Tamara</a> 2023</p>
                        </div>
                    </div>
                <!--**********************************
                    Footer end
                ***********************************-->

        	</div>
			<!-- Modal -->
			<div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
					<h5 class="modal-title" id="exampleModalLabel1">Add Person</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body">
						<label for="PersonName1" class="form-label d-block">Enter Name</label>
						<input type="text" id="PersonName1" class="form-control w-100 mb-3" placeholder="Username">
						<label for="PersonPosition1" class="form-label d-block">Enter Position</label>
						<input type="text" id="PersonPosition1" class="form-control w-100" placeholder="Position">
						
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary btn-sm">Save changes</button>
					</div>
				</div>
				</div>
			</div>	
			<!-- /Modal -->	
		</div>		
        <!--**********************************
            Content body end
        ***********************************-->
		
			
		
		<!--**********************************
           Support ticket button start
        ***********************************-->
		
        <!--**********************************
           Support ticket button end
        ***********************************-->
	

</div>
    <!--**********************************
        Main wrapper end
    ***********************************-->

    <!--**********************************
        Scripts
    ***********************************-->
    <!-- Required vendors -->
    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
	<script src="{{ asset('assets/vendor/chart.js/Chart.bundle.min.js') }}"></script>
	<script src="{{ asset('assets/vendor/jquery-nice-select/js/jquery.nice-select.min.js') }}"></script> 
	<!-- Apex Chart -->
	<script src="{{ asset('assets/vendor/apexchart/apexchart.js') }}"></script>
	<!-- Chart piety plugin files -->
    <script src="{{ asset('assets/vendor/peity/jquery.peity.min.js') }}"></script> 
	<!-- Chartist -->
   <script src="{{ asset('assets/vendor/chartist/js/chartist.min.js') }}"></script> 
   <script src="{{ asset('assets/vendor/jquery-autocomplete/jquery-ui.js') }}"></script> 
	<!-- <script src="{{ asset('assets/./js/dashboard/dashboard-2.js') }}"></script> -->
	<!-- Dashboard 1 -->
	
	<script src="{{ asset('assets/js/custom.min.js') }}"></script>
	<script src="{{ asset('assets/js/dlabnav-init.js') }}"></script>
	<script src="{{ asset('assets/js/demo.js') }}"></script>
    <!-- <script src="{{ asset('assets/js/styleSwitcher.js') }}"></script> -->

    <!-- Datatable -->
    <script src="{{ asset('assets/vendor/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins-init/datatables.init.js') }}"></script>

	<!-- sweetalert2 -->
	<script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.js') }}"></script>

	<!-- select2 -->
	<script src="{{ asset('assets/vendor/select2/js/select2.full.min.js') }}"></script>

	<script>
		// $('[data-toggle="tooltip"]').tooltip();

		//change the theme color controller
		var stTheme = '';
		if(getCookie('themeBg')){
			if(getCookie('themeBg') == "theme_2"){
				$("#iconTheme").html(`<i class="fa fa-sun" style="color: white; font-size: 20px; padding: 5px;"></i>`);
			}else{
				$("#iconTheme").html(`<i class="fa fa-moon" style="color: white; font-size: 20px; padding: 5px;"></i>`);
			}
        }else{ 
			$("#iconTheme").html(`<i class="fa fa-moon" style="color: white; font-size: 20px; padding: 5px;"></i>`);
		}
		$('input[name="themecolor"]').on('click', function() {  
			if(this.value == 1){			// 1 dark
				stTheme = 'theme_2';
				$("#iconTheme").html(`<i class="fa fa-sun" style="color: white; font-size: 20px; padding: 5px;"></i>`);
			}else{							// 0 light
				stTheme = 'theme_4';
				$("#iconTheme").html(`<i class="fa fa-moon" style="color: white; font-size: 20px; padding: 5px;"></i>`);
			}
			body.attr('data-theme', stTheme);
			setCookie('themeBg', stTheme);
		});

		var notFound = `<img src="{{ asset('assets/not-found.gif') }}" style="width: 70%; left: 15%; position: relative;">`;

		if($("input[type='checkbox']").is(":checked")){
			$("input[type='checkbox']").val(1);
		}else{
			$("input[type='checkbox']").val(0);
		}
		$("input[type='checkbox']").on('change', function(){
			$(this).val(this.checked ? 1 : 0); 
			$(".status").val(this.checked ? 1 : 0);  
		});

		$(".select2").select2({ 
		});

		$(".single-select").select2({
			dropdownParent: $("#addModal")
		});
		$(".single-select-edit").select2({
			dropdownParent: $("#editModal")
		});
		$(".single-select-relation").select2({
			dropdownParent: $("#relationModal")
		});

		$.ajaxSetup({
			// data: csfrData
			"beforeSend": function (xhr) {
				xhr.setRequestHeader("Authorization", "Bearer " + getCookie('tokens'));
			}, 
		}); 

		var getUser = null;
		$.ajax({
			type: "GET",
			url: `{{ url('api/get_user') }}`,
			// data: {
			//     "searchData": val, 
			// },
			dataType: "JSON",
			success: function(res) { 
				getUser = res['data'];
				$("#nameUser").html(getUser['name']);
				$("#emailUser").html(getUser['email']);
				
				if(res['status'] != 200){
					deleteCookie('tokens');
					window.location.href = "{{url('login')}}";
				} 
			}
		});

		function logouts(){
			deleteCookie('tokens');
			window.location.href = "{{url('login')}}";
		}

		function toNumberArr(value) {
         return Number(value);
      }
	</script>
	
    @yield('js')
</body>

<!-- Mirrored from dolab.dexignlab.com/xhtml/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Wed, 20 Dec 2023 21:15:44 GMT -->
</html>
