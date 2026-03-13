@inject('layoutHelper', 'JeroenNoten\LaravelAdminLte\Helpers\LayoutHelper')

@php( $dashboard_url = View::getSection('dashboard_url') ?? config('adminlte.dashboard_url', 'home') )

@if (config('adminlte.use_route_url', false))
    @php( $dashboard_url = $dashboard_url ? route($dashboard_url) : '' )
@else
    @php( $dashboard_url = $dashboard_url ? url($dashboard_url) : '' )
@endif

<a href="{{ $dashboard_url }}"
    @if($layoutHelper->isLayoutTopnavEnabled())
        class="navbar-brand {{ config('adminlte.classes_brand') }}"
    @else
        class="brand-link {{ config('adminlte.classes_brand') }}"
    @endif>

    @if(config('adminlte.logo_mini'))
        @php($appName = trim((string) (config('app.name') ?? '')) ?: 'MDIA')
        <span class="brand-logo-expanded"><img src="{{ asset('assets/logo_full.jpeg') }}" alt="MDIA" style="height: 35px; width: 35px; object-fit: cover; border-radius: 6px;"> <span style="margin-left: 10px; font-family: Sora, sans-serif; font-weight: 600; color: #FFFFFF; font-size: 16px;">{{ $appName }}</span></span>
        <span class="brand-logo-collapsed">{!! config('adminlte.logo_mini') !!}</span>
    @else
        <img src="{{ asset(config('adminlte.logo_img', 'vendor/adminlte/dist/img/AdminLTELogo.png')) }}"
             alt="{{ config('adminlte.logo_img_alt', 'AdminLTE') }}"
             class="{{ config('adminlte.logo_img_class', 'brand-image img-circle elevation-3') }}"
             style="opacity:.8">
        <span class="brand-text font-weight-light {{ config('adminlte.classes_brand_text') }}">
            {!! config('adminlte.logo', '<b>Admin</b>LTE') !!}
        </span>
    @endif

</a>
