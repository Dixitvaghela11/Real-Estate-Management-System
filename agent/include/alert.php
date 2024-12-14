    <!-- include/alert.php -->
    <style>
        .custom-alert {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) scale(0.9);
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            padding: 50px;
            height-bottam : 50px;
            max-width: 500px;
            width: 55%;
            text-align: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s cubic-bezier(0.68, -0.55, 0.27, 1.55);
            z-index: 1000; /* Ensure it appears on top */
        }

        .custom-alert.show {
            opacity: 1;
            visibility: visible;
            transform: translate(-50%, -50%) scale(1);
        }

        .custom-alert-icon {
            font-size: 64px;
            margin-bottom: 20px;
            animation: bounce 1s infinite alternate;
        }

        @keyframes bounce {
            0% { transform: translateY(0); }
            100% { transform: translateY(-10px); }
        }

        .custom-alert-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #333;
            animation: fadeInDown 0.5s;
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .custom-alert-message {
            font-size: 16px;
            margin-bottom: 25px;
            color: #666;
            line-height: 1.5;
            animation: fadeIn 0.5s 0.2s both;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .custom-alert-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            animation: slideUp 0.5s 0.4s both;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .custom-alert-button {
            background-color: #3085d6;
            color: white;
            border: none;
            border-radius: 6px;
            padding: 12px 24px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            outline: none;
            position: relative;
            overflow: hidden;
        }

        .custom-alert-button:hover {
            background-color: #2778c4;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .custom-alert-button:active {
            transform: translateY(0);
            box-shadow: none;
        }

        .custom-alert-button::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.5);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .custom-alert-button:focus:not(:active)::after {
            animation: ripple 1s ease-out;
        }

        @keyframes ripple {
            0% { transform: scale(0, 0); opacity: 1; }
            20% { transform: scale(25, 25); opacity: 1; }
            100% { opacity: 0; transform: scale(40, 40); }
        }

        .custom-alert-button.cancel {
            background-color: #dc3545;
        }

        .custom-alert-button.cancel:hover {
            background-color: #c82333;
        }

        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
        .question { color: #6f42c1; }
        .custom { color: #fd7e14; }
    </style>

    <div id="customAlert" class="custom-alert" role="alertdialog" aria-modal="true">
        <div id="alertIcon" class="custom-alert-icon" aria-hidden="true"></div>
        <div id="alertTitle" class="custom-alert-title"></div>
        <div id="alertMessage" class="custom-alert-message"></div>
        <div class="custom-alert-buttons">
            <button id="alertOkButton" class="custom-alert-button">OK</button>
            <button id="alertCancelButton" class="custom-alert-button cancel">Cancel</button>
        </div>
    </div>

    <script>
        function showAlert(type, title, message, redirectUrl = null) {
            const alert = document.getElementById('customAlert');
            const icon = document.getElementById('alertIcon');
            const titleElement = document.getElementById('alertTitle');
            const messageElement = document.getElementById('alertMessage');
            const okButton = document.getElementById('alertOkButton');
            const cancelButton = document.getElementById('alertCancelButton');

            // Set icon based on alert type
            switch (type) {
                case 'success':
                    icon.innerHTML = '✅';
                    icon.className = 'custom-alert-icon success';
                    break;
                case 'error':
                    icon.innerHTML = '❌';
                    icon.className = 'custom-alert-icon error';
                    break;
                case 'info':
                    icon.innerHTML = 'ℹ️';
                    icon.className = 'custom-alert-icon info';
                    break;
                case 'warning':
                    icon.innerHTML = '⚠️';
                    icon.className = 'custom-alert-icon warning';
                    break;
                case 'question':
                    icon.innerHTML = '❓';
                    icon.className = 'custom-alert-icon question';
                    break;
                default:
                    icon.innerHTML = '🔔';
                    icon.className = 'custom-alert-icon custom';
                    break;
            }

            titleElement.textContent = title;
            messageElement.textContent = message;

            alert.classList.add('show');

            okButton.onclick = function() {
                alert.classList.remove('show');
                if (redirectUrl) {
                    window.location.href = redirectUrl;
                }
            };

            cancelButton.onclick = function() {
                alert.classList.remove('show');
            };

            // Close alert when clicking outside
            window.onclick = function(event) {
                if (event.target == alert) {
                    alert.classList.remove('show');
                }
            };

            // Handle keyboard events for accessibility
            alert.onkeydown = function(event) {
                if (event.key === 'Escape') {
                    alert.classList.remove('show');
                }
            };

            // Set focus to the OK button when the alert is shown
            setTimeout(() => okButton.focus(), 100);
        }
    </script>