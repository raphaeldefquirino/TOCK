document.addEventListener('DOMContentLoaded', function() {
    // Elementos do DOM
    const employeeForm = document.getElementById('employee-form');
    const fullnameInput = document.getElementById('fullname');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('confirm-password');
    const jobTitleInput = document.getElementById('job-title');
    const departmentInput = document.getElementById('department');
    const accessLevelSelect = document.getElementById('access-level');
    const phoneInput = document.getElementById('phone');
    const admissionDateInput = document.getElementById('admission-date');
    const statusSelect = document.getElementById('status');
    const notesTextarea = document.getElementById('notes');
    const generatePasswordCheckbox = document.getElementById('generate-password');
    const saveButton = document.getElementById('save-button');
    const clearButton = document.getElementById('clear-button');
    const togglePasswordBtn = document.querySelector('.toggle-password');
    const successModal = document.getElementById('success-modal');
    const closeModalButtons = document.querySelectorAll('.close-modal');
    const newEmployeeButton = document.getElementById('new-employee-button');
    
    // Definir data de hoje como valor padrão para data de admissão
    const today = new Date().toISOString().split('T')[0];
    admissionDateInput.value = today;
    
    // Mostrar/ocultar senha
    togglePasswordBtn.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        confirmPasswordInput.setAttribute('type', type);
        
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
    
    // Habilitar/desabilitar campos de senha quando gerar senha automática estiver marcado
    generatePasswordCheckbox.addEventListener('change', function() {
        const isChecked = this.checked;
        passwordInput.disabled = isChecked;
        confirmPasswordInput.disabled = isChecked;
        
        if (isChecked) {
            passwordInput.value = '';
            confirmPasswordInput.value = '';
            const passwordGroup = passwordInput.closest('.input-group');
            const confirmPasswordGroup = confirmPasswordInput.closest('.input-group');
            passwordGroup.classList.remove('valid', 'invalid');
            confirmPasswordGroup.classList.remove('valid', 'invalid');
            document.getElementById('password-error').textContent = '';
            document.getElementById('confirm-password-error').textContent = '';
        }
    });
    
    // Validação em tempo real
    fullnameInput.addEventListener('input', validateFullname);
    emailInput.addEventListener('input', validateEmail);
    passwordInput.addEventListener('input', validatePassword);
    confirmPasswordInput.addEventListener('input', validateConfirmPassword);
    jobTitleInput.addEventListener('input', validateJobTitle);
    departmentInput.addEventListener('input', validateDepartment);
    accessLevelSelect.addEventListener('change', validateAccessLevel);
    phoneInput.addEventListener('input', validatePhone);
    admissionDateInput.addEventListener('change', validateAdmissionDate);
    
    // Limpar formulário
    clearButton.addEventListener('click', function() {
        employeeForm.reset();
        admissionDateInput.value = today;
        
        // Remover classes de validação
        const inputGroups = document.querySelectorAll('.input-group');
        inputGroups.forEach(group => {
            group.classList.remove('valid', 'invalid');
        });
        
        // Limpar mensagens de erro
        const errorMessages = document.querySelectorAll('.error-message');
        errorMessages.forEach(message => {
            message.textContent = '';
        });
    });
    
    // Submissão do formulário
    employeeForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Validar todos os campos
        const isFullnameValid = validateFullname();
        const isEmailValid = validateEmail();
        let isPasswordValid = true;
        let isConfirmPasswordValid = true;
        
        if (!generatePasswordCheckbox.checked) {
            isPasswordValid = validatePassword();
            isConfirmPasswordValid = validateConfirmPassword();
        }
        
        const isJobTitleValid = validateJobTitle();
        const isDepartmentValid = validateDepartment();
        const isAccessLevelValid = validateAccessLevel();
        const isPhoneValid = validatePhone();
        const isAdmissionDateValid = validateAdmissionDate();
        
        // Se todos os campos forem válidos, simular cadastro
        if (isFullnameValid && isEmailValid && isPasswordValid && isConfirmPasswordValid && 
            isJobTitleValid && isDepartmentValid && isAccessLevelValid && 
            isPhoneValid && isAdmissionDateValid) {
            
            simulateSave();
        } else {
            // Adicionar efeito de shake nos campos inválidos
            const invalidGroups = document.querySelectorAll('.input-group.invalid');
            invalidGroups.forEach(group => {
                group.classList.add('shake');
                setTimeout(() => {
                    group.classList.remove('shake');
                }, 600);
            });
        }
    });
    
    // Funções de validação
    function validateFullname() {
        const fullnameValue = fullnameInput.value.trim();
        const fullnameGroup = fullnameInput.closest('.input-group');
        const fullnameError = document.getElementById('fullname-error');
        
        if (fullnameValue === '') {
            setError(fullnameGroup, fullnameError, 'Nome completo é obrigatório');
            return false;
        } else if (fullnameValue.length < 3) {
            setError(fullnameGroup, fullnameError, 'Nome deve ter pelo menos 3 caracteres');
            return false;
        } else {
            setSuccess(fullnameGroup, fullnameError);
            return true;
        }
    }
    
    function validateEmail() {
        const emailValue = emailInput.value.trim();
        const emailGroup = emailInput.closest('.input-group');
        const emailError = document.getElementById('email-error');
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (emailValue === '') {
            setError(emailGroup, emailError, 'Email é obrigatório');
            return false;
        } else if (!emailRegex.test(emailValue)) {
            setError(emailGroup, emailError, 'Digite um email válido');
            return false;
        } else {
            setSuccess(emailGroup, emailError);
            return true;
        }
    }
    
    function validatePassword() {
        const passwordValue = passwordInput.value.trim();
        const passwordGroup = passwordInput.closest('.input-group');
        const passwordError = document.getElementById('password-error');
        
        if (passwordValue === '') {
            setError(passwordGroup, passwordError, 'Senha é obrigatória');
            return false;
        } else if (passwordValue.length < 6) {
            setError(passwordGroup, passwordError, 'Senha deve ter pelo menos 6 caracteres');
            return false;
        } else {
            setSuccess(passwordGroup, passwordError);
            return true;
        }
    }
    
    function validateConfirmPassword() {
        const confirmPasswordValue = confirmPasswordInput.value.trim();
        const passwordValue = passwordInput.value.trim();
        const confirmPasswordGroup = confirmPasswordInput.closest('.input-group');
        const confirmPasswordError = document.getElementById('confirm-password-error');
        
        if (confirmPasswordValue === '') {
            setError(confirmPasswordGroup, confirmPasswordError, 'Confirmação de senha é obrigatória');
            return false;
        } else if (confirmPasswordValue !== passwordValue) {
            setError(confirmPasswordGroup, confirmPasswordError, 'As senhas não coincidem');
            return false;
        } else {
            setSuccess(confirmPasswordGroup, confirmPasswordError);
            return true;
        }
    }
    
    function validateJobTitle() {
        const jobTitleValue = jobTitleInput.value.trim();
        const jobTitleGroup = jobTitleInput.closest('.input-group');
        const jobTitleError = document.getElementById('job-title-error');
        
        if (jobTitleValue === '') {
            setError(jobTitleGroup, jobTitleError, 'Cargo/Função é obrigatório');
            return false;
        } else {
            setSuccess(jobTitleGroup, jobTitleError);
            return true;
        }
    }
    
    function validateDepartment() {
        const departmentValue = departmentInput.value.trim();
        const departmentGroup = departmentInput.closest('.input-group');
        const departmentError = document.getElementById('department-error');
        
        if (departmentValue === '') {
            setError(departmentGroup, departmentError, 'Departamento é obrigatório');
            return false;
        } else {
            setSuccess(departmentGroup, departmentError);
            return true;
        }
    }
    
    function validateAccessLevel() {
        const accessLevelValue = accessLevelSelect.value;
        const accessLevelGroup = accessLevelSelect.closest('.input-group');
        const accessLevelError = document.getElementById('access-level-error');
        
        if (accessLevelValue === '') {
            setError(accessLevelGroup, accessLevelError, 'Nível de acesso é obrigatório');
            return false;
        } else {
            setSuccess(accessLevelGroup, accessLevelError);
            return true;
        }
    }
    
    function validatePhone() {
        const phoneValue = phoneInput.value.trim();
        const phoneGroup = phoneInput.closest('.input-group');
        const phoneError = document.getElementById('phone-error');
        const phoneRegex = /^\(\d{2}\)\s\d{4,5}-\d{4}$/;
        
        if (phoneValue !== '' && !phoneRegex.test(phoneValue)) {
            setError(phoneGroup, phoneError, 'Formato: (00) 00000-0000');
            return false;
        } else {
            setSuccess(phoneGroup, phoneError);
            return true;
        }
    }
    
    function validateAdmissionDate() {
        const admissionDateValue = admissionDateInput.value;
        const admissionDateGroup = admissionDateInput.closest('.input-group');
        const admissionDateError = document.getElementById('admission-date-error');
        
        if (admissionDateValue === '') {
            setError(admissionDateGroup, admissionDateError, 'Data de admissão é obrigatória');
            return false;
        } else {
            setSuccess(admissionDateGroup, admissionDateError);
            return true;
        }
    }
    
    // Funções auxiliares
    function setError(inputGroup, errorElement, message) {
        inputGroup.classList.remove('valid');
        inputGroup.classList.add('invalid');
        errorElement.textContent = message;
    }
    
    function setSuccess(inputGroup, errorElement) {
        inputGroup.classList.remove('invalid');
        inputGroup.classList.add('valid');
        errorElement.textContent = '';
    }
    
    // Máscara para telefone
    phoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        if (value.length <= 2) {
            value = value;
        } else if (value.length <= 7) {
            value = `(${value.substring(0, 2)}) ${value.substring(2)}`;
        } else {
            value = `(${value.substring(0, 2)}) ${value.substring(2, 7)}-${value.substring(7, 11)}`;
        }
        
        e.target.value = value;
    });
    
    // Simular salvamento
    function simulateSave() {
        saveButton.classList.add('loading');
        
        // Simular requisição (3 segundos)
        setTimeout(function() {
            saveButton.classList.remove('loading');
            
            // Mostrar modal de sucesso
            successModal.classList.add('active');
        }, 2000);
    }
    
    // Fechar modal
    closeModalButtons.forEach(button => {
        button.addEventListener('click', function() {
            successModal.classList.remove('active');
        });
    });
    
    // Cadastrar outro funcionário
    newEmployeeButton.addEventListener('click', function() {
        successModal.classList.remove('active');
        clearButton.click();
    });
    
    // Fechar modal ao clicar fora
    window.addEventListener('click', function(e) {
        if (e.target === successModal) {
            successModal.classList.remove('active');
        }
    });
    
    // Adicionar efeito de foco nos inputs
    const inputs = document.querySelectorAll('input, select, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.closest('.input-group').classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.closest('.input-group').classList.remove('focused');
        });
    });
});
