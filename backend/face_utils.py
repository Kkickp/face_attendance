import numpy as np
import cv2
import base64
from deepface import DeepFace

def decode_base64_image(base64_string):
    """Mengubah format gambar base64 dari PHP ke object matrix OpenCV (numpy array)."""
    try:
        if not base64_string:
            return None
        if "," in base64_string:
            base64_string = base64_string.split(",")[1]
        if not base64_string:
            return None
        img_data = base64.b64decode(base64_string)
        if not img_data:
            return None
        np_arr = np.frombuffer(img_data, np.uint8)
        if np_arr.size == 0:
            return None
        img = cv2.imdecode(np_arr, cv2.IMREAD_COLOR)
        return img
    except Exception:
        return None

def check_liveness(img):
    """
    Simulasi Passive Liveness Detection. 
    Mendeteksi apakah gambar blur (misal karena kertas foto digerakkan / kualitas webcam buruk).
    Dalam implementasi produksi, gunakan model CNN khusus anti-spoofing seperti Silent-Face-Anti-Spoofing.
    """
    gray = cv2.cvtColor(img, cv2.COLOR_BGR2GRAY)
    variance = cv2.Laplacian(gray, cv2.CV_64F).var()
    if variance < 10.0: # Threshold blur
        return False, "Spoofing Terdeteksi: Gambar tidak fokus atau dari media cetak (blur)."
    return True, "Liveness OK"

def get_face_encoding(base64_image):
    """Mengekstrak fitur wajah menggunakan DeepFace (tidak butuh C++ Build Tools)."""
    img = decode_base64_image(base64_image)
    if img is None:
        return None, "Gambar tidak valid"
    
    # Pengecekan liveness (Anti-Spoofing)
    is_live, msg = check_liveness(img)
    if not is_live:
        return None, msg

    try:
        # Ekstrak fitur wajah. Facenet menghasilkan 128-dimensional vector.
        # enforce_detection=True akan melempar ValueError jika wajah tidak ditemukan.
        results = DeepFace.represent(img_path=img, model_name="Facenet", detector_backend="opencv", enforce_detection=True)
        
        if len(results) == 0:
            return None, "Tidak ada wajah yang terdeteksi dalam frame"
        if len(results) > 1:
            return None, "Terdeteksi lebih dari satu wajah. Pastikan hanya ada satu wajah."
            
        return results[0]["embedding"], "Success"
        
    except ValueError:
        # DeepFace melempar ValueError jika tidak mendeteksi wajah sama sekali
        return None, "Tidak ada wajah yang terdeteksi dalam frame"
    except Exception as e:
        return None, f"Terjadi kesalahan saat memproses wajah: {str(e)}"

def cosine_distance(source_representation, test_representation):
    """Menghitung cosine distance antara dua vektor secara manual."""
    a = np.matmul(np.transpose(source_representation), test_representation)
    b = np.sum(np.multiply(source_representation, source_representation))
    c = np.sum(np.multiply(test_representation, test_representation))
    return 1 - (a / (np.sqrt(b) * np.sqrt(c)))

def match_face(target_encoding, known_encodings, threshold=0.40):
    """
    Mencocokkan wajah baru dengan kumpulan wajah di database menggunakan Cosine Distance.
    Threshold Cosine untuk Facenet berkisar di 0.40. Semakin kecil nilainya, semakin ketat.
    """
    if not known_encodings:
        return -1, False
    
    distances = []
    for known in known_encodings:
        dist = cosine_distance(known, target_encoding)
        distances.append(dist)
        
    best_match_index = np.argmin(distances)
    
    if distances[best_match_index] <= threshold:
        return best_match_index, True
    return -1, False
