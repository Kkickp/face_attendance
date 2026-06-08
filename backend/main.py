from fastapi import FastAPI, Depends, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from sqlalchemy.orm import Session
from pydantic import BaseModel
import json

from database import SessionLocal, engine, Base, Mahasiswa, Presensi
from face_utils import get_face_encoding, match_face

# Inisialisasi API
app = FastAPI(title="Face Recognition Attendance Engine")

# Mengizinkan akses dari domain PHP
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=False,
    allow_methods=["*"],
    allow_headers=["*"],
)

def get_db():
    db = SessionLocal()
    try:
        yield db
    finally:
        db.close()

# Schema Request
class EnrollRequest(BaseModel):
    nim: str
    nama_lengkap: str
    foto_base64: str

class RecognizeRequest(BaseModel):
    foto_base64: str

@app.post("/api/enroll")
def enroll_face(req: EnrollRequest, db: Session = Depends(get_db)):
    # Cek apakah NIM sudah ada di database
    existing = db.query(Mahasiswa).filter(Mahasiswa.nim == req.nim).first()
    if existing:
        return {"status": "error", "message": "NIM sudah terdaftar."}
        
    encoding, msg = get_face_encoding(req.foto_base64)
    if encoding is None:
        return {"status": "error", "message": msg}
        
    new_mhs = Mahasiswa(
        nim=req.nim,
        nama_lengkap=req.nama_lengkap,
        face_encoding=json.dumps(encoding)
    )
    db.add(new_mhs)
    db.commit()
    return {"status": "success", "message": "Pendaftaran wajah berhasil."}

@app.post("/api/recognize")
def recognize_face(req: RecognizeRequest, db: Session = Depends(get_db)):
    target_encoding, msg = get_face_encoding(req.foto_base64)
    if target_encoding is None:
        # Jika spoofing atau tidak ada wajah, tolak
        return {"status": "error", "message": msg}
        
    # Ambil semua data wajah dari DB untuk dibandingkan
    students = db.query(Mahasiswa).all()
    if not students:
        return {"status": "error", "message": "Sistem belum memiliki data wajah."}
        
    known_encodings = []
    nims = []
    names = []
    for s in students:
        known_encodings.append(json.loads(s.face_encoding))
        nims.append(s.nim)
        names.append(s.nama_lengkap)
        
    best_match_idx, is_match = match_face(target_encoding, known_encodings)
    
    if is_match:
        matched_nim = nims[best_match_idx]
        matched_name = names[best_match_idx]
        
        # Mencatat kehadiran ke database presensi
        new_log = Presensi(nim=matched_nim, status="Hadir")
        db.add(new_log)
        db.commit()
        
        return {
            "status": "success", 
            "nama": matched_name, 
            "nim": matched_nim, 
            "message": f"Presensi Berhasil, Halo {matched_name}"
        }
    else:
        return {"status": "unknown", "message": "Wajah Tidak Dikenali."}
