# Office Attendance - Application Flow

Dokumen ini menjelaskan alur kerja lengkap (flow) dari aplikasi Absensi Kantor, mulai dari autentikasi hingga manajemen data.

## 1. Autentikasi & Otorisasi

Sistem menggunakan multi-role authentication (Admin & Employee).

-   **Login**: `/login`
    -   Sistem mengecek kredensial (Email & Password).
    -   **Redirect Logic**:
        -   Jika `role === 'admin'` -> Redirect ke `/admin/dashboard`.
        -   Jika `role === 'employee'` -> Redirect ke `/dashboard`.
-   **Security**:
    -   Route dilindungi middleware `auth`.
    -   Middleware khusus `role:admin` atau `role:employee` membatasi akses antar role.

---

## 2. Alur Karyawan (Employee)

Karyawan menggunakan aplikasi untuk melakukan absensi harian.

### A. Dashboard (`/dashboard`)

-   Menampilkan ringkasan status absensi hari ini.
-   Navigasi ke halaman absensi.

### B. Proses Absensi (`/attendance`)

Halaman ini adalah inti fitur karyawan.

1.  **Inisialisasi Halaman**:

    -   Browser meminta izin akses **Kamera** dan **Lokasi (GPS)**.
    -   Sistem mengambil daftar `OfficeLocation` (Titik Absensi) dari database untuk validasi client-side.

2.  **Validasi Pra-Submit (Client-Side)**:

    -   **Geofencing**: Script JS menghitung jarak karyawan ke kantor terdekat menggunakan rumus Haversine.
        -   ✅ **In Range**: Tombol submit aktif.
        -   ❌ **Too Far**: Tombol submit non-aktif (kecuali Testing Mode `ALLOW_ANY_LOCATION` aktif).
    -   **Selfie**: User wajib mengambil foto via webcam. Foto dikonversi ke Base64.

3.  **Submit Absensi (Server-Side Logic)**:

    -   **Endpoint**: `POST /attendance`
    -   **Langkah Validasi Server**:
        1.  **Blocked Device Check**: Cek apakah `device_info` (User Agent) ada di daftar blacklist.
        2.  **Duplicate Check**: Cek apakah user sudah absen dengan status yang sama (IN/OUT) pada hari ini.
        3.  **Geofencing Re-Check**: Server menghitung ulang jarak berdasarkan koordinat yang dikirim untuk mencegah manipulasi client-side.
        4.  **Photo Validation**: Decode Base64 image dan validasi format (JPG/PNG).

4.  **Penyimpanan Data**:
    -   Data absensi disimpan ke tabel `attendances`.
    -   Foto disimpan ke `storage/app/private/attendance_photos/YYYYMMDD_HHMMSSmmm.ext`.
    -   Relasi foto dicatat di tabel `attendance_photos`.

### C. Riwayat Absensi (`/attendance/history`)

-   Menampilkan daftar log absensi pribadi karyawan.

---

## 3. Alur Administrator (Admin)

Admin memiliki kontrol penuh atas sistem.

### A. Dashboard (`/admin/dashboard`)

-   Overview statistik kehadiran hari ini.

### B. Manajemen Karyawan (`/admin/employees`)

-   CRUD (Create, Read, Update, Delete) data user/karyawan.
-   Mengatur password dan role.

### C. Monitoring Absensi (`/admin/attendances`)

-   **List**: Melihat daftar siapa saja yang sudah absen.
-   **Detail**:
    -   Melihat lokasi (Lat, Long, Jarak) saat absen.
    -   **Lihat Foto**: Admin mengakses foto via route aman `/admin/attendance-photo/{id}` yang mendekripsi/membaca file dari private storage.
    -   **Hapus**: Menghapus record absensi jika ada kesalahan/kecurangan (Foto di storage juga ikut terhapus).

### D. Konfigurasi Lokasi (`/admin/office-locations`)

-   Admin menentukan titik koordinat kantor (Latitude, Longitude) dan Radius (meter) yang diperbolehkan untuk absen.

### E. Blocked Devices (`/admin/blocked-devices`)

-   Admin dapat memblokir User Agent/Device ID tertentu yang mencurigakan, sehingga device tersebut tidak bisa digunakan untuk absen lagi.

---

## 4. Struktur Data (ERD Simpel)

-   **Users**: Menyimpan data login & role.
-   **OfficeLocations**: Daftar titik valid untuk absen.
-   **Attendances**: Log transaksi absen (relasi ke User & OfficeLocation).
-   **AttendancePhotos**: Menyimpan path foto bukti kehadiran.
-   **BlockedDevices**: Daftar blacklist perangkat.
