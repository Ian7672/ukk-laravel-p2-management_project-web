    @once
    <style>
        .action-sheet {
            position: fixed;
            inset: 0;
            display: grid;
            align-items: flex-end;
            justify-items: center;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s ease;
            z-index: 1350;
        }

        .action-sheet:not(.hidden) {
            pointer-events: auto;
            opacity: 1;
        }

        .action-sheet.hidden {
            display: none;
        }

        .action-sheet__overlay {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(12px);
        }

        .action-sheet__panel {
            position: relative;
            width: min(520px, 100% - 1.5rem);
            border-radius: 28px 28px 0 0;
            background: rgba(17, 24, 39, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 -20px 45px rgba(15, 23, 42, 0.4);
            padding: 1.75rem 1.75rem 1.5rem;
            pointer-events: auto;
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
            max-height: min(90vh, 640px);
            overflow: hidden;
        }

        .action-sheet__handle {
            width: 48px;
            height: 5px;
            border-radius: 999px;
            background: rgba(148, 163, 184, 0.4);
            margin: 0 auto;
        }

        .action-sheet__header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
        }

        .action-sheet__title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #f8fafc;
            margin-bottom: 0.35rem;
        }

        .action-sheet__subtitle {
            font-size: 0.875rem;
            color: rgba(226, 232, 240, 0.75);
            margin-bottom: 0.15rem;
        }

        .action-sheet__message {
            font-size: 0.95rem;
            color: rgba(226, 232, 240, 0.85);
            line-height: 1.6;
        }

        .action-sheet__close {
            border: none;
            background: rgba(148, 163, 184, 0.16);
            color: rgba(226, 232, 240, 0.85);
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.25s ease;
        }

        .action-sheet__close:hover {
            background: rgba(99, 102, 241, 0.28);
            color: #ffffff;
        }

        .action-sheet__actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
        }

        .action-sheet__body {
            background: rgba(15, 23, 42, 0.55);
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 18px;
            padding: 1.25rem 1.35rem;
            flex: 1 1 auto;
            overflow-y: auto;
            overscroll-behavior: contain;
        }

        .action-sheet__body::-webkit-scrollbar {
            width: 6px;
        }

        .action-sheet__body::-webkit-scrollbar-track {
            background: transparent;
        }

        .action-sheet__body::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.35);
            border-radius: 999px;
        }

        .action-sheet__body::-webkit-scrollbar-thumb:hover {
            background: rgba(148, 163, 184, 0.5);
        }

        .action-sheet__body > *:last-child {
            margin-bottom: 0;
        }

        .action-sheet__actions .btn-modern {
            min-width: 120px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.88), rgba(59, 130, 246, 0.88));
            border: none;
            color: #ffffff;
            box-shadow: 0 12px 28px rgba(99, 102, 241, 0.35);
            padding: 0.6rem 1.35rem;
            border-radius: 999px;
        }

        .action-sheet__actions .btn-modern:hover {
            background: linear-gradient(135deg, rgba(99, 102, 241, 1), rgba(59, 130, 246, 1));
            box-shadow: 0 16px 34px rgba(99, 102, 241, 0.4);
        }

        .action-sheet .loading-spinner {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 2px solid rgba(148, 163, 184, 0.55);
            border-top-color: transparent;
            display: inline-block;
            margin-right: 0.5rem;
            animation: action-sheet-spinner 0.75s linear infinite;
            vertical-align: middle;
        }

        /* PERFECT CANCEL BUTTON - DARK/LIGHT MODE OPTIMIZED */
        .action-sheet__actions .btn-outline {
            /* Dark mode default */
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.2)) !important;
            border: 1px solid rgba(239, 68, 68, 0.4) !important;
            color: #fca5a5 !important;
            padding: 0.6rem 1.35rem !important;
            border-radius: 999px !important;
            font-size: 0.875rem !important;
            font-weight: 600 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            backdrop-filter: blur(20px) saturate(180%) !important;
            min-width: 100px !important;
}

.action-sheet__actions .btn-outline:hover {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.3), rgba(220, 38, 38, 0.3)) !important;
    border-color: rgba(239, 68, 68, 0.6) !important;
    color: #fecaca !important;
    transform: translateY(-2px) !important;
    box-shadow: 0 8px 20px rgba(239, 68, 68, 0.25) !important;
}

.action-sheet__actions .btn-outline:active {
    transform: translateY(0) !important;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2) !important;
}

/* Light mode - lebih soft dan matching dengan tema light */
[data-theme="light"] .action-sheet__actions .btn-outline {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.12), rgba(220, 38, 38, 0.12)) !important;
    border: 1px solid rgba(239, 68, 68, 0.3) !important;
    color: #dc2626 !important;
    box-shadow: 0 2px 6px rgba(239, 68, 68, 0.1) !important;
    border-radius: 999px !important;
}

[data-theme="light"] .action-sheet__actions .btn-outline:hover {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.18), rgba(220, 38, 38, 0.18)) !important;
    border-color: rgba(239, 68, 68, 0.45) !important;
    color: #b91c1c !important;
    box-shadow: 0 6px 16px rgba(239, 68, 68, 0.15) !important;
    transform: translateY(-2px) !important;
}

