@component('mail::layout')
    {{-- Header --}}
{{--     @slot('header')
        @component('mail::header', ['url' => config('app.url')])
            {{ config('app.name') }}
        @endcomponent
    @endslot --}}

    {{-- Body --}}
    {{ $slot }}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            © {{ date('Y') }} - Equipe de Desenvolvimento de Sistemas - Subsecretaria de Tecnologia da Informação - Prefeitura Municipal de Mesquita - RJ.
        @endcomponent
    @endslot
@endcomponent
