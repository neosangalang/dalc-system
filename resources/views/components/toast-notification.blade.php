<!-- Global Toast Notification Component (Tailwind + Alpine.js) -->
<div x-data="{
    toasts: [],
    init() {
        if (window._pendingToasts && window._pendingToasts.length) {
            window._pendingToasts.forEach(t => this.add(t));
            window._pendingToasts = [];
        }
    },
    add(toast) {
        const id = Date.now() + Math.random();
        const type = toast.type || 'success';
        const defaultDurations = {
            success: 3500,
            info: 4000,
            warning: 4500,
            purple: 4000,
            error: 5500
        };
        const duration = toast.duration || defaultDurations[type] || 3500;

        let icon = toast.icon;
        if (!icon) {
            if (type === 'error') icon = 'fa-circle-exclamation';
            else if (type === 'warning') icon = 'fa-triangle-exclamation';
            else if (type === 'purple') icon = 'fa-shield-halved';
            else if (type === 'info') icon = 'fa-circle-info';
            else icon = 'fa-check-circle';
        }

        let title = toast.title;
        if (!title && title !== false) {
            if (type === 'success') title = 'Confirmation';
            else if (type === 'error') title = 'Error';
            else if (type === 'warning') title = 'Action Notice';
            else if (type === 'purple') title = 'Security';
            else if (type === 'info') title = 'Notice';
        }

        const item = {
            id: id,
            title: title,
            message: toast.message || 'Action completed successfully.',
            type: type,
            icon: icon,
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
                     'bg-amber-50 text-amber-600 border border-amber-200': t.type === 'warning',
                     'bg-rose-50 text-rose-600 border border-rose-200': t.type === 'error'
                 }">
                <i class="fa text-sm" :class="t.icon"></i>
            </div>

            <!-- Toast Body -->
            <div class="flex-1 min-w-0 pr-1">
                <template x-if="t.title">
                    <span class="block text-[10px] font-black uppercase tracking-wider mb-0.5"
                          :class="{
                              'text-emerald-700': t.type === 'success',
                              'text-purple-700': t.type === 'purple',
                              'text-blue-700': t.type === 'info',
                              'text-amber-700': t.type === 'warning',
                              'text-rose-700': t.type === 'error'
                          }"
                          x-text="t.title"></span>
                </template>
                <span class="block text-xs sm:text-[13px] font-bold text-[#1E2657] leading-snug break-words" x-text="t.message"></span>
            </div>

            <!-- Optional Instant Close Button -->
            <button type="button"
                    @click="remove(t.id)"
                    class="text-[#9CA0AF] hover:text-[#1E2657] transition-colors p-1 rounded-md hover:bg-slate-100 cursor-pointer shrink-0"
                    title="Dismiss immediately">
                <i class="fa fa-times text-xs"></i>
            </button>

            <!-- Dynamic Countdown Bar (Removes toast without manual interaction) -->
            <div class="absolute bottom-0 left-0 right-0 h-1 bg-slate-100 overflow-hidden">
                <div class="h-full transition-all duration-75 ease-linear"
                     :class="{
                         'bg-[#10B981]': t.type === 'success',
                         'bg-[#8B5CF6]': t.type === 'purple',
                         'bg-[#3B82F6]': t.type === 'info',
                         'bg-amber-500': t.type === 'warning',
                         'bg-rose-500': t.type === 'error'
                     }"
                     :style="'width: ' + t.progress + '%'"></div>
            </div>
        </div>
    </template>

</div>

<script>
    // Global Toast Notification Helper
    window.showToast = function(message, type = 'success', duration = null, title = null) {
        if (!type) type = 'success';
        
        const payload = {
            message: message,
            type: type,
            duration: duration,
            title: title
        };

        window.dispatchEvent(new CustomEvent('show-toast', { detail: payload }));
        
        // Also queue in case Alpine is still initializing
        if (!window.Alpine) {
            window._pendingToasts = window._pendingToasts || [];
            window._pendingToasts.push(payload);
        }
    };

    // Polyfill/Override native alert to be non-blocking & dynamic auto-dismissing
    window.alert = function(message) {
        const msg = String(message ?? '');
        const lower = msg.toLowerCase();
        let type = 'info';
        let title = 'Notice';

        if (lower.includes('error') || lower.includes('failed') || lower.includes('wrong') || lower.includes('could not')) {
            type = 'error';
            title = 'Error';
        } else if (lower.includes('please') || lower.includes('must') || lower.includes('required') || lower.includes('invalid') || lower.includes('too large') || lower.includes('select') || lower.includes('fill') || lower.includes('type')) {
            type = 'warning';
            title = 'Action Notice';
        } else if (lower.includes('success') || lower.includes('copied') || lower.includes('saved') || lower.includes('done') || lower.includes('updated') || lower.includes('complete')) {
            type = 'success';
            title = 'Confirmation';
        }

        window.showToast(msg, type, null, title);
    };
</script>
