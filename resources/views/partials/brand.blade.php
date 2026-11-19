{{-- Included logo file: public/images/brand-logo.png --}}
@if (is_file(public_path('images/brand-logo.png')))
    <img src="{{ asset('images/brand-logo.png') }}" class="brand-logo" alt="W Articles logo">
@else
    <span class="brand-logo-missing">Add your logo</span>
@endif
