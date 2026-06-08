from sqlalchemy import create_engine, Column, String, Integer, Text, ForeignKey, TIMESTAMP
from sqlalchemy.orm import declarative_base, sessionmaker, relationship
from datetime import datetime

# Menggunakan default koneksi XAMPP/MySQL lokal
DATABASE_URL = "mysql+pymysql://root:@localhost/face_attendance_db"

engine = create_engine(DATABASE_URL)
SessionLocal = sessionmaker(autocommit=False, autoflush=False, bind=engine)
Base = declarative_base()

class Mahasiswa(Base):
    __tablename__ = "mahasiswa"
    nim = Column(String(20), primary_key=True, index=True)
    nama_lengkap = Column(String(100), nullable=False)
    face_encoding = Column(Text, nullable=False)
    created_at = Column(TIMESTAMP, default=datetime.utcnow)

class Presensi(Base):
    __tablename__ = "presensi"
    id = Column(Integer, primary_key=True, index=True, autoincrement=True)
    nim = Column(String(20), ForeignKey("mahasiswa.nim", ondelete="CASCADE"), nullable=False)
    waktu_presensi = Column(TIMESTAMP, default=datetime.utcnow)
    status = Column(String(50), nullable=False)
    foto_bukti = Column(Text, nullable=True)
