export function initCentralLogin() {
    // Cargar reCAPTCHA dinámicamente si no existe
    if (!document.querySelector('script[src*="recaptcha/api.js"]')) {
        const script = document.createElement('script');
        script.src = 'https://www.google.com/recaptcha/api.js';
        script.async = true;
        script.defer = true;
        document.body.appendChild(script);
    }

    const gateForm = document.getElementById('gateKeyForm');
    const gateInput = document.getElementById('gate_key');
    const gateBtn = document.getElementById('verifyGateButton');
    let gateCaptchaSolved = false;

    function setGateDisabled(state) {
        if (gateInput) gateInput.disabled = state;
        if (gateBtn) gateBtn.disabled = state;
    }

    if (gateForm) {
        setGateDisabled(true);
        gateForm.addEventListener('submit', function (e) {
            if (!gateCaptchaSolved) {
                e.preventDefault();
            }
        });
    }

    const loginForm = document.getElementById('centralLoginForm');
    const loginEmail = document.getElementById('email');
    const loginPassword = document.getElementById('password');
    const loginRemember = document.getElementById('remember');
    const loginBtn = document.getElementById('centralLoginButton');
    let loginCaptchaSolved = false;

    function setLoginDisabled(state) {
        if (loginEmail) loginEmail.disabled = state;
        if (loginPassword) loginPassword.disabled = state;
        if (loginRemember) loginRemember.disabled = state;
        if (loginBtn) loginBtn.disabled = state;
    }

    if (loginForm) {
        setLoginDisabled(true);
        loginForm.addEventListener('submit', function (e) {
            if (!loginCaptchaSolved) {
                e.preventDefault();
            }
        });
    }

    // Exponer callbacks globalmente para reCAPTCHA
    window.onGateCaptchaSuccess = function () {
        gateCaptchaSolved = true;
        setGateDisabled(false);
    };
    window.onGateCaptchaExpired = function () {
        gateCaptchaSolved = false;
        setGateDisabled(true);
    };

    window.onLoginCaptchaSuccess = function () {
        loginCaptchaSolved = true;
        setLoginDisabled(false);
    };
    window.onLoginCaptchaExpired = function () {
        loginCaptchaSolved = false;
        setLoginDisabled(true);
    };
}
