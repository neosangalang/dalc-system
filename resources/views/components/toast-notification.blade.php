<!-- Global Toast Notification Component (Tailwind + Alpine.js) -->
<div x-data="{
    toasts: [],
    add(toast) {
        const id = Date.now() + Math.random();
        const duration = toast.duration || (toast.type === 'error' ? 5000 : 3500);
        const item = {
            id: id,
            message: toast.message || 'Action completed successfully.',
            type: toast.type || 'success',
            icon: toast.icon || (toast.type === 'error' ? 'fa-circle-exclamation' : (toast.type === 'purple' ? 'fa-shield-halved' : 'fa-check-circle')),
            duration: duration,
            remaining: duration,
            progress: 100,
            paused: false,
            interval: null
        };

        const step = 25;
        item.interval = setInterval(() => {
            if (!item.paused) {
                item.remaining -= step;
                item.progress = Math.max(0, (item.remaining / item.duration) * 100);
                if (item.remaining <= 0) {
                    this.remove(id);
                }
            }
        }, step);

        this.toasts.push(item);
    },
    remove(id) {
        const found = this.toasts.find(t => t.id === id);
        if (found && found.interval) clearInterval(found.interval);
        this.toasts = this.toasts.filter(t => t.id !== id);
    },
    pause(t) { t.paused = true; },
    resume(t) { t.paused = false; }
}"
@show-toast.window="add($event.detail)"
class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-[10000] flex flex-col gap-2.5 max-w-sm w-full pointer-events-none px-3 sm:px-0">

    <template x-for="t in toasts" :key="t.id">
        <div class="pointer-events-auto relative overflow-hidden flex items-start gap-3 p-4 bg-white border border-black/15 rounded-2xl shadow-2xl transition-all transform duration-300"
             @mouseenter="pause(t)"
             @mouseleave="resume(t)"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-3 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-95">

            <!-- Toast Icon -->
            <div class="w-8 h-8 rounded-xl flex items-center justify-center shrink-0 shadow-sm mt-0.5"
                 :class="{
                     'bg-[#10B981]/10 text-[#10B981] border border-[#10B981]/20': t.type === 'success',
                     'bg-[#8B5CF6]/10 text-[#8B5CF6] border border-[#8B5CF6]/20': t.type === 'purple',
                     'bg-[#3B82F6]/10 text-[#3B82F6] border border-[#3B82F6]/20': t.type === 'info',
                     'bg-rose-50 text-rose-600 border border-rose-200': t.type === 'error'
                 }">
                <i class="fa text-sm" :class="t.icon"></i>
            </div>

            <!-- Toast Body -->
            <div class="flex-1 min-w-0 pr-1">
                <span class="block text-xs sm:text-[13px] font-extrabold text-[#1E2657] leading-tight" x-text="t.message"></span>
            </div>

            <!-- Close Button -->
            <button type="button"
                    @click="remove(t.id)"
                    class="text-[#9CA0AF] hover:text-[#1E2657] transition-colors p-1 rounded-md hover:bg-slate-100 cursor-pointer"
                    title="Dismiss">
                <i class="fa fa-times text-xs"></i>
            </button>

            <!-- Dynamic Countdown Bar -->
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-100 overflow-hidden">
                <div class="h-full transition-all duration-75 ease-linear"
                     :class="{
                         'bg-[#10B981]': t.type === 'success',
                         'bg-[#8B5CF6]': t.type === 'purple',
                         'bg-[#3B82F6]': t.type === 'info',
                         'bg-rose-500': t.type === 'error'
                     }"
                     :style="'width: ' + t.progress + '%'"></div>
            </div>
        </div>
    </template>

</div>

<script>
    if (typeof window.showToast !== 'function') {
        window.showToast = function(message, type = 'success', duration = 3500) {
            window.dispatchEvent(new CustomEvent('show-toast', {
                detail: {
                    message: message,
                    type: type,
                    duration: duration,
                    icon: type === 'purple' ? 'fa-shield-halved' : (type === 'error' ? 'fa-circle-exclamation' : 'fa-check-circle')
                }
            }));
        };
    }
</script>
