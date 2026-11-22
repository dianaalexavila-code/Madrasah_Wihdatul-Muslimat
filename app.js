document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-confirm]').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!window.confirm(form.getAttribute('data-confirm'))) event.preventDefault();
        });
    });
    document.querySelectorAll('[data-auto-number]').forEach(function (field) {
        if (field.value) return;
        var year = new Date().getFullYear();
        fetch('nomor_induk.php?year=' + year, { headers: { 'Accept': 'application/json' } })
            .then(function (response) { if (!response.ok) throw new Error('Nomor induk gagal dimuat'); return response.json(); })
            .then(function (data) { if (data.number) field.value = data.number; })
            .catch(function () { field.placeholder = 'Isi nomor induk secara manual'; });
    });
    var birthDate = document.getElementById('tanggal_lahir');
    var ageLabel = document.getElementById('student-age');
    function updateAge() {
        if (!birthDate || !ageLabel || !birthDate.value) {
            if (ageLabel) ageLabel.textContent = 'Umur akan dihitung setelah tanggal lahir diisi.';
            return;
        }
        var birth = new Date(birthDate.value + 'T00:00:00');
        var today = new Date();
        if (Number.isNaN(birth.getTime()) || birth > today) {
            ageLabel.textContent = 'Tanggal lahir tidak valid.';
            return;
        }
        var age = today.getFullYear() - birth.getFullYear();
        if (today.getMonth() < birth.getMonth() || (today.getMonth() === birth.getMonth() && today.getDate() < birth.getDate())) age--;
        ageLabel.textContent = 'Umur saat ini: ' + age + ' tahun';
    }
    if (birthDate) {
        birthDate.addEventListener('change', updateAge);
        updateAge();
    }
});
