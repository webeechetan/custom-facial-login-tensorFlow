<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Face</title>
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api@1.0.1/dist/face-api.min.js"></script>
</head>
<body>
    <h1>Register Face</h1>
    <video id="video" width="720" height="560" autoplay muted></video>
    <button onclick="registerFace()">Register Face</button>

    <script>
        async function startVideo() {
            const video = document.getElementById('video');
            navigator.mediaDevices.getUserMedia({ video: {} })
                .then(stream => video.srcObject = stream)
                .catch(console.error);

            await faceapi.nets.tinyFaceDetector.loadFromUri('/models');
            await faceapi.nets.faceLandmark68Net.loadFromUri('/models');
            await faceapi.nets.faceRecognitionNet.loadFromUri('/models');
        }

        async function registerFace() {
            const video = document.getElementById('video');
            const detections = await faceapi.detectSingleFace(video, new faceapi.TinyFaceDetectorOptions()).withFaceLandmarks().withFaceDescriptor();
            if (!detections) {
                alert('No face detected, please try again.');
                return;
            }
            const descriptor = detections.descriptor;
            saveDescriptorToServer(descriptor);
        }

        async function saveDescriptorToServer(descriptor) {
            const response = await fetch('/register-face', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ descriptor })
            });
            const data = await response.json();
            if (data.success) {
                alert("Face registered successfully!");
                // Redirect to a login page or dashboard after successful registration
                window.location.href = "/login";
            } else {
                alert("Face registration failed. Please try again.");
            }
        }

        // Start the video stream and load models
        document.addEventListener("DOMContentLoaded", function() {
            startVideo();
        });
    </script>
</body>
</html>
