<x-layout.user.app>

    <div class="container">
        <div class="row justify-content-center text-center">
            <div class="col-md-8">
                <div class="card dark:text-white border">
                    <div class="card-header dark:text-white border">{{ __('Internal Server Error') }}</div>

                    <div class="card-body dark:text-white border">
                        <p>{{ __('We are experiencing some technical difficulties. Please try again later.') }}</p>
                        <p><a href="{{ route('home') }}">{{ __('Back to Home') }}</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layout.user.app>
