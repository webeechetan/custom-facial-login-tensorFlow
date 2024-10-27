<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Face Login</title>
    <!-- Use a browser-compatible version of face-api.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.0.1/dist/face-api.min.js"></script>
</head>
<body>
    <h1>Face Login</h1>
    <video id="video" width="720" height="560" autoplay muted style="filter: brightness(1.5) contrast(1.2);"></video>
    <button onclick="captureFace()">Login with Face</button>

    <script>
        async function startVideo() {
            const video = document.getElementById('video');

            // Load a more robust model for better low-light performance
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
            
            // Try detection with retries to handle low-light and missed detections
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

            if (!detections) {
                alert('No face detected, please try again in better lighting.');
                return;
            }

            const descriptor = detections.descriptor;
            sendDescriptorToServer(descriptor);
        }

        async function sendDescriptorToServer(descriptor) {
            const response = await fetch('/face-login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ descriptor })
            });
            const data = await response.json();
            if (data.success) {
                alert("Login Successful");
                // Redirect to a protected route
                window.location.href = "/dashboard";
            } else {
                alert("Face not recognized");
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            startVideo();
        });
    </script>
</body>
</html>
