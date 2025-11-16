<?php
include 'navbar.php';

 ?>

 <!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Google Map Embed</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
      * {
        box-sizing: border-box;
      }
      .map-container {
        font-family: 'Roboto', sans-serif;
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        overflow: hidden;
        max-width: 800px;
        width: 90%;
        margin: 50px auto;
        transition: transform 0.3s ease;
      }
      .map-container:hover {
        transform: scale(1.02);
      }
      .map-header {
        background-color: #f4ab42;
        color: white;
        padding: 15px 20px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
      }
      .map-header h1 {
        font-weight: 300;
        font-size: 1.8rem;
      }
      .map-header svg {
        margin-right: 10px;
      }
      .map-content {
        padding: 20px;
      }
      iframe {
        width: 100%;
        height: 450px;
        border: none;
        border-radius: 10px;
      }
    </style>
  </head>
  <body>
    <div class="map-container">
      <div class="map-header">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
          <circle cx="12" cy="10" r="3"></circle>
        </svg>
        <h1>Caloocan City Public Library Location</h1>
      </div>
      <div class="map-content">
        <iframe
          src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d811.4813530398179!2d120.98049477413652!3d14.65153661189998!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3397b5d3443044cd%3A0x3c9921c7cfa450c8!2sCaloocan%20City%20Public%20Library!5e0!3m2!1sen!2sph!4v1732576050660!5m2!1sen!2sph"
          allowfullscreen=""
          loading="lazy"
          referrerpolicy="no-referrer-when-downgrade"
        ></iframe>
      </div>
    </div>
  </body>
</html>
