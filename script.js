document.getElementById('dutyForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Validasi tambahan
    if (!data.playerName || data.playerName.length < 3) {
        showResponse('Nama player minimal 3 karakter!', 'error');
        return;
    }
    
    if (data.dutyTime <= 0) {
        showResponse('Durasi duty harus lebih dari 0 jam!', 'error');
        return;
    }
    
    const submitBtn = document.querySelector('.submit-btn');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('submit.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            showResponse('Report duty berhasil dikirim! Terima kasih.', 'success');
            this.reset();
        } else {
            showResponse(result.message || 'Gagal mengirim report!', 'error');
        }
    } catch (error) {
        showResponse('Error koneksi. Silakan coba lagi.', 'error');
        console.error('Error:', error);
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

function showResponse(message, type) {
    const responseEl = document.getElementById('response');
    responseEl.textContent = message;
    responseEl.className = `response ${type}`;
    responseEl.style.display = 'block';
    
    if (type === 'success') {
        setTimeout(() => {
            responseEl.style.display = 'none';
        }, 5000);
    }
}
