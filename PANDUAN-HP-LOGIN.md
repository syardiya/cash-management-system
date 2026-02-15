# 📱 PANDUAN AKSES DARI HP & FIX LOGIN

## ✅ MASALAH YANG TELAH DIPERBAIKI

1. **Tampilan "Tumpuk" di HP (Fixed)**
   - Masalah: Ada typo pada kode `viewport` di `login.php` yang membuat tampilan di HP jadi kecil/rusak.
   - Solusi: Kode sudah diperbaiki, sekarang tampilan akan menyesuaikan layar HP (responsive).

2. **Login "Invalid Credential" (Fixed)**
   - Masalah: Akun admin belum terbentuk dengan benar di database atau password hash tidak cocok.
   - Solusi: Saya buatkan script reset otomatis.

3. **Akses dari HP (Flexibel)**
   - Masalah: Link `localhost` tidak bisa dibuka di HP.
   - Solusi: Saya ubah konfigurasi agar otomatis mendeteksi IP Address komputer Anda.

---

## 🛠️ CARA LOGIN SEKARANG

Saya telah membuat tool khusus untuk memperbaiki akun admin Anda.

**Langkah 1: Reset Akun Admin**
1. Buka browser di PC/Laptop
2. Kunjungi link ini untuk reset otomatis:
   👉 **http://localhost/cash-management-system/setup_admin.php**
3. Anda akan melihat pesan **"✅ User created successfully!"** atau **"✅ Password reset successfully!"**

**Langkah 2: Login**
Setelah reset, gunakan akun ini:
- **Username:** `admin`
- **Password:** `Admin123!`

---

## 📲 CARA BUKA DI HP (SATU WIFI)

Agar bisa dibuka di HP, komputer dan HP **WAJIB** terhubung ke **WiFi yang sama**.

**1. Cari IP Address Komputer**
- Buka PowerShell atau CMD di komputer
- Ketik: `ipconfig`
- Cari baris **IPv4 Address** (Contoh: `192.168.1.10`)

**2. Buka di Browser HP**
- Buka Chrome/Safari di HP
- Ketik alamat IP tadi + folder project
- Format: `http://[IP-KOMPUTER]/cash-management-system`
- Contoh: **http://192.168.1.10/cash-management-system**

**3. Coba Login**
- Gunakan akun `admin` / `Admin123!`
- Tampilan sekarang sudah rapi dan tidak tumpuk!

---

## ❓ JIKA MASIH GAGAL

**Q: Masih tidak bisa dibuka di HP?**
A: Pastikan firewall windows tidak memblokir. Matikan sementara Windows Firewall untuk test.

**Q: Tampilan masih aneh?**
A: Clear cache di browser HP atau buka mode Incognito (Tab Penyamaran).

**Q: Login masih gagal?**
A: Pastikan huruf besar/kecil password benar: `Admin123!` (A besar).

---

**Selamat mencoba! 🚀**
