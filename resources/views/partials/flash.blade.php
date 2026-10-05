{{-- Success, error and validation banners. Included by the dashboard layout. --}}
@if (session('success'))
    <div class="mb-6"><x-alert type="success">{{ session('success') }}</x-alert></div>
@endif

@if (session('error'))
    <div class="mb-6"><x-alert type="error">{{ session('error') }}</x-alert></div>
@endif

@if ($errors->any())
    <div class="mb-6"><x-alert type="error">Some fields need a second look. Fix the marked ones and try again.</x-alert></div>
@endif