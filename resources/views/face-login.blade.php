<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Face Login</title>
    <!-- Use a browser-compatible version of face-api.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.0.1/dist/face-api.min.js"></script>
    <!-- CSS for styling -->
    <style>
        /* Center everything on the page */
        body, html {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
        }

        .container {
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            background-color: #ffffff;
            max-width: 400px;
            position: relative;
        }

        h1 {
            color: #4a4e69;
            margin-bottom: 20px;
            font-size: 1.5em;
        }

        video {
            width: 100%;
            max-width: 350px;
            height: auto;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2);
        }

        button {
            background-color: #4a4e69;
            color: #fff;
            padding: 10px 20px;
            font-size: 1em;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #333753;
        }

        button:active {
            transform: scale(0.98);
        }

        .message {
            color: #d9534f;
            font-size: 0.9em;
            margin-top: 15px;
        }

        /* Loader styles */
        .loader {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4a4e69;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 0.8s linear infinite;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none; /* Initially hidden */
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Face Login</h1>
        <video id="video" autoplay muted></video>
        <button onclick="captureFace()">Login with Face</button>
        <div id="message" class="message"></div>
        <div id="loader" class="loader"></div> <!-- Loader -->
    </div>

    <script>
        async function startVideo() {
            const video = document.getElementById('video');

            // Load models from the 'models' directory
            await faceapi.nets.ssdMobilenetv1.loadFromUri('/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');

            // Start video after models are loaded
            navigator.mediaDevices.getUserMedia({ video: {} })
                .then(stream => video.srcObject = stream)
                .catch(console.error);
        }

        async function captureFace() {
            const video = document.getElementById('video');
            const loader = document.getElementById('loader');
            const message = document.getElementById('message');
            message.textContent = ''; // Clear previous messages
            loader.style.display = 'block'; // Show loader

            let attempts = 0;
            let detections;

            while (attempts < 3 && !detections) {
                detections = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();
                if (!detections) {
                    attempts++;
                    console.log(`Attempt ${attempts}: No face detected`);
                    await new Promise(resolve => setTimeout(resolve, 1000)); // wait 1 second before retrying
                }
            }

            loader.style.display = 'none'; // Hide loader

            if (!detections) {
                message.textContent = 'No face detected, please try again in better lighting.';
                return;
            }

            const descriptor = detections.descriptor;
            sendDescriptorToServer(descriptor);
        }

        async function sendDescriptorToServer(descriptor) {
            const loader = document.getElementById('loader');
            const message = document.getElementById('message');
            loader.style.display = 'block'; // Show loader while sending request

            const response = await fetch('/face-login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ descriptor })
            });
            const data = await response.json();
            loader.style.display = 'none'; // Hide loader after request completes
            if (data.success) {
                alert("Login Successful");
                // Redirect to a protected route
                window.location.href = "/dashboard";
            } else {
                message.textContent = "Face not recognized. Try again.";
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            startVideo();
        });
    </script>
</body>
</html>
