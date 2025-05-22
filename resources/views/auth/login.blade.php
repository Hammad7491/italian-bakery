<!-- meta tag e altri link -->
<!DOCTYPE html>
<html lang="it" data-theme="light">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pasticcere Pro | Accesso</title>
  <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.ico') }}" sizes="16x16">
  <!-- font icone Remix -->
  <link rel="stylesheet" href="{{ asset('assets/css/remixicon.css') }}">
  <!-- Bootstrap css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/bootstrap.min.css') }}">
  <!-- Apex Chart css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/apexcharts.css') }}">
  <!-- Data Table css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/dataTables.min.css') }}">
  <!-- Editor di testo css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/editor-katex.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.atom-one-dark.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/lib/editor.quill.snow.css') }}">
  <!-- Date picker css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/flatpickr.min.css') }}">
  <!-- Calendario css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/full-calendar.css') }}">
  <!-- Vector Map css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/jquery-jvectormap-2.0.5.css') }}">
  <!-- Popup css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/magnific-popup.css') }}">
  <!-- Slick Slider css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/slick.css') }}">
  <!-- Prism css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/prism.css') }}">
  <!-- Upload file css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/file-upload.css') }}">
  <!-- Audio player css -->
  <link rel="stylesheet" href="{{ asset('assets/css/lib/audioplayer.css') }}">
  <!-- css principale -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>

<section class="auth bg-base d-flex flex-wrap">  
    <div class="auth-left d-lg-block d-none">
        <div class="d-flex align-items-center flex-column h-100 justify-content-center">
            <img src="{{ asset('assets/images/asset/login.jpg') }}" alt="">
        </div>
    </div>
    <div class="auth-right py-32 px-24 d-flex flex-column justify-content-center">
        <div class="max-w-464-px mx-auto w-100">
            <div>
                <a href="{{ url('/') }}" class="mb-40 max-w-290-px">
                    <img src="{{ asset('assets/images/asset/logo.jpg') }}" alt="">
                </a>
                <h4 class="mb-12">Accedi a Pasticcere Pro</h4>
                <p class="mb-32 text-secondary-light text-lg">Bentornato! Inserisci i tuoi dati</p>
            </div>
            {{-- resources/views/auth/login.blade.php --}}
            <form action="{{ route('login.submit') }}" method="POST">
                @csrf
                
                <div class="icon-field mb-16">
                    <span class="icon top-50 translate-middle-y">
                        <iconify-icon icon="mage:email"></iconify-icon>
                    </span>
                    <input
                        type="email"
                        class="form-control h-56-px bg-neutral-50 radius-12 @error('email') is-invalid @enderror"
                        placeholder="Email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                    >
                    @error('email')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="position-relative mb-20">
                    <div class="icon-field">
                        <span class="icon top-50 translate-middle-y">
                            <iconify-icon icon="solar:lock-password-outline"></iconify-icon>
                        </span>
                        <input
                            type="password"
                            class="form-control h-56-px bg-neutral-50 radius-12 @error('password') is-invalid @enderror"
                            id="your-password"
                            placeholder="Password"
                            name="password"
                            required
                        >
                    </div>
                    <span
                        class="toggle-password ri-eye-line cursor-pointer position-absolute end-0 top-50 translate-middle-y me-16 text-secondary-light"
                        data-toggle="#your-password"
                    ></span>
                    @error('password')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button
                  type="submit"
                  class="btn text-sm btn-sm px-12 py-16 w-100 radius-12 mt-32"
                  style="
                    background-color: #e2ae76;
                    color: #041930;
                    border: 2px solid #e2ae76;
                  "
                >
                  Accedi
                </button>
            </form>
        </div>
    </div>
</section>

<!-- jQuery -->
<script src="{{ asset('assets/js/lib/jquery-3.7.1.min.js') }}"></script>
<!-- Bootstrap js -->
<script src="{{ asset('assets/js/lib/bootstrap.bundle.min.js') }}"></script>
<!-- Apex Chart js -->
<script src="{{ asset('assets/js/lib/apexcharts.min.js') }}"></script>
<!-- Data Table js -->
<script src="{{ asset('assets/js/lib/dataTables.min.js') }}"></script>
<!-- Iconify js -->
<script src="{{ asset('assets/js/lib/iconify-icon.min.js') }}"></script>
<!-- jQuery UI js -->
<script src="{{ asset('assets/js/lib/jquery-ui.min.js') }}"></script>
<!-- Vector Map js -->
<script src="{{ asset('assets/js/lib/jquery-jvectormap-2.0.5.min.js') }}"></script>
<script src="{{ asset('assets/js/lib/jquery-jvectormap-world-mill-en.js') }}"></script>
<!-- Popup js -->
<script src="{{ asset('assets/js/lib/magnifc-popup.min.js') }}"></script>
<!-- Slick Slider js -->
<script src="{{ asset('assets/js/lib/slick.min.js') }}"></script>
<!-- Prism js -->
<script src="{{ asset('assets/js/lib/prism.js') }}"></script>
<!-- File upload js -->
<script src="{{ asset('assets/js/lib/file-upload.js') }}"></script>
<!-- Audio player js -->
<script src="{{ asset('assets/js/lib/audioplayer.js') }}"></script>
<!-- main js -->
<script src="{{ asset('assets/js/app.js') }}"></script>

<script>
  // ================== Toggle Password ===================
  function initializePasswordToggle(toggleSelector) {
    $(toggleSelector).on('click', function() {
        $(this).toggleClass("ri-eye-off-line");
        var input = $($(this).attr("data-toggle"));
        if (input.attr("type") === "password") {
            input.attr("type", "text");
        } else {
            input.attr("type", "password");
        }
    });
  }
  // Inizializza il toggle
  initializePasswordToggle('.toggle-password');
  // =======================================================

  // Quick login buttons
  $(document).on('click', '.quick-login-btn', function() {
    const email = $(this).data('email');
    const pass  = $(this).data('password');

    // riempi i campi
    $('input[name="email"]').val(email);
    $('input[name="password"]').val(pass);

    // invia il form
    $(this).closest('form').submit();
  });
</script>
</body>
</html>
