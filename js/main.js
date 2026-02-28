(function () {
    const signupForm = document.getElementById('signupForm');
    if (signupForm) {
        const firstName = document.getElementById('firstName');
        const lastName = document.getElementById('lastName');
        const username = document.getElementById('username');
        const email = document.getElementById('email');
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirmPassword');
        const passwordError = document.getElementById('passwordError');

        signupForm.addEventListener('submit', function (event) {
            let valid = true;

            if (!/^[A-Za-z\-\s]{1,100}$/.test(firstName.value.trim())) {
                firstName.classList.add('is-invalid');
                valid = false;
            } else {
                firstName.classList.remove('is-invalid');
            }

            if (!/^[A-Za-z\-\s]{1,100}$/.test(lastName.value.trim())) {
                lastName.classList.add('is-invalid');
                valid = false;
            } else {
                lastName.classList.remove('is-invalid');
            }

            if (!/^[A-Za-z0-9_]{3,30}$/.test(username.value.trim())) {
                username.classList.add('is-invalid');
                valid = false;
            } else {
                username.classList.remove('is-invalid');
            }

            if (!/^\S+@\S+\.\S+$/.test(email.value.trim())) {
                email.classList.add('is-invalid');
                valid = false;
            } else {
                email.classList.remove('is-invalid');
            }

            if (password.value.length < 8) {
                password.classList.add('is-invalid');
                valid = false;
            } else {
                password.classList.remove('is-invalid');
            }

            if (password.value !== confirmPassword.value) {
                confirmPassword.classList.add('is-invalid');
                passwordError.classList.remove('d-none');
                valid = false;
            } else {
                confirmPassword.classList.remove('is-invalid');
                passwordError.classList.add('d-none');
            }

            if (!valid) {
                event.preventDefault();
            }
        });
    }

    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        const email = document.getElementById('email');
        const password = document.getElementById('password');

        loginForm.addEventListener('submit', function (event) {
            let valid = true;

            if (!/^\S+@\S+\.\S+$/.test(email.value.trim())) {
                email.classList.add('is-invalid');
                valid = false;
            } else {
                email.classList.remove('is-invalid');
            }

            if (password.value.trim() === '') {
                password.classList.add('is-invalid');
                valid = false;
            } else {
                password.classList.remove('is-invalid');
            }

            if (!valid) {
                event.preventDefault();
            }
        });
    }
})();