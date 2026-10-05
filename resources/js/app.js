// 1. Mobile sidebar: open and close
const sidebar = document.getElementById('sidebar');
const backdrop = document.getElementById('sidebar-backdrop');

function setSidebar(open) {
    if (!sidebar) return;
    sidebar.classList.toggle('-translate-x-full', !open);
    backdrop.classList.toggle('hidden', !open);
}

document.querySelectorAll('[data-sidebar-open]').forEach((el) =>
    el.addEventListener('click', () => setSidebar(true))
);
document.querySelectorAll('[data-sidebar-close]').forEach((el) =>
    el.addEventListener('click', () => setSidebar(false))
);

// 2. Confirm dialog. Any form with data-confirm asks first.
const dialog = document.getElementById('confirm-dialog');

if (dialog) {
    let pendingForm = null;

    document.addEventListener('submit', (event) => {
        const form = event.target;
        if (!form.dataset.confirm || form.dataset.confirmed) return;

        event.preventDefault();
        pendingForm = form;
        dialog.querySelector('[data-confirm-title]').textContent = form.dataset.confirmTitle || 'Please confirm';
        dialog.querySelector('[data-confirm-message]').textContent = form.dataset.confirm;
        dialog.querySelector('[data-confirm-yes]').textContent = form.dataset.confirmButton || 'Yes, continue';
        dialog.showModal();
    });

    dialog.querySelector('[data-confirm-yes]').addEventListener('click', () => {
        if (!pendingForm) return;
        pendingForm.dataset.confirmed = '1';
        dialog.close();
        pendingForm.requestSubmit();
    });

    dialog.querySelector('[data-confirm-no]').addEventListener('click', () => {
        pendingForm = null;
        dialog.close();
    });
}

// 3. Loading state. Submit button with data-loading-text locks and changes label.
document.addEventListener('submit', (event) => {
    if (event.defaultPrevented) return;
    const button = event.target.querySelector('button[type="submit"][data-loading-text]');
    if (button) {
        button.disabled = true;
        button.textContent = button.dataset.loadingText;
    }
});