document.addEventListener('DOMContentLoaded', function() {
    // Elementos do DOM
    const loginForm = document.getElementById('login-form');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const emailError = document.getElementById('email-error');
    const passwordError = document.getElementById('password-error');
    const togglePasswordBtn = document.querySelector('.toggle-password');
    const loginButton = document.getElementById('login-button');
    
    // Validação de email em tempo real
    emailInput.addEventListener('input', function() {
        validateEmail();
    });
    
    // Validação de senha em tempo real
    passwordInput.addEventListener('input', function() {
        validatePassword();
    });
    
    // Mostrar/ocultar senha
    togglePasswordBtn.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Alternar ícone
        const icon = this.querySelector('i');
        if (type === 'password') {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    });
    
    // Validação do formulário antes do envio
    loginForm.addEventListener('submit', function(e) {
        // Validar todos os campos
        const isEmailValid = validateEmail();
        const isPasswordValid = validatePassword();
        
        // Se algum campo for inválido, impedir o envio do formulário
        if (!isEmailValid || !isPasswordValid) {
            e.preventDefault();
            
            // Adicionar efeito de shake nos campos inválidos
            if (!isEmailValid) {
                const emailGroup = emailInput.closest('.input-group');
                emailGroup.classList.add('shake');
                setTimeout(() => {
                    emailGroup.classList.remove('shake');
                }, 600);
            }
            
            if (!isPasswordValid) {
                const passwordGroup = passwordInput.closest('.input-group');
                passwordGroup.classList.add('shake');
                setTimeout(() => {
                    passwordGroup.classList.remove('shake');
                }, 600);
            }
        } else {
            // Mostrar estado de carregamento
            loginButton.classList.add('loading');
            // Permitir que o formulário seja enviado para o backend
        }
    });
    
    // Função para validar email
    function validateEmail() {
        const emailValue = emailInput.value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const emailGroup = emailInput.closest('.input-group');
        
        if (emailValue === '') {
            setError(emailInput, emailError, 'O email é obrigatório', emailGroup);
            return false;
        } else if (!emailRegex.test(emailValue)) {
            setError(emailInput, emailError, 'Digite um email válido', emailGroup);
            return false;
        } else {
            setSuccess(emailInput, emailError, emailGroup);
            return true;
        }
    }
    
    // Função para validar senha
    function validatePassword() {
        const passwordValue = passwordInput.value.trim();
        const passwordGroup = passwordInput.closest('.input-group');
        
        if (passwordValue === '') {
            setError(passwordInput, passwordError, 'A senha é obrigatória', passwordGroup);
            return false;
        } else if (passwordValue.length < 6) {
            setError(passwordInput, passwordError, 'A senha deve ter pelo menos 6 caracteres', passwordGroup);
            return false;
        } else {
            setSuccess(passwordInput, passwordError, passwordGroup);
            return true;
        }
    }
    
    // Função para definir erro
    function setError(input, errorElement, message, inputGroup) {
        inputGroup.classList.remove('valid');
        inputGroup.classList.add('invalid');
        errorElement.textContent = message;
    }
    
    // Função para definir sucesso
    function setSuccess(input, errorElement, inputGroup) {
        inputGroup.classList.remove('invalid');
        inputGroup.classList.add('valid');
        errorElement.textContent = '';
    }
    
    // Adicionar efeitos de foco nos inputs
    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.closest('.input-group').classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.closest('.input-group').classList.remove('focused');
        });
    });
    
    // Adicionar efeito de ripple nos botões
    const buttons = document.querySelectorAll('button');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            this.appendChild(ripple);
            
            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });
});
