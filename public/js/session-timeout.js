/**
 * Session Timeout Warning Script
 * Shows a warning modal 1 minute before session expires
 * Auto-logout when session expires
 */

(function () {
    // Session timeout in milliseconds (5 minutes = 300000ms)
    const SESSION_TIMEOUT = 5 * 60 * 1000; // 5 minutes
    const WARNING_TIME = 4 * 60 * 1000; // Show warning at 4 minutes (1 minute before expiry)

    let sessionTimer;
    let warningTimer;
    let warningShown = false;

    // Create warning modal
    function createWarningModal() {
        const modalHTML = `
            <div id="session-timeout-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); z-index: 99999; align-items: center; justify-content: center;">
                <div style="background: white; padding: 32px 28px; border-radius: 16px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3); max-width: 440px; width: 90%; text-align: center; animation: slideIn 0.3s ease-out;">
                    <div style="margin-bottom: 20px;">
                        <div style="width: 64px; height: 64px; margin: 0 auto 16px; background: linear-gradient(135deg, #FEF3C7, #F59E0B); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                            <svg style="width: 32px; height: 32px; color: #D97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 600; color: #1F2937;">Session Expiring Soon</h3>
                        <p style="margin: 0; color: #6B7280; font-size: 15px; line-height: 1.5;">Your session will expire in <strong id="countdown-time">60</strong> seconds due to inactivity.</p>
                        <p style="margin: 8px 0 0 0; color: #6B7280; font-size: 14px;">Click "Stay Logged In" to continue your session.</p>
                    </div>
                    <div style="display: flex; gap: 12px; justify-content: center;">
                        <button id="stay-logged-in-btn" style="
                            padding: 12px 24px;
                            background: linear-gradient(135deg, #10B981, #059669);
                            color: white;
                            border: none;
                            border-radius: 8px;
                            cursor: pointer;
                            font-weight: 600;
                            font-size: 14px;
                            transition: all 0.2s ease;
                            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
                        " onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(16, 185, 129, 0.4)'"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(16, 185, 129, 0.3)'">
                            Stay Logged In
                        </button>
                        <button id="logout-now-btn" style="
                            padding: 12px 24px;
                            background: linear-gradient(135deg, #6B7280, #4B5563);
                            color: white;
                            border: none;
                            border-radius: 8px;
                            cursor: pointer;
                            font-weight: 600;
                            font-size: 14px;
                            transition: all 0.2s ease;
                            box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
                        " onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(107, 114, 128, 0.4)'"
                           onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(107, 114, 128, 0.3)'">
                            Logout Now
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Add CSS animations
        const style = document.createElement("style");
        style.textContent = `
            @keyframes slideIn {
                from {
                    opacity: 0;
                    transform: scale(0.8) translateY(-20px);
                }
                to {
                    opacity: 1;
                    transform: scale(1) translateY(0);
                }
            }
        `;
        document.head.appendChild(style);

        document.body.insertAdjacentHTML("beforeend", modalHTML);

        // Add event listeners
        document
            .getElementById("stay-logged-in-btn")
            .addEventListener("click", extendSession);
        document
            .getElementById("logout-now-btn")
            .addEventListener("click", logoutNow);
    }

    // Show warning modal with countdown
    function showWarning() {
        if (warningShown) return;

        warningShown = true;
        const modal = document.getElementById("session-timeout-modal");
        if (!modal) {
            createWarningModal();
        }

        const modal2 = document.getElementById("session-timeout-modal");
        modal2.style.display = "flex";

        // Start countdown (60 seconds)
        let secondsLeft = 60;
        const countdownElement = document.getElementById("countdown-time");

        const countdownInterval = setInterval(() => {
            secondsLeft--;
            if (countdownElement) {
                countdownElement.textContent = secondsLeft;
            }

            if (secondsLeft <= 0) {
                clearInterval(countdownInterval);
                logoutNow();
            }
        }, 1000);

        // Store interval for cleanup
        window.sessionCountdownInterval = countdownInterval;
    }

    // Hide warning modal
    function hideWarning() {
        const modal = document.getElementById("session-timeout-modal");
        if (modal) {
            modal.style.display = "none";
        }
        warningShown = false;

        // Clear countdown
        if (window.sessionCountdownInterval) {
            clearInterval(window.sessionCountdownInterval);
        }
    }

    // Extend session by making a keep-alive request
    function extendSession() {
        fetch("/keep-alive", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,
                "Content-Type": "application/json",
                Accept: "application/json",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    hideWarning();
                    resetTimers();
                }
            })
            .catch((error) => {
                console.error("Keep-alive error:", error);
                logoutNow();
            });
    }

    // Logout immediately
    function logoutNow() {
        // Create a form and submit it
        const form = document.createElement("form");
        form.method = "POST";
        form.action = "/logout";

        const csrfToken = document.querySelector(
            'meta[name="csrf-token"]'
        ).content;
        const csrfInput = document.createElement("input");
        csrfInput.type = "hidden";
        csrfInput.name = "_token";
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);

        document.body.appendChild(form);
        form.submit();
    }

    // Reset all timers
    function resetTimers() {
        clearTimeout(warningTimer);
        clearTimeout(sessionTimer);
        startTimers();
    }

    // Start timers
    function startTimers() {
        // Show warning 1 minute before expiry
        warningTimer = setTimeout(showWarning, WARNING_TIME);

        // Auto logout when session expires
        sessionTimer = setTimeout(logoutNow, SESSION_TIMEOUT);
    }

    // Reset timers on user activity
    function resetOnActivity() {
        if (!warningShown) {
            resetTimers();
        }
    }

    // Initialize on page load
    document.addEventListener("DOMContentLoaded", function () {
        // Only run if user is authenticated (check for CSRF token)
        if (document.querySelector('meta[name="csrf-token"]')) {
            createWarningModal();
            startTimers();

            // Reset timers on user activity
            const events = [
                "mousedown",
                "mousemove",
                "keypress",
                "scroll",
                "touchstart",
                "click",
            ];
            events.forEach((event) => {
                document.addEventListener(event, resetOnActivity, {
                    passive: true,
                });
            });
        }
    });
})();