[data-theme="light"] .action-sheet__actions .btn-outline:active {
    transform: translateY(0) !important;
    box-shadow: 0 3px 8px rgba(239, 68, 68, 0.12) !important;
}

        [data-theme="light"] .action-sheet__overlay {
            background: rgba(15, 23, 42, 0.35);
        }

        [data-theme="light"] .action-sheet__panel {
            background: rgba(255, 255, 255, 0.97);
            border: 1px solid rgba(203, 213, 225, 0.7);
            box-shadow: 0 -16px 40px rgba(148, 163, 184, 0.25);
        }

        [data-theme="light"] .action-sheet__title {
            color: #0f172a;
        }

        [data-theme="light"] .action-sheet__subtitle {
            color: #475569;
        }

        [data-theme="light"] .action-sheet__message {
            color: #334155;
        }

        [data-theme="light"] .action-sheet__body {
            background: rgba(248, 250, 252, 0.95);
            border: 1px solid rgba(203, 213, 225, 0.65);
        }

        [data-theme="light"] .action-sheet__close {
            background: rgba(226, 232, 240, 0.9);
            color: #1f2937;
        }

        [data-theme="light"] .action-sheet__close:hover {
            background: rgba(129, 140, 248, 0.25);
            color: #1d4ed8;
        }

        [data-theme="light"] .action-sheet__actions .btn-outline {
            color: #1f2937;
            border-color: rgba(148, 163, 184, 0.5);
            border-radius: 999px;
        }

        [data-theme="light"] .action-sheet__actions .btn-outline:hover {
            background: rgba(148, 163, 184, 0.18);
        }

        [data-theme="light"] .action-sheet__actions .btn-modern {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.92), rgba(59, 130, 246, 0.92));
            box-shadow: 0 12px 30px rgba(99, 102, 241, 0.28);
            color: #ffffff;
            border-radius: 999px;
        }

        [data-theme="light"] .action-sheet__actions .btn-modern:hover {
            background: linear-gradient(135deg, rgba(79, 70, 229, 1), rgba(37, 99, 235, 1));
            box-shadow: 0 16px 36px rgba(99, 102, 241, 0.35);
        }

        body.action-sheet-open {
            overflow: hidden;
        }

        @keyframes action-sheet-spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <div class="action-sheet hidden" id="actionSheet" aria-hidden="true">
        <div class="action-sheet__overlay" data-action-sheet-close></div>
        <div class="action-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="actionSheetTitle">
            <div class="action-sheet__handle"></div>
            <div class="action-sheet__header">
                <div>
                    <p class="action-sheet__subtitle mb-0" id="actionSheetSubtitle"></p>
                    <h5 class="action-sheet__title mb-0" id="actionSheetTitle">Konfirmasi</h5>
                </div>
                <button type="button" class="action-sheet__close" data-action-sheet-close aria-label="Tutup konfirmasi">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="action-sheet__body">
                <div class="action-sheet__message mb-0" id="actionSheetMessage">Apakah Anda yakin ingin melanjutkan tindakan ini?</div>
            </div>
            <div class="action-sheet__actions">
                <button type="button" class="btn-outline btn-sm" id="actionSheetCancelBtn" data-action-sheet-close>
                    Batal
                </button>
                <button type="button" class="btn-modern btn-sm" id="actionSheetConfirmBtn">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    </div>

    <script>
    (() => {
        if (window.ActionSheet) {
            return;
        }

        const sheet = document.getElementById('actionSheet');
        if (!sheet) {
            return;
        }

        const titleEl = document.getElementById('actionSheetTitle');
        const subtitleEl = document.getElementById('actionSheetSubtitle');
        const messageEl = document.getElementById('actionSheetMessage');
        const confirmBtn = document.getElementById('actionSheetConfirmBtn');
        const cancelBtn = document.getElementById('actionSheetCancelBtn');
        const closeTriggers = sheet.querySelectorAll('[data-action-sheet-close]');

        let currentAction = null;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

        const syncActionButtons = (action = currentAction) => {
            if (!action) {
                confirmBtn.classList.remove('d-none');
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = 'Ya, Lanjutkan';

                cancelBtn.classList.remove('d-none');
                cancelBtn.textContent = 'Batal';
                return;
            }

            if (action.confirmLabel === null) {
                confirmBtn.classList.add('d-none');
                confirmBtn.disabled = true;
                confirmBtn.innerHTML = '';
            } else {
                confirmBtn.classList.remove('d-none');
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = action.confirmLabel || 'Ya, Lanjutkan';
            }

            if (action.cancelLabel === null) {
                cancelBtn.classList.add('d-none');
            } else {
                cancelBtn.classList.remove('d-none');
                cancelBtn.textContent = action.cancelLabel || 'Batal';
            }
        };

        const closeSheet = () => {
            if (sheet.classList.contains('hidden')) {
                return;
            }

            sheet.classList.add('hidden');
            sheet.setAttribute('aria-hidden', 'true');
            document.body.classList.remove('action-sheet-open');

            const previousAction = currentAction;
            syncActionButtons(null);
            currentAction = null;

            document.dispatchEvent(new CustomEvent('action-sheet:closed', { detail: previousAction }));
        };

        const parsePayload = (value) => {
            if (!value) {
                return {};
            }
            try {
                return JSON.parse(value);
            } catch (error) {
                console.warn('Tidak dapat mengurai payload aksi:', error);
                return {};
            }
        };

        const normalizeValue = (value) => {
            if (value === undefined || value === '') {
                return undefined;
            }
            if (value === 'null') {
                return null;
            }
            if (value === 'true') {
                return true;
            }
            if (value === 'false') {
                return false;
            }
            return value;
        };

        const openSheet = (options = {}) => {
            currentAction = {
                title: options.title ?? 'Konfirmasi',
                subtitle: options.subtitle ?? '',
                message: options.message ?? 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                messageHtml: options.messageHtml ?? (options.message && typeof options.message === 'object' && options.message.html ? options.message.html : null),
                confirmLabel: options.confirmLabel !== undefined ? options.confirmLabel : 'Ya, Lanjutkan',
                cancelLabel: options.cancelLabel !== undefined ? options.cancelLabel : 'Batal',
                loadingLabel: options.loadingLabel ?? 'Memproses...',
                formSelector: options.formSelector || null,
                url: options.url || null,
                method: (options.method || 'POST').toUpperCase(),
                payload: options.payload || {},
                successBehavior: options.successBehavior || '',
                closeOnSuccess: options.closeOnSuccess ?? true,
            };

            titleEl.textContent = currentAction.title;
            subtitleEl.textContent = currentAction.subtitle;
            subtitleEl.classList.toggle('d-none', !currentAction.subtitle);
            messageEl.innerHTML = '';

            if (currentAction.messageHtml) {
                messageEl.innerHTML = currentAction.messageHtml;
            } else if (typeof currentAction.message === 'string') {
                messageEl.textContent = currentAction.message;
            } else if (currentAction.message && typeof currentAction.message === 'object' && currentAction.message.html) {
                messageEl.innerHTML = currentAction.message.html;
            } else if (currentAction.message) {
                messageEl.innerHTML = currentAction.message;
            }

            syncActionButtons(currentAction);

            sheet.classList.remove('hidden');
            sheet.setAttribute('aria-hidden', 'false');
            document.body.classList.add('action-sheet-open');
            document.dispatchEvent(new CustomEvent('action-sheet:opened', { detail: currentAction }));
        };

        confirmBtn.addEventListener('click', async () => {
            if (!currentAction || confirmBtn.classList.contains('d-none')) {
                return;
            }

            confirmBtn.disabled = true;
            confirmBtn.innerHTML = `<span class="loading-spinner"></span>${currentAction.loadingLabel || 'Memproses...'}`;

            if (currentAction.formSelector) {
                const form = document.querySelector(currentAction.formSelector);
                if (form) {
                    form.submit();
                    return;
                }
                console.warn('Form aksi tidak ditemukan:', currentAction.formSelector);
                syncActionButtons();
                return;
            }

            if (currentAction.url) {
                try {
                    const response = await fetch(currentAction.url, {
                        method: currentAction.method,
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                        },
                        body: currentAction.method === 'GET' ? undefined : JSON.stringify(currentAction.payload),
                    });

                    if (!response.ok) {
                        const payload = await response.json().catch(() => ({}));
                        throw new Error(payload.message || `Request gagal (${response.status})`);
                    }

                    if (currentAction.successBehavior === 'reload') {
                        window.location.reload();
                        return;
                    }

                    if (currentAction.closeOnSuccess) {
                        closeSheet();
                    } else {
                        syncActionButtons();
                    }
                } catch (error) {
                    console.error('Gagal menjalankan aksi:', error);
                    alert(error.message || 'Terjadi kesalahan. Silakan coba lagi.');
                    syncActionButtons();
                }
                return;
            }

            closeSheet();
        });

        closeTriggers.forEach(trigger => {
            trigger.addEventListener('click', closeSheet);
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape' && !sheet.classList.contains('hidden')) {
                closeSheet();
            }
        });

        document.addEventListener('click', event => {
            const trigger = event.target.closest('[data-action-sheet-trigger]');
            if (!trigger) {
                return;
            }

            event.preventDefault();

            const payload = parsePayload(trigger.dataset.actionPayload);

            window.ActionSheet.open({
                title: trigger.dataset.actionTitle,
                subtitle: trigger.dataset.actionSubtitle,
                message: trigger.dataset.actionMessage,
                confirmLabel: normalizeValue(trigger.dataset.actionConfirmLabel),
                cancelLabel: normalizeValue(trigger.dataset.actionCancelLabel),
                loadingLabel: trigger.dataset.actionLoadingLabel,
                formSelector: trigger.dataset.actionForm,
                url: trigger.dataset.actionUrl,
                method: trigger.dataset.actionMethod,
                payload,
                successBehavior: trigger.dataset.actionSuccess,
            });
        });

        window.ActionSheet = {
            open: openSheet,
            close: closeSheet,
        };
    })();
    </script>
    @endonce
