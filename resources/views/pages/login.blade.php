@extends('index')
@push('css')
<style>body { background-image: url('assets/media/auth/bg4.jpg'); } [data-bs-theme="dark"] body { background-image: url('assets/media/auth/bg4-dark.jpg'); }</style>
@endpush
@section('content')
<!--begin::Root-->
<div class="d-flex flex-column flex-root">
    <!--begin::Page bg image-->
    <!--end::Page bg image-->
    <!--begin::Authentication - Sign-in -->
    <login-component></login-component>
    <!--end::Authentication - Sign-in-->
</div>
<!--end::Root-->
@endsection
@push('scripts')
<!--begin::Custom Javascript(used for this page only)-->
{{-- <script src="{{ asset('assets/js/custom/authentication/sign-in/general.js?v='.rand()) }}"></script> --}}
<!--end::Custom Javascript-->
@endpush