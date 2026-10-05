{{-- One dialog for the whole page. Filled by app.js when a form has data-confirm. --}}
<dialog id="confirm-dialog" class="m-auto w-full max-w-sm rounded-md border border-line bg-white p-0 text-ink">
    <div class="p-5">
        <h2 class="text-base font-semibold" data-confirm-title>Please confirm</h2>
        <p class="mt-2 text-sm text-muted" data-confirm-message></p>
    </div>
    <div class="flex justify-end gap-2 border-t border-line bg-stone-50 px-5 py-3">
        <button type="button" class="btn btn-secondary" data-confirm-no>Keep it</button>
        <button type="button" class="btn btn-primary" data-confirm-yes>Yes, continue</button>
    </div>
</dialog>