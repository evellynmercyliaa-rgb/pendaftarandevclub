document.getElementById('formDaftar').addEventListener('submit', async function(e) {
    e.preventDefault(); 

    const nis    = document.getElementById('nis').value.trim();
    const nama   = document.getElementById('nama').value.trim();
    const divisi = document.getElementById('divisi').value;
    const email  = document.getElementById('email').value.trim();
    const errNis = document.getElementById('errNis');

    // Validasi Regex NIS (8 digit angka)
    const nisRegex = /^[0-9]{8}$/;
    if (!nisRegex.test(nis)) {
        errNis.classList.remove('hidden');
        return;
    } else {
        errNis.classList.add('hidden');
    }

    const payload = { nis, nama, divisi, email };

    try {
        const response = await fetch('api.php?action=daftar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });

        // Ambil respon sebagai teks dulu untuk mencegah crash parser JSON
        const textData = await response.text();

        let result;
        try {
            result = JSON.parse(textData);
        } catch (jsonErr) {
            // Jika PHP mengirim teks error HTML/Biasa, tampilkan di alert ini!
            Swal.fire({
                icon: 'error',
                title: 'Error dari Server (PHP)',
                html: `<div style="text-align:left; max-height:200px; overflow:auto;">${textData}</div>`,
                confirmButtonColor: '#ef4444'
            });
            return;
        }

        // Jika respon PHP berupa JSON yang benar
        if (result.status === 'success') {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: result.message,
                confirmButtonColor: '#10b981'
            });
            document.getElementById('formDaftar').reset();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: result.message,
                confirmButtonColor: '#ef4444'
            });
        }

    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Koneksi Terputus!',
            text: 'Tidak bisa terhubung ke api.php. Pastikan nama file/folder sudah benar!',
            confirmButtonColor: '#ef4444'
        });
    }
});