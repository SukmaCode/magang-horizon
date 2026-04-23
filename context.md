1. Strategi Autentikasi Sanctum (SPA Mode)

Karena kamu menggunakan Inertia.js, cara terbaik menggunakan Sanctum adalah melalui Cookie-based Session Authentication. Ini lebih aman daripada menyimpan token di localStorage karena terlindungi dari serangan XSS.

    Middleware: Pastikan EnsureFrontendRequestsAreStateful aktif di Kernel.php.

    CSRF Protection: Setiap request dari React akan secara otomatis divalidasi oleh Laravel melalui cookie.

2. Fitur Registrasi (Multi-Role)

Registrasi tidak bisa disamakan untuk semua aktor. Biasanya, hanya Mahasiswa dan Industri yang melakukan registrasi mandiri. Aktor lain (Dosen & Admin) biasanya didaftarkan oleh sistem.

    Workflow Registrasi:

        User mengisi form di React.

        Controller menyimpan data ke tabel users.

        Tergantung pada role_id, sistem membuat entitas di tabel mahasiswas atau industris.

    Validasi: Gunakan Form Request Laravel untuk memastikan NIM (untuk mahasiswa) atau Nama Perusahaan (untuk industri) bersifat unik.

3. Fitur Login & Redirect Berdasarkan Role

Masalah umum di sistem multi-aktor adalah "kemana user diarahkan setelah login?".

    Custom Redirect: Di AuthenticatedSessionController, buat logika dinamis. Jika role adalah admin, arahkan ke /admin/dashboard, jika mahasiswa, ke /mahasiswa/dashboard.

    Auth Payload di Inertia: Kirim data user dan rolenya melalui HandleInertiaRequests middleware agar komponen React tahu menu apa saja yang harus ditampilkan.

4. Arsitektur Pengguna (Role-Based Access Control)

Sistem ini memiliki setidaknya 5 aktor berbeda. Kamu harus memastikan setiap aktor memiliki tampilan dashboard dan hak akses yang berbeda:

    Mahasiswa: Input CV, tanda tangan digital, input absensi/kegiatan, Jika magang sudah selesai susun laporan magang

    Supervisor Industri: Review CV, upload agreement, approval absensi harian.

    Dosen Pembimbing (Supervisor Kampus 1): Approval pendaftaran dan monitoring progres

    Dosen Prodi (Supervisor Kampus 2): Review laporan dari dosen pembimbing dan memberikan penilaian akhir.

    Admin/Sistem: Verifikasi data, plotting pembimbing (SK), dan penerbitan sertifikat.

5. Manajemen Dokumen & Digital Signature

Alur "Agreement" dan "SK" melibatkan dokumen legal. Hal yang perlu diperhatikan:

    File Handling: Sistem harus mampu menangani upload PDF (CV, Agreement, Laporan) dengan batasan ukuran file yang ketat.

    E-Signature: Kamu bisa menggunakan library untuk tanda tangan digital di canvas atau integrasi API pihak ketiga agar proses tanda tangan mahasiswa dan industri bisa dilakukan langsung di website.

    Generate PDF: Sistem sebaiknya bisa men-generate sertifikat secara otomatis menggunakan template PDF setelah admin melakukan verifikasi akhir.

6. Logika Workflow (State Management)

Setiap tahapan dalam gambar tersebut adalah sebuah "State".

    Sistem tidak boleh mengizinkan mahasiswa mengisi absensi jika tahap "Admin assign pembimbing" belum selesai.

    Gunakan status di database (misal: status: 'waiting_selection', 'on_internship', 'completed') untuk mengontrol apa yang bisa diakses user di setiap tahap.

7. Fitur Absensi & Journaling (Real-time)

Karena mahasiswa harus input absensi dan kegiatan harian:

    Validasi: Pertimbangkan untuk menambahkan fitur geofencing atau minimal timestamp agar data absensi akurat.

    Notifikasi: Penting untuk memberikan notifikasi (bisa via Email atau WhatsApp API) kepada Supervisor Industri ketika ada absensi yang butuh di-approve.

8. Struktur Database

Kamu akan membutuhkan relasi tabel yang cukup kuat. Gambaran kasarnya:

    Users (untuk login semua aktor).

    Companies (data industri).

    Applications (menghubungkan mahasiswa dengan industri dan statusnya).

    Daily_Logs (untuk absensi dan jurnal harian).

    Assessments (untuk nilai akhir dari dua supervisor).

9. Teknologi yang Disarankan

   Backend: Laravel (PHP)

   Frontend: Intertia.js dengan React untuk dashboard yang interaktif.

   Mobile: Mengingat mahasiswa sering di lapangan, versi Mobile App dengan Responsive Web sangat penting untuk pengisian absensi harian.

10. Penanganan "Edge Cases"

   Sistem harus mampu me-reset status mahasiswa agar bisa melamar ke industri lain tanpa harus membuat akun baru.

   Pastikan ada fitur "Log History" agar mahasiswa bisa melihat histori lamaran mereka yang ditolak sebelumnya.
