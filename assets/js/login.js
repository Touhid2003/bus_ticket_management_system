const container = document.getElementById('container');
const registerBtn = document.getElementById('register');
const loginBtn = document.getElementById('login');
const passwordToggle = document.querySelector('.password-toggle');
const loginPassword = document.getElementById('login-password');

registerBtn.addEventListener('click', () => {
    container.classList.add("active");
});

loginBtn.addEventListener('click', () => {
    container.classList.remove("active");
});

passwordToggle.addEventListener('click', () => {
    const showPassword = loginPassword.type === 'password';
    loginPassword.type = showPassword ? 'text' : 'password';
    passwordToggle.setAttribute('aria-pressed', String(showPassword));
    passwordToggle.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
    passwordToggle.querySelector('i').className = showPassword
        ? 'fa-solid fa-eye-slash'
        : 'fa-solid fa-eye';
});