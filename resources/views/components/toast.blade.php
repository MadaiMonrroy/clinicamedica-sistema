<div id="toast-container" class="fixed top-5 right-5 z-[9999] space-y-3 pointer-events-none"></div>

<script>
window.showToast = function ({
    title = 'Notificación',
    message = '',
    type = 'info',
    duration = 4000
} = {}) {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const styles = {
        success: {
            wrapper: 'bg-green-50 border-green-200 text-green-700',
            iconWrap: 'text-green-500',
            title: 'text-green-600',
            message: 'text-slate-600',
            close: 'text-green-400 hover:text-green-600',
            icon: `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            `
        },
        info: {
            wrapper: 'bg-sky-50 border-sky-200 text-sky-700',
            iconWrap: 'text-sky-500',
            title: 'text-sky-600',
            message: 'text-slate-600',
            close: 'text-sky-400 hover:text-sky-600',
            icon: `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/>
                </svg>
            `
        },
        warn: {
            wrapper: 'bg-amber-50 border-amber-200 text-amber-700',
            iconWrap: 'text-amber-500',
            title: 'text-amber-600',
            message: 'text-slate-600',
            close: 'text-amber-400 hover:text-amber-600',
            icon: `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M10.29 3.86l-7.55 13.09A2 2 0 004.47 20h15.06a2 2 0 001.73-3.05L13.71 3.86a2 2 0 00-3.42 0z"/>
                </svg>
            `
        },
        error: {
            wrapper: 'bg-red-50 border-red-200 text-red-700',
            iconWrap: 'text-red-500',
            title: 'text-red-600',
            message: 'text-slate-600',
            close: 'text-red-400 hover:text-red-600',
            icon: `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 6l12 12M18 6L6 18"/>
                </svg>
            `
        },
        secondary: {
            wrapper: 'bg-slate-50 border-slate-200 text-slate-700',
            iconWrap: 'text-slate-500',
            title: 'text-slate-600',
            message: 'text-slate-600',
            close: 'text-slate-400 hover:text-slate-600',
            icon: `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="8" stroke-width="2"></circle>
                </svg>
            `
        },
        contrast: {
            wrapper: 'bg-slate-900 border-slate-800 text-white',
            iconWrap: 'text-white',
            title: 'text-white',
            message: 'text-slate-300',
            close: 'text-slate-400 hover:text-white',
            icon: `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="8" stroke-width="2"></circle>
                </svg>
            `
        }
    };

    const config = styles[type] || styles.info;

    const toast = document.createElement('div');
    toast.className = `
        pointer-events-auto w-[360px] max-w-[calc(100vw-2rem)]
        border rounded-2xl shadow-[0_10px_30px_rgba(15,23,42,0.12)]
        ${config.wrapper}
        toast-enter
    `;

    toast.innerHTML = `
        <div class="flex items-start gap-3 px-4 py-3">
            <div class="mt-0.5 shrink-0 ${config.iconWrap}">
                ${config.icon}
            </div>

            <div class="min-w-0 flex-1">
                <p class="text-[1rem] font-semibold leading-5 ${config.title}">
                    ${title}
                </p>
                <p class="mt-1 text-[0.95rem] leading-5 ${config.message}">
                    ${message}
                </p>
            </div>

            <button type="button" class="shrink-0 transition ${config.close}" aria-label="Cerrar">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    `;

    const closeButton = toast.querySelector('button');

    const removeToast = () => {
        toast.classList.remove('toast-enter');
        toast.classList.add('toast-leave');
        setTimeout(() => toast.remove(), 220);
    };

    closeButton.addEventListener('click', removeToast);

    container.appendChild(toast);

    setTimeout(removeToast, duration);
};
</script>

<style>
.toast-enter {
    animation: toastIn 0.22s ease-out;
}

.toast-leave {
    animation: toastOut 0.2s ease-in forwards;
}

@keyframes toastIn {
    from {
        opacity: 0;
        transform: translateY(-8px) scale(0.98);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes toastOut {
    from {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
    to {
        opacity: 0;
        transform: translateY(-6px) scale(0.98);
    }
}
</style>