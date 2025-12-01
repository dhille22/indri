document.addEventListener('DOMContentLoaded',()=>{
  // Password visibility toggle
  const pwToggle = document.querySelector('.pw-toggle');
  const pwInput = document.querySelector('#password');
  if (pwToggle && pwInput) {
    pwToggle.addEventListener('click', () => {
      const isPwd = pwInput.getAttribute('type') === 'password';
      pwInput.setAttribute('type', isPwd ? 'text' : 'password');
      pwToggle.setAttribute('aria-pressed', String(isPwd));
      pwInput.focus();
    });
  }

  // Submit loading state for auth form
  const authForm = document.querySelector('.auth-container form');
  if (authForm) {
    authForm.addEventListener('submit', () => {
      const btn = authForm.querySelector('button[type="submit"]');
      if (btn) {
        btn.classList.add('loading');
        btn.setAttribute('disabled', 'true');
      }
    });
  }
});
