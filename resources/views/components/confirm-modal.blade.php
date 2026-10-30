<!-- Dynamic Confirmation Modal Component (Tailwind + Alpine.js) -->
<div x-data="{
    open: false,
    title: 'Confirm Action',
    message: 'Are you sure you want to proceed?',
    confirmText: 'Confirm',
    cancelText: 'Cancel',
    confirmColor: 'purple',
    icon: 'fa-shield-halved',
    loading: false,
    callback: null,

    show(opts) {
        this.title = opts.title || 'Confirm Action';
        this.message = opts.message || 'Are you sure you want to proceed?';
        this.confirmText = opts.confirmText || 'Confirm';
        this.cancelText = opts.cancelText || 'Cancel';
        this.confirmColor = opts.confirmColor || 'purple';
        this.icon = opts.icon || 'fa-shield-halved';
        this.callback = typeof opts.onConfirm === 'function' ? opts.onConfirm : null;
        this.loading = false;
        this.open = true;
    },

    close() {
        if (!this.loading) {
            this.open = false;
        }
    },

    async confirm() {
        if (this.loading) return;
        if (this.callback) {
            this.loading = true;
            try {
                await this.callback();
                this.open = false;
            } catch (err) {
                console.error('Confirmation action error:', err);
            } finally {
                this.loading = false;
            }
        } else {
            this.open = false;
        }
    }
}"
@open-confirm-modal.window="show($event.detail)"
@keydown.escape.window="close()"
x-cloak
x-show="open"
class="fixed inset-0 z-[9999] overflow-y-auto"
aria-labelledby="confirm-modal-title"
role="dialog"
aria-modal="true">

    <!-- Modal Flexbox Container -->
    <div class="flex min-h-full items-end justify-center p-3 sm:items-center sm:p-4 text-center">

        <!-- Backdrop with Blur -->
        <div x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"
             @click="close()"></div>

        <!-- Modal Dialog Card -->
        <div x-show="open"
             x-transition:enter="ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all border border-black/15 z-10">

            <!-- Top Close Button -->
            <div class="absolute top-4 right-4 z-10">
                <button type="button"
                        @click="close()"
                        :disabled="loading"
                        class="text-[#9CA0AF] hover:text-[#1E2657] transition-colors p-1.5 rounded-lg hover:bg-slate-100 cursor-pointer disabled:opacity-50">
                    <i class="fa fa-times text-lg"></i>
                </button>
            </div>

            <!-- Content Area -->
            <div class="p-5 sm:p-6">
                <!-- Icon Header -->
                <div class="flex items-center gap-3.5 mb-4">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-sm"
                         :class="{
                             'bg-[#8B5CF6]/10 text-[#8B5CF6] border border-[#8B5CF6]/20': confirmColor === 'purple',
                             'bg-[#3B82F6]/10 text-[#3B82F6] border border-[#3B82F6]/20': confirmColor === 'blue',
                             'bg-[#10B981]/10 text-[#10B981] border border-[#10B981]/20': confirmColor === 'emerald',
                             'bg-amber-50 text-amber-600 border border-amber-200': confirmColor === 'amber',
                             'bg-rose-50 text-rose-600 border border-rose-200': confirmColor === 'rose'
                         }">
                        <i class="fa text-xl" :class="icon"></i>
                    </div>
                    <div>
                        <h4 id="confirm-modal-title" class="text-base sm:text-lg font-extrabold text-[#1E2657] m-0 leading-tight" x-text="title"></h4>
                        <span class="text-[11px] font-bold text-[#9CA0AF] uppercase tracking-wider">Confirmation Required</span>
                    </div>
                </div>

                <!-- Message Body -->
                <div class="text-[13px] text-[#6B7086] leading-relaxed mb-6 font-medium" x-html="message"></div>

                <!-- Action Buttons -->
                <div class="flex flex-col-reverse sm:flex-row items-center justify-end gap-2.5 pt-2 border-t border-black/10">
                    <button type="button"
                            @click="close()"
                            :disabled="loading"
                            class="w-full sm:w-auto px-4 py-2.5 bg-white border border-black/15 text-[#6B7086] text-xs sm:text-[13px] font-extrabold rounded-xl hover:bg-slate-50 transition-colors shadow-sm disabled:opacity-50 cursor-pointer"
                            x-text="cancelText">
                    </button>
                    <button type="button"
                            @click="confirm()"
                            :disabled="loading"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 text-white text-xs sm:text-[13px] font-extrabold rounded-xl transition-all shadow-sm disabled:opacity-70 cursor-pointer active:scale-95"
                            :class="{
                                'bg-[#8B5CF6] hover:bg-purple-700': confirmColor === 'purple',
                                'bg-[#3B82F6] hover:bg-blue-600': confirmColor === 'blue',
                                'bg-[#10B981] hover:bg-emerald-600': confirmColor === 'emerald',
                                'bg-amber-600 hover:bg-amber-700': confirmColor === 'amber',
                                'bg-rose-600 hover:bg-rose-700': confirmColor === 'rose'
                            }">
                        <span x-show="loading" class="inline-flex items-center"><i class="fa fa-spinner fa-spin mr-2"></i> Processing...</span>
                        <span x-show="!loading" class="inline-flex items-center">
                            <i class="fa mr-1.5" :class="icon"></i>
                            <span x-text="confirmText"></span>
                        </span>
                    </button>
                </div>
            </div>

        </div>

    </div>
</div>

<script>
    // Global helper accessible anywhere: window.confirmAction({ ... })
    if (typeof window.confirmAction !== 'function') {
        window.confirmAction = function(options) {
            window.dispatchEvent(new CustomEvent('open-confirm-modal', { detail: options }));
        };
    }
</script>

