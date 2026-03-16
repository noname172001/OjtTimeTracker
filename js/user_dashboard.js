// Live clock
    function updateDateTime() {
        const now = new Date();
        const pad = n => String(n).padStart(2,'0');
        const mm = pad(now.getMonth()+1), dd = pad(now.getDate()), yy = now.getFullYear();
        const hh = pad(now.getHours()), mi = pad(now.getMinutes()), ss = pad(now.getSeconds());
        document.getElementById('datetime').textContent = `${mm}/${dd}/${yy} ${hh}:${mi}:${ss}`;
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);

    // Modal logic
    const overlay = document.getElementById('clockOutOverlay');
    const modal   = document.getElementById('clockOutModal');
    function openModal()  { overlay.classList.add('active'); modal.style.display = 'block'; }
    function closeModal() { overlay.classList.remove('active'); modal.style.display = 'none'; }

    const openBtn   = document.getElementById('openClockOutModal');
    const closeBtn  = document.getElementById('closeClockOutModal');
    const cancelBtn = document.getElementById('cancelClockOut');

    if (openBtn)   openBtn.addEventListener('click', openModal);
    if (closeBtn)  closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', closeModal);