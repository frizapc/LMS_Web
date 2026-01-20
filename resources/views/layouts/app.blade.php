<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

</head>
<body>
    @include('components.navbar')
    
    <div>
        @include('components.sidebar')
        
        <div class="main-content">
            <div class="main-wrapper">
                <!-- Toast Notification -->
                <div class="toast-container position-fixed bottom-0 end-0 p-3">
                    <div id="liveToast" class="toast rounded-pill" role="alert" aria-live="assertive" aria-atomic="true"> 
                        <div class="toast-body">
                        </div>
                    </div>
                </div>

                @yield('content')
            </div>
            @include('components.footer')
        </div>
    </div>
    

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.js"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- Independent CSS -->
    @stack('styles')
    
    <!-- Independent JS -->
    @stack('scripts')
    <script src="{{ asset('js/helpers/toastHelper.js') }}"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const toast = toastHelper.restore();

            if (toast) {
                toastHelper.showToast(toast.success, toast.message);
            }
        });

        const pageStart = performance.now();

        window.addEventListener("load", () => {
            const loadTime = performance.now() - pageStart;

            document.getElementById("load-time").textContent =
                `Load time: ${(loadTime / 1000).toFixed(2)}s`;
        });
    </script>
</body>
</html>

