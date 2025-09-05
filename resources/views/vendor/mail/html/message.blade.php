@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        <table class="header" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <td align="center">
                    <a href="{{ config('app.url') }}" style="font-size: 20px; font-weight: bold; color: #4f46e5; text-decoration:none;">
                        {{ env('APP_NAME') }}
                    </a>
                    <div style="font-size: 14px; color: #6b7280; margin-top: 4px;">
                        Your Secure Link to Global Payments
                    </div>
                </td>
            </tr>
        </table>
    @endslot

    {{-- Main Content --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            {{ $subcopy }}
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        <table class="footer" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation">
            <tr>
                <td align="center" style="font-size: 12px; color: #9ca3af; padding: 20px;">
                    &copy; {{ date('Y') }} {{ env('APP_NAME') }}. All rights reserved.
                </td>
            </tr>
        </table>
    @endslot
@endcomponent
