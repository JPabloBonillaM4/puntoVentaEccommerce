<!--begin::Javascript-->
		<script>
			window.Laravel = {!! json_encode([
                'csrfToken' => csrf_token(),
                'baseUrl' =>  url('/'),
                'userData' =>  Auth::user(),
				'errors' => $errors->all(),
            ]) !!}
		</script>
		<!--begin::Global Javascript Bundle(mandatory for all pages)-->
		<script src="{{ asset('assets/plugins/global/plugins.bundle.js?v='.rand()) }}"></script>
		<script src="assets/js/scripts.bundle.js"></script>
		<!--end::Global Javascript Bundle-->
		<!--begin::Vendors Javascript(used for this page only)-->
		<script src="{{ asset('assets/plugins/custom/fullcalendar/fullcalendar.bundle.js?v='.rand()) }}"></script>
		<script src="https://cdn.amcharts.com/lib/5/index.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/radar.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/map.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/continentsLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/usaLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js"></script>
		<script src="https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js"></script>
		<script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js?v='.rand()) }}"></script>
		<!--end::Vendors Javascript-->
		<!--begin::Custom Javascript(used for this page only)-->
		<script src="{{ asset('assets/js/widgets.bundle.js?v='.rand()) }}"></script>
		<script src="{{ asset('assets/js/custom/widgets.js?v='.rand()) }}"></script>
		<script src="{{ asset('assets/js/custom/apps/chat/chat.js?v='.rand()) }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/upgrade-plan.js?v='.rand()) }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/create-campaign.js?v='.rand()) }}"></script>
		<script src="{{ asset('assets/js/custom/utilities/modals/users-search.js?v='.rand()) }}"></script>
		<!--end::Custom Javascript-->
		<!--end::Javascript-->
		@stack('scripts')
		{{-- VITE --}}
		@vite('resources/js/app.js')
	</body>
	<!--end::Body-->
</html>