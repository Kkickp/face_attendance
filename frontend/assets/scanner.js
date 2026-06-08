const video = document.getElementById('webcam');
const statusIcon = document.getElementById('statusIcon');
const statusText = document.getElementById('statusText');

const PYTHON_API_URL = "http://localhost:8000/api";

let isRecognizing = false;

// Setup Webcam
async function setupWebcam() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
    } catch (err) {
        console.error("Error accessing webcam:", err);
        updateStatus("error", "❌", "Kamera tidak dapat diakses");
    }
}

function updateStatus(type, icon, text) {
    statusIcon.textContent = icon;
    statusText.textContent = text;
    statusText.className = `status-text ${type}`;
}

// Capture frame and send to API
async function captureAndRecognize() {
    if (isRecognizing) return;
    
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    
    // Pastikan video sudah jalan
    if(canvas.width === 0) {
        setTimeout(captureAndRecognize, 500);
        return;
    }

    isRecognizing = true;
    
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0, canvas.width, canvas.height);
    const base64Image = canvas.toDataURL('image/jpeg', 0.8);

    try {
        updateStatus("warning", "⏳", "Memindai wajah...");
        
        const response = await fetch(`${PYTHON_API_URL}/recognize`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ foto_base64: base64Image })
        });
        
        const data = await response.json();
        
        if (data.status === "success") {
            updateStatus("success", "✅", data.message);
            // Jeda lebih lama jika sukses agar user bisa baca
            setTimeout(() => { isRecognizing = false; }, 3000);
        } else {
            updateStatus("error", "⚠️", data.message || "Wajah tidak dikenali");
            setTimeout(() => { isRecognizing = false; }, 1500);
        }
        
    } catch (error) {
        console.error("API Error:", error);
        updateStatus("error", "❌", "Koneksi ke server AI terputus");
        setTimeout(() => { isRecognizing = false; }, 2000);
    }
}

// Start sequence
setupWebcam().then(() => {
    // Jalankan setiap 1.5 detik
    setInterval(captureAndRecognize, 1500);
});
