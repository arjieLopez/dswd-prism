/**
 * Form Validation Script
 * Provides client-side validation for various input types
 */

// Input Validation Function
window.validateInput = function (input) {
    const validationType = input.getAttribute("data-validation");
    const errorSpan = document.getElementById(input.id + "_error");
    const value = input.value;
    let isValid = true;
    let errorMessage = "";

    // Remove existing error styling
    input.classList.remove("border-red-500");
    if (errorSpan) {
        errorSpan.classList.add("hidden");
        errorSpan.textContent = "";
    }

    if (value.length === 0) {
        return true; // Empty is okay, required validation handles this
    }

    switch (validationType) {
        case "text-only":
            // Only letters, spaces, hyphens, periods, and apostrophes (for names)
            const textOnlyRegex = /^[a-zA-Z\s.\-']+$/;
            if (!textOnlyRegex.test(value)) {
                isValid = false;
                errorMessage =
                    "Only letters, spaces, hyphens, periods, and apostrophes are allowed.";
            }
            break;

        case "number-only":
            // Only numbers
            const numberOnlyRegex = /^[0-9]+$/;
            if (!numberOnlyRegex.test(value)) {
                isValid = false;
                errorMessage = "Only numbers are allowed.";
            }
            break;

        case "alphanumeric":
            // Letters and numbers only (with hyphens)
            const alphanumericRegex = /^[a-zA-Z0-9\-]+$/;
            if (!alphanumericRegex.test(value)) {
                isValid = false;
                errorMessage =
                    "Only letters, numbers, and hyphens are allowed.";
            }
            break;

        case "email":
            // Basic email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                isValid = false;
                errorMessage = "Please enter a valid email address.";
            }
            break;

        case "decimal":
            // Numbers with optional decimal point
            const decimalRegex = /^[0-9]+(\.[0-9]+)?$/;
            if (!decimalRegex.test(value)) {
                isValid = false;
                errorMessage =
                    "Please enter a valid number (decimals allowed).";
            }
            break;

        case "phone":
            // Phone numbers (numbers, spaces, hyphens, parentheses, plus sign)
            const phoneRegex = /^[\d\s\-\+\(\)]+$/;
            if (!phoneRegex.test(value)) {
                isValid = false;
                errorMessage = "Please enter a valid phone number.";
            }
            break;

        case "no-special-chars":
            // Letters, numbers, and spaces only (no special characters)
            const noSpecialRegex = /^[a-zA-Z0-9\s]+$/;
            if (!noSpecialRegex.test(value)) {
                isValid = false;
                errorMessage = "Special characters are not allowed.";
            }
            break;
    }

    // Show error if invalid
    if (!isValid) {
        input.classList.add("border-red-500");
        if (errorSpan) {
            errorSpan.textContent = errorMessage;
            errorSpan.classList.remove("hidden");
        }
    }

    return isValid;
};

// Validate all inputs with data-validation attribute on form submit
document.addEventListener("DOMContentLoaded", function () {
    const forms = document.querySelectorAll("form");

    forms.forEach((form) => {
        form.addEventListener("submit", function (e) {
            const validatedInputs = form.querySelectorAll("[data-validation]");
            let isFormValid = true;

            validatedInputs.forEach((input) => {
                if (!validateInput(input)) {
                    isFormValid = false;
                }
            });

            if (!isFormValid) {
                e.preventDefault();
                // Scroll to first error
                const firstError = form.querySelector(".border-red-500");
                if (firstError) {
                    firstError.scrollIntoView({
                        behavior: "smooth",
                        block: "center",
                    });
                    firstError.focus();
                }
            }
        });
    });
});
