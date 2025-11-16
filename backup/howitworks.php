<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Process Guide</title>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .open-modal-btn {
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }


        .modal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .modal.show {
            display: flex;
            opacity: 1;
        }

        .library-process-container {
            max-width: 800px;
            width: 95%;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            display: flex;
            overflow: hidden;
            max-height: 90vh;
            position: relative;
        }

        .modal-close {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            cursor: pointer;
            color: #64748b;
            padding: 8px;
            border-radius: 50%;
            transition: background 0.2s ease;
            z-index: 10;
        }

        .modal-close:hover {
            background: #f1f5f9;
        }

        .steps-navigation {
            width: 250px;
            background-color: #f8fafc;
            padding: 24px 16px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            border-right: 1px solid #e2e8f0;
            overflow-y: auto;
        }

        .step-button {
            background: transparent;
            border: none;
            color: #64748b;
            padding: 12px 16px;
            text-align: left;
            cursor: pointer;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.2s ease;
        }

        .step-button:hover {
            background: #f1f5f9;
            color: #0f172a;
        }

        .step-button.active {
            background: #0B4208;
            color: white;
        }

        .step-button svg {
            width: 18px;
            height: 18px;
        }

        .step-content {
            flex-grow: 1;
            padding: 55px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            overflow-y: auto;
        }

        .step-icon {
            width: 64px;
            height: 64px;
            background: #FD8418;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
        }

        .step-icon svg {
            color: white;
            width: 28px;
            height: 28px;
        }

        .step-content h2 {
            color: #0f172a;
            margin-bottom: 12px;
            font-size: 1.5rem;
            font-weight: 600;
        }

        .step-content p {
            color: #64748b;
            max-width: 400px;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .library-process-container {
                flex-direction: column;
                width: 90%;
                max-height: 90vh;
                margin: 5vh auto;
            }

            .steps-navigation {
                width: 100%;
                flex-direction: row;
                overflow-x: auto;
                padding: 12px;
                border-right: none;
                border-bottom: 1px solid #e2e8f0;
                scrollbar-width: thin;
                scrollbar-color: #888 #f1f1f1;
            }

            .steps-navigation::-webkit-scrollbar {
                height: 8px;
            }

            .steps-navigation::-webkit-scrollbar-thumb {
                background-color: #888;
                border-radius: 4px;
            }

            .step-button {
                flex-shrink: 0;
                padding: 8px 12px;
                font-size: 0.9rem;
                min-width: 120px;
                justify-content: center;
                text-align: center;
            }

            .step-content {
                padding: 24px 16px;
            }

            .modal-close {
                top: 10px;
                right: 10px;
            }
        }

        /* Improve touch target size for mobile */
        @media (max-width: 480px) {
            .step-button {
                padding: 12px 16px;
                min-width: 100px;
            }

            .modal-close {
                padding: 12px;
            }
        }
    </style>
</head>
<body>
   

    <div class="modal">
        <div class="library-process-container">
            <button class="modal-close">
            </button>
            <div class="steps-navigation">
                <button class="step-button active" data-step="0">
                    <i data-feather="book"></i>
                    Book Request
                </button>
                <button class="step-button" data-step="1">
                    <i data-feather="check-circle"></i>
                    Approval
                </button>
                <button class="step-button" data-step="2">
                    <i data-feather="book-open"></i>
                    Physical Pickup
                </button>
                <button class="step-button" data-step="3">
                    <i data-feather="tablet"></i>
                    E-Book Access
                </button>
            </div>
            <div class="step-content">
                <div class="step-icon">
                    <i data-feather="book"></i>
                </div>
                <h2>Book Request</h2>
                <p>Submit your book request through our library system. Our librarian will process your request promptly.</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            feather.replace();

            const steps = [
                {
                    icon: 'book',
                    title: 'Book Request',
                    description: 'Submit your book request through our library system. Our librarian will process your request promptly.'
                },
                {
                    icon: 'check-circle',
                    title: 'Approval',
                    description: 'Requests are processed on a first-come basis. Our librarian will verify and approve your request quickly.'
                },
                {
                    icon: 'book-open',
                    title: 'Physical Pickup',
                    description: 'Visit our library to collect your approved physical book from our librarian.'
                },
                {
                    icon: 'tablet',
                    title: 'E-Book Access',
                    description: 'Access e-books instantly through your library account on any device, anytime.'
                }
            ];

            const modal = document.querySelector('.modal');
            const openModalBtn = document.querySelector('.open-modal-btn');
            const closeModalBtn = document.querySelector('.modal-close');
            const stepButtons = document.querySelectorAll('.step-button');
            const stepContent = document.querySelector('.step-content');

            openModalBtn.addEventListener('click', () => {
                modal.classList.add('show');
            });

            closeModalBtn.addEventListener('click', () => {
                modal.classList.remove('show');
            });

            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('show');
                }
            });

            function updateStep(index) {
                const step = steps[index];
                stepContent.innerHTML = `
                    <div class="step-icon">
                        <i data-feather="${step.icon}"></i>
                    </div>
                    <h2>${step.title}</h2>
                    <p>${step.description}</p>
                `;
                feather.replace();

                stepButtons.forEach((btn, i) => {
                    btn.classList.toggle('active', i === index);
                });
            }

            stepButtons.forEach((btn, index) => {
                btn.addEventListener('click', () => updateStep(index));
            });
        });
    </script>
</body>
</html>