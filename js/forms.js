/**
 * FindMyDojo - Forms JavaScript File
 * Handles form validation, submission, and interactions
 */

document.addEventListener('DOMContentLoaded', function() {
  
  // ===== FORM VALIDATION =====
  const forms = document.querySelectorAll('form[data-validate]');
  
  forms.forEach(form => {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Clear previous errors
      clearFormErrors(form);
      
      // Validate form
      if (validateForm(form)) {
        submitForm(form);
      }
    });
    
    // Real-time validation
    const inputs = form.querySelectorAll('input, textarea, select');
    inputs.forEach(input => {
      input.addEventListener('blur', function() {
        validateField(this);
      });
      
      input.addEventListener('input', function() {
        // Remove error on input
        const errorElement = this.parentElement.querySelector('.error-message');
        if (errorElement) {
          errorElement.remove();
          this.classList.remove('error');
        }
      });
    });
  });
  
  // Validate individual field
  function validateField(field) {
    const value = field.value.trim();
    const type = field.type;
    const required = field.hasAttribute('required');
    const minLength = field.getAttribute('minlength');
    const maxLength = field.getAttribute('maxlength');
    const pattern = field.getAttribute('pattern');
    
    let errorMessage = '';
    
    // Required check
    if (required && !value) {
      errorMessage = 'This field is required';
    }
    // Email validation
    else if (type === 'email' && value) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!emailRegex.test(value)) {
        errorMessage = 'Please enter a valid email address';
      }
    }
    // Min length check
    else if (minLength && value.length < parseInt(minLength)) {
      errorMessage = `Minimum ${minLength} characters required`;
    }
    // Max length check
    else if (maxLength && value.length > parseInt(maxLength)) {
      errorMessage = `Maximum ${maxLength} characters allowed`;
    }
    // Pattern check
    else if (pattern && value) {
      const regex = new RegExp(pattern);
      if (!regex.test(value)) {
        errorMessage = 'Please match the requested format';
      }
    }
    // Password strength (if field has data-password attribute)
    else if (field.hasAttribute('data-password') && value) {
      if (value.length < 8) {
        errorMessage = 'Password must be at least 8 characters';
      } else if (!/[A-Z]/.test(value)) {
        errorMessage = 'Password must contain at least one uppercase letter';
      } else if (!/[a-z]/.test(value)) {
        errorMessage = 'Password must contain at least one lowercase letter';
      } else if (!/[0-9]/.test(value)) {
        errorMessage = 'Password must contain at least one number';
      }
    }
    // Password confirmation
    else if (field.hasAttribute('data-confirm')) {
      const originalField = document.querySelector(field.getAttribute('data-confirm'));
      if (originalField && value !== originalField.value) {
        errorMessage = 'Passwords do not match';
      }
    }
    
    if (errorMessage) {
      showFieldError(field, errorMessage);
      return false;
    }
    
    return true;
  }
  
  // Validate entire form
  function validateForm(form) {
    const fields = form.querySelectorAll('input, textarea, select');
    let isValid = true;
    
    fields.forEach(field => {
      if (!validateField(field)) {
        isValid = false;
      }
    });
    
    return isValid;
  }
  
  // Show field error
  function showFieldError(field, message) {
    field.classList.add('error');
    
    // Check if error message already exists
    const existingError = field.parentElement.querySelector('.error-message');
    if (existingError) {
      existingError.textContent = message;
      return;
    }
    
    const errorElement = document.createElement('div');
    errorElement.className = 'error-message';
    errorElement.textContent = message;
    errorElement.style.cssText = `
      color: #ef4444;
      font-size: 0.875rem;
      margin-top: 0.25rem;
      animation: fadeInUp 0.3s ease-out;
    `;
    
    field.parentElement.appendChild(errorElement);
  }
  
  // Clear form errors
  function clearFormErrors(form) {
    const errorMessages = form.querySelectorAll('.error-message');
    errorMessages.forEach(msg => msg.remove());
    
    const errorFields = form.querySelectorAll('.error');
    errorFields.forEach(field => field.classList.remove('error'));
  }
  
  // Submit form
  async function submitForm(form) {
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    
    // Show loading state
    submitButton.disabled = true;
    submitButton.innerHTML = '<span class="spinner"></span> Submitting...';
    
    // For auth forms, let the browser handle the submission
    if (form.closest('#loginForm, #registerForm')) {
      // Don't prevent default, let PHP handle it
      form.submit();
      return;
    }
    
    // Get form data
    const formData = new FormData(form);
    const action = form.getAttribute('action') || form.getAttribute('data-action');
    
    try {
      // Simulate API call for other forms
      await new Promise(resolve => setTimeout(resolve, 1500));
      
      // Success
      showToast('Form submitted successfully!', 'success');
      form.reset();
      
      // Redirect if specified
      const redirect = form.getAttribute('data-redirect');
      if (redirect) {
        setTimeout(() => {
          window.location.href = redirect;
        }, 1000);
      }
      
    } catch (error) {
      // Error
      showToast('An error occurred. Please try again.', 'error');
      console.error('Form submission error:', error);
    } finally {
      // Reset button
      submitButton.disabled = false;
      submitButton.innerHTML = originalText;
    }
  }
  
  // ===== PASSWORD VISIBILITY TOGGLE =====
  const passwordToggles = document.querySelectorAll('[data-password-toggle]');
  
  passwordToggles.forEach(toggle => {
    toggle.addEventListener('click', function() {
      const targetId = this.getAttribute('data-password-toggle');
      const passwordField = document.getElementById(targetId);
      const icon = this.querySelector('i');
      
      if (passwordField) {
        if (passwordField.type === 'password') {
          passwordField.type = 'text';
          icon.classList.remove('fa-eye');
          icon.classList.add('fa-eye-slash');
        } else {
          passwordField.type = 'password';
          icon.classList.remove('fa-eye-slash');
          icon.classList.add('fa-eye');
        }
      }
    });
  });
  
  // ===== SEARCH FUNCTIONALITY =====
  const searchForms = document.querySelectorAll('[data-search-form]');
  
  searchForms.forEach(form => {
    const searchInput = form.querySelector('input[type="search"], input[type="text"]');
    const clearButton = form.querySelector('[data-search-clear]');
    
    if (searchInput) {
      // Show/hide clear button
      searchInput.addEventListener('input', function() {
        if (clearButton) {
          clearButton.style.display = this.value ? 'block' : 'none';
        }
      });
      
      // Clear search
      if (clearButton) {
        clearButton.addEventListener('click', function() {
          searchInput.value = '';
          searchInput.focus();
          this.style.display = 'none';
          
          // Trigger search/filter
          searchInput.dispatchEvent(new Event('input'));
        });
      }
    }
  });
  
  // ===== FILTER FUNCTIONALITY =====
  const filterSelects = document.querySelectorAll('[data-filter]');
  
  filterSelects.forEach(select => {
    select.addEventListener('change', function() {
      const filterType = this.getAttribute('data-filter');
      const filterValue = this.value.toLowerCase();
      const targetContainer = document.querySelector(this.getAttribute('data-filter-target'));
      
      if (targetContainer) {
        const items = targetContainer.querySelectorAll('[data-filter-item]');
        
        items.forEach(item => {
          const itemValue = item.getAttribute(`data-${filterType}`);
          
          if (!filterValue || filterValue === 'all' || itemValue === filterValue) {
            item.style.display = '';
            item.classList.add('fade-in-up');
          } else {
            item.style.display = 'none';
          }
        });
        
        // Show "no results" message if needed
        const visibleItems = Array.from(items).filter(item => item.style.display !== 'none');
        updateResultsCount(targetContainer, visibleItems.length);
      }
    });
  });
  
  // Update results count
  function updateResultsCount(container, count) {
    let countElement = container.querySelector('.results-count');
    
    if (!countElement) {
      countElement = document.createElement('p');
      countElement.className = 'results-count';
      container.insertBefore(countElement, container.firstChild);
    }
    
    if (count === 0) {
      countElement.innerHTML = '<strong>No results found</strong>. Try adjusting your filters.';
      countElement.style.color = 'var(--color-muted-foreground)';
    } else {
      countElement.innerHTML = `Showing <strong>${count}</strong> ${count === 1 ? 'result' : 'results'}`;
      countElement.style.color = 'var(--color-foreground)';
    }
  }
  
  // ===== FILE UPLOAD PREVIEW =====
  const fileInputs = document.querySelectorAll('input[type="file"][data-preview]');
  
  fileInputs.forEach(input => {
    input.addEventListener('change', function() {
      const previewContainer = document.getElementById(this.getAttribute('data-preview'));
      
      if (previewContainer && this.files && this.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
          previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 200px; border-radius: var(--radius-md);">`;
        };
        
        reader.readAsDataURL(this.files[0]);
      }
    });
  });
  
  console.log('Form handling initialized! 📝');
});

// Add CSS for form error states
const formStyles = document.createElement('style');
formStyles.textContent = `
  .form-input.error,
  .form-textarea.error,
  .form-select.error {
    border-color: #ef4444;
  }
  
  .form-input.error:focus,
  .form-textarea.error:focus,
  .form-select.error:focus {
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
  }
  
  .error-message {
    animation: fadeInUp 0.3s ease-out;
  }
`;
document.head.appendChild(formStyles);