@once
<style>
    .subtask-sheet {
        position: fixed;
        inset: 0;
        display: grid;
        align-items: flex-end;
        justify-items: center;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.25s ease;
        z-index: 1325;
    }

    .subtask-sheet:not(.hidden) {
        pointer-events: auto;
        opacity: 1;
    }

    .subtask-sheet.hidden {
        display: none;
    }

    .subtask-sheet__overlay {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.65);
        backdrop-filter: blur(14px);
    }

    .subtask-sheet__panel {
        position: relative;
        width: min(640px, 100% - 1.5rem);
        border-radius: 28px 28px 0 0;
        background: rgba(17, 24, 39, 0.97);
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 -24px 50px rgba(15, 23, 42, 0.45);
        padding: clamp(1.5rem, 2vw, 2rem);
        pointer-events: auto;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
        max-height: 85vh;
    }

    .subtask-sheet__handle {
        width: 52px;
        height: 5px;
        border-radius: 999px;
        background: rgba(148, 163, 184, 0.45);
        margin: 0 auto;
    }

    .subtask-sheet__header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1.25rem;
    }

    .subtask-sheet__title {
        font-size: 1.4rem;
        font-weight: 600;
        color: #f8fafc;
        margin-bottom: 0.35rem;
    }

    .subtask-sheet__subtitle {
        font-size: 0.92rem;
        color: rgba(226, 232, 240, 0.75);
        margin: 0;
    }

    .subtask-sheet__close {
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

    .subtask-sheet__close:hover {
        background: rgba(99, 102, 241, 0.3);
        color: #ffffff;
    }

    .subtask-sheet__body {
        overflow-y: auto;
        padding-right: 0.2rem;
        margin-right: -0.2rem;
    }

    .subtask-sheet__form .form-label {
        font-weight: 600;
        color: rgba(226, 232, 240, 0.9);
    }

    .subtask-sheet__form .form-control {
        background: rgba(15, 23, 42, 0.65);
        border: 1px solid rgba(148, 163, 184, 0.35);
        color: #f1f5f9;
        border-radius: 12px;
        padding: 0.75rem 0.9rem;
        transition: all 0.2s ease;
    }

    .subtask-sheet__form .form-control:focus {
        background: rgba(15, 23, 42, 0.85);
        border-color: rgba(129, 140, 248, 0.55);
        box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.2);
        color: #ffffff;
    }

    .subtask-sheet__actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .subtask-sheet__actions .btn-outline {
        background: transparent;
        border: 1px solid rgba(148, 163, 184, 0.35);
        color: rgba(226, 232, 240, 0.85);
        padding: 0.6rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.25s ease;
    }

    .subtask-sheet__actions .btn-outline:hover {
        background: rgba(148, 163, 184, 0.2);
        color: #ffffff;
    }

    body.subtask-sheet-open {
        overflow: hidden;
    }

    .subtask-sheet .loading-spinner {
        width: 16px;
        height: 16px;
        border-radius: 999px;
        border: 2px solid rgba(148, 163, 184, 0.5);
        border-top-color: transparent;
        display: inline-block;
        margin-right: 0.45rem;
        animation: subtask-sheet-spinner 0.8s linear infinite;
    }

    [data-theme="light"] .subtask-sheet__overlay {
        background: rgba(15, 23, 42, 0.28);
    }

    [data-theme="light"] .subtask-sheet__panel {
        background: rgba(255, 255, 255, 0.98);
        border: 1px solid rgba(203, 213, 225, 0.7);
        box-shadow: 0 -18px 45px rgba(148, 163, 184, 0.22);
    }

    [data-theme="light"] .subtask-sheet__title {
        color: #0f172a;
    }

    [data-theme="light"] .subtask-sheet__subtitle {
        color: #475569;
    }

    [data-theme="light"] .subtask-sheet__close {
        background: rgba(226, 232, 240, 0.95);
        color: #1f2937;
    }

    [data-theme="light"] .subtask-sheet__close:hover {
        background: rgba(129, 140, 248, 0.2);
        color: #1d4ed8;
    }

    [data-theme="light"] .subtask-sheet__form .form-label {
        color: #1f2937;
    }

    [data-theme="light"] .subtask-sheet__form .form-control {
        background: rgba(248, 250, 252, 0.96);
        border: 1px solid rgba(203, 213, 225, 0.65);
        color: #1f2937;
    }

    [data-theme="light"] .subtask-sheet__form .form-control:focus {
        background: rgba(255, 255, 255, 1);
        border-color: rgba(99, 102, 241, 0.45);
        box-shadow: 0 0 0 3px rgba(129, 140, 248, 0.2);
        color: #111827;
    }

    [data-theme="light"] .subtask-sheet__actions .btn-outline {
        color: #1f2937;
        border-color: rgba(148, 163, 184, 0.5);
    }

    [data-theme="light"] .subtask-sheet__actions .btn-outline:hover {
        background: rgba(148, 163, 184, 0.2);
    }

    @media (max-width: 576px) {
        .subtask-sheet__panel {
            width: 100%;
            border-radius: 18px 18px 0 0;
            padding: 1.25rem;
        }
    }

    @keyframes subtask-sheet-spinner {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>

<div class="subtask-sheet hidden" id="subtaskSheet" aria-hidden="true">
    <div class="subtask-sheet__overlay" data-subtask-sheet-close></div>
    <div class="subtask-sheet__panel" role="dialog" aria-modal="true" aria-labelledby="subtaskSheetTitle">
        <div class="subtask-sheet__handle"></div>
        <div class="subtask-sheet__header">
            <div>
                <h5 class="subtask-sheet__title mb-0" id="subtaskSheetTitle">Add Subtask</h5>
                <p class="subtask-sheet__subtitle mb-0" id="subtaskSheetSubtitle"></p>
            </div>
            <button type="button" class="subtask-sheet__close" data-subtask-sheet-close aria-label="Tutup formulir subtask">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="subtask-sheet__body">
            <form id="subtaskSheetForm" class="subtask-sheet__form" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="subtaskSheetTitleInput" class="form-label" id="subtaskSheetTitleLabel">Judul Subtask</label>
                    <input type="text" class="form-control" id="subtaskSheetTitleInput" name="subtask_title"
                           placeholder="Masukkan judul subtask" required>
                </div>
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="subtaskSheetEstimateInput" class="form-label" id="subtaskSheetEstimateLabel">Estimasi Waktu (Jam)</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="subtaskSheetEstimateInput"
                               name="estimated_hours" placeholder="Contoh: 2.5">
                    </div>
                </div>
                <div class="mt-3">
                    <label for="subtaskSheetDescriptionInput" class="form-label" id="subtaskSheetDescriptionLabel">Deskripsi Subtask</label>
                    <textarea class="form-control" id="subtaskSheetDescriptionInput" name="description" rows="4"
                              placeholder="Tuliskan detail tambahan (opsional)"></textarea>
                </div>
                <div class="subtask-sheet__actions mt-4">
                    <button type="button" class="btn-outline btn-sm" data-subtask-sheet-close id="subtaskSheetCancelBtn">
                        Batal
                    </button>
                    <button type="submit" class="btn-modern btn-sm" id="subtaskSheetSubmitBtn">
                        <i class="bi bi-save me-1"></i> Simpan Subtask
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(() => {
    if (window.SubtaskSheet) {
        return;
    }

    const sheet = document.getElementById('subtaskSheet');
    if (!sheet) {
        return;
    }

    const form = document.getElementById('subtaskSheetForm');
    const titleEl = document.getElementById('subtaskSheetTitle');
    const subtitleEl = document.getElementById('subtaskSheetSubtitle');
    const titleLabel = document.getElementById('subtaskSheetTitleLabel');
    const estimateLabel = document.getElementById('subtaskSheetEstimateLabel');
    const descriptionLabel = document.getElementById('subtaskSheetDescriptionLabel');
    const titleInput = document.getElementById('subtaskSheetTitleInput');
    const estimateInput = document.getElementById('subtaskSheetEstimateInput');
    const descriptionInput = document.getElementById('subtaskSheetDescriptionInput');
    const submitBtn = document.getElementById('subtaskSheetSubmitBtn');
    const cancelBtn = document.getElementById('subtaskSheetCancelBtn');
    const closeTriggers = sheet.querySelectorAll('[data-subtask-sheet-close]');

    let previousFocus = null;
    let defaultTexts = {
        heading: 'Tambah Subtask',
        subtitle: '',
        titleLabel: 'Judul Subtask',
        titlePlaceholder: 'Masukkan judul subtask',
        estimateLabel: 'Estimasi Waktu (Jam)',
        estimatePlaceholder: 'Contoh: 2.5',
        descriptionLabel: 'Deskripsi Subtask',
        descriptionPlaceholder: 'Tuliskan detail tambahan (opsional)',
        submitLabel: 'Simpan Subtask',
        loadingLabel: 'Menyimpan...'
    };

    const openSheet = (options = {}) => {
        previousFocus = document.activeElement;

        const texts = { ...defaultTexts, ...options.texts };
        titleEl.textContent = texts.heading;
        subtitleEl.textContent = texts.subtitle || '';
        subtitleEl.classList.toggle('d-none', !texts.subtitle);

        titleLabel.textContent = texts.titleLabel;
        titleInput.placeholder = texts.titlePlaceholder;
        estimateLabel.textContent = texts.estimateLabel;
        estimateInput.placeholder = texts.estimatePlaceholder;
        descriptionLabel.textContent = texts.descriptionLabel;
        descriptionInput.placeholder = texts.descriptionPlaceholder;
        submitBtn.innerHTML = `<i class="bi bi-save me-1"></i> ${texts.submitLabel}`;
        submitBtn.dataset.loadingLabel = texts.loadingLabel;
        submitBtn.dataset.idleLabel = texts.submitLabel;

        form.action = options.action || '#';

        form.reset();
        sheet.classList.remove('hidden');
        sheet.setAttribute('aria-hidden', 'false');
        document.body.classList.add('subtask-sheet-open');

        requestAnimationFrame(() => {
            titleInput.focus({ preventScroll: false });
        });
    };

    const closeSheet = () => {
        sheet.classList.add('hidden');
        sheet.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('subtask-sheet-open');

        submitBtn.disabled = false;
        submitBtn.innerHTML = `<i class="bi bi-save me-1"></i> ${submitBtn.dataset.idleLabel || defaultTexts.submitLabel}`;

        if (previousFocus && typeof previousFocus.focus === 'function') {
            previousFocus.focus({ preventScroll: false });
        }
    };

    const withLoading = async (callback) => {
        const original = submitBtn.innerHTML;
        const loadingText = submitBtn.dataset.loadingLabel || defaultTexts.loadingLabel;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<span class="loading-spinner"></span>${loadingText}`;

        try {
            await callback();
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = original;
        }
    };

    form.addEventListener('submit', event => {
        event.preventDefault();
        submitBtn.dataset.idleLabel = submitBtn.textContent.trim();
        withLoading(() => {
            form.submit();
        });
    });

    closeTriggers.forEach(trigger => {
        trigger.addEventListener('click', () => {
            closeSheet();
        });
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && !sheet.classList.contains('hidden')) {
            closeSheet();
        }
    });

    document.addEventListener('click', event => {
        const trigger = event.target.closest('[data-subtask-sheet-trigger]');
        if (!trigger) {
            return;
        }

        event.preventDefault();

        const action = trigger.dataset.subtaskUrl || '#';
        const heading = trigger.dataset.subtaskHeading || defaultTexts.heading;
        const subtitle = trigger.dataset.subtaskSubtitle || '';
        const titleLabelText = trigger.dataset.subtaskTitleLabel || defaultTexts.titleLabel;
        const titlePlaceholderText = trigger.dataset.subtaskTitlePlaceholder || defaultTexts.titlePlaceholder;
        const estimateLabelText = trigger.dataset.subtaskEstimateLabel || defaultTexts.estimateLabel;
        const estimatePlaceholderText = trigger.dataset.subtaskEstimatePlaceholder || defaultTexts.estimatePlaceholder;
        const descriptionLabelText = trigger.dataset.subtaskDescriptionLabel || defaultTexts.descriptionLabel;
        const descriptionPlaceholderText = trigger.dataset.subtaskDescriptionPlaceholder || defaultTexts.descriptionPlaceholder;
        const submitLabelText = trigger.dataset.subtaskSubmitLabel || defaultTexts.submitLabel;
        const loadingLabelText = trigger.dataset.subtaskLoadingLabel || defaultTexts.loadingLabel;

        openSheet({
            action,
            texts: {
                heading,
                subtitle,
                titleLabel: titleLabelText,
                titlePlaceholder: titlePlaceholderText,
                estimateLabel: estimateLabelText,
                estimatePlaceholder: estimatePlaceholderText,
                descriptionLabel: descriptionLabelText,
                descriptionPlaceholder: descriptionPlaceholderText,
                submitLabel: submitLabelText,
                loadingLabel: loadingLabelText,
            }
        });
    });

    window.SubtaskSheet = {
        open: openSheet,
        close: closeSheet,
    };
})();
</script>
@endonce
