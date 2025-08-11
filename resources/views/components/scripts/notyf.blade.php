<!-- Notyf CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.css">
<script src="https://cdn.jsdelivr.net/npm/notyf@3/notyf.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const notyf = new Notyf({
            duration: 3000,
            position: { x: 'right', y: 'top' },
            types: [
                {
                    type: 'info',
                    background: '#0dcaf0',
                    icon: {
                        className: 'bi bi-info-circle',
                        tagName: 'i',
                        text: ''
                    }
                },
                {
                    type: 'warning',
                    background: '#ffc107',
                    icon: {
                        className: 'bi bi-exclamation-triangle',
                        tagName: 'i',
                        text: ''
                    }
                },
                {
                    type: 'error',
                    background: '#dc3545',
                    icon: {
                        className: 'bi bi-x-circle',
                        tagName: 'i',
                        text: ''
                    }
                },
                {
                    type: 'success',
                    background: '#198754',
                    icon: {
                        className: 'bi bi-check-circle',
                        tagName: 'i',
                        text: ''
                    }
                }
            ]
        });

        window.Livewire.on('notify', (detail) => {
            console.log('Received notify event:', detail); // Debug log

            const type = typeof detail?.type === 'string' ? detail.type : 'info';
            const message = typeof detail?.message === 'string' ? detail.message : '';

            if (message.trim()) {
                notyf.open({ type, message });
            } else {
                console.warn('Empty message received in notify event.');
            }
        });
    });
</script>