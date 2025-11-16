<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Location Modal</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        .location-modal-trigger {
            color: white;
            background-color: #3498db;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .location-modal-trigger:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            background-color: #2980b9;
        }

        .location-modal3 {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 15px;
        }

        .location-modal3.open {
            display: flex;
        }

        .location-modal-content {
            background: white;
            border-radius: 12px;
            width: 100%;
            max-width: 1000px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 24px rgba(0,0,0,0.2);
            position: relative;
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            padding: 30px;
        }

        .location-close-modal {
            position: absolute;
            top: 15px;
            right: 15px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            transition: color 0.3s ease;
            z-index: 10;
        }

        .location-close-modal:hover {
            color: #1a1a1a;
        }

        .location-map-container iframe {
            width: 100%;
            height: 400px;
            border: none;
            border-radius: 8px;
            object-fit: cover;
        }

        .location-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: #1a1a1a;
        }

        .location-details {
            margin-bottom: 30px;
        }

        .location-details p {
            margin: 8px 0;
            line-height: 1.6;
            color: #555;
        }

        .location-hours-section, .location-contact-section {
            margin-bottom: 30px;
            padding: 15px 0;
            border-top: 1px solid #eee;
        }

        .location-section-title {
            font-size: 1.2rem;
            font-weight: 500;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #1a1a1a;
        }

        .location-highlight {
            color: #1a1a1a;
            font-weight: 500;
        }

        /* Enhanced Mobile Responsiveness */
        @media (max-width: 768px) {
            .location-modal-content {
                grid-template-columns: 1fr;
                max-height: none;
                height: auto;
                width: 100%;
                border-radius: 12px;
                padding: 20px;
                gap: 20px;
                overflow-y: auto;
                max-height: 95vh;
            }

            .location-map-container iframe {
                height: 250px;
            }

            .location-title {
                font-size: 1.3rem;
            }

            .location-details p {
                font-size: 0.95rem;
            }

            .location-section-title {
                font-size: 1.1rem;
                gap: 8px;
            }

            .location-close-modal {
                top: 10px;
                right: 10px;
                font-size: 20px;
            }
        }

        /* Touch-friendly adjustments */
        @media (max-width: 480px) {
            .location-modal-content {
                padding: 15px;
            }

            .location-map-container iframe {
                height: 200px;
            }

            .location-details p {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>

    <div class="location-modal3" id="libraryModal">
        <div class="location-modal-content">
            <button class="location-close-modal" id="closeModal">&times;</button>

            <div class="location-map-container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d811.4813530398179!2d120.98049477413652!3d14.65153661189998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b5d3443044cd%3A0x3c9921c7cfa450c8!2sCaloocan%20City%20Public%20Library!5e0!3m2!1sen!2sph!4v1732576050660!5m2!1sen!2sph"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>

            <div class="location-info-container">
                <h1 class="location-title">Location</h1>
                <div class="location-details">
                    <p class="location-highlight">CALOOCAN CITY PUBLIC LIBRARY</p>
                    <p>Macario Asistio Sr</p>
                    <p>10th Avenue, Grace Park</p>
                    <p>Caloocan City, Philippines</p>
                </div>

                <div class="location-hours-section">
                    <h2 class="location-section-title">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <path d="M12 6v6l4 2"/>
                        </svg>
                        Library Hours
                    </h2>
                    <p>Mon - Fri: 8:00 AM - 5:00 PM</p>
                    <p style="color: #666;">(Closed on Weekends and Holidays)</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('libraryModal');
            const openModalBtn = document.getElementById('openLibraryModal');
            const closeModalBtn = document.getElementById('closeModal');

            // Open modal
            openModalBtn.addEventListener('click', () => {
                modal.classList.add('open');
            });

            // Close modal
            closeModalBtn.addEventListener('click', () => {
                modal.classList.remove('open');
            });

            // Close modal when clicking outside the modal content
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    modal.classList.remove('open');
                }
            });

            // Close modal with Escape key
            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && modal.classList.contains('open')) {
                    modal.classList.remove('open');
                }
            });
        });
    </script>
</body>
</html>