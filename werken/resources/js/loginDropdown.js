document.addEventListener('DOMContentLoaded', function () {
    const userIcon = document.getElementById('user-icon');
    const loginDropdown = document.getElementById('login-dropdown');
    const loginForm = document.getElementById('login-form');
    const loginError = document.getElementById('login-error');

    // Mostrar/ocultar el formulario de inicio de sesión al hacer clic en el ícono de usuario
    userIcon.addEventListener('click', function () {
        loginDropdown.classList.toggle('hidden');
    });

    // Ocultar el formulario al hacer clic fuera de él
    document.addEventListener('click', function (event) {
        if (!loginDropdown.contains(event.target) && !userIcon.contains(event.target)) {
            loginDropdown.classList.add('hidden');
        }
    });

    // Manejar el envío del formulario de inicio de sesión con AJAX
    loginForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Evitar la recarga de la página

        const formData = new FormData(loginForm);

        fetch(loginForm.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = "/dashboard"; // Redirige al dashboard si el login es exitoso
            } else {
                loginError.classList.remove('hidden'); // Muestra el mensaje de error
            }
        })
        .catch(error => {
            console.error('Error:', error);
            loginError.classList.remove('hidden');
        });
    });
});
