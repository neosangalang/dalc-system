@props(['status'])

@if ($status)
    <div x-data="{ 
            show: true, 
            duration: 4500, 
            remaining: 4500, 
            progress: 100, 
            paused: false, 
            interval: null,
            init() {
                const step = 25;
                this.interval = setInterval(() => {
                    if (!this.paused) {
                        this.remaining -= step;
                        this.progress = Math.max(0, (this.remaining / this.duration) * 100);
                        if (this.remaining <= 0) {
                            if (this.interval) clearInterval(this.interval);
                            this.show = false;
                        }
                    }
                }, step);
            }
        }"
        x-show="show"
        x-cloak
        @mouseenter="paused = true"
        @mouseleave="paused = false"
        x-transition:leave="transition-all ease-in-out duration-300"
        x-transition:leave-start="opacity-100 max-h-32 mb-4 py-2.5"
        x-transition:leave-end="opacity-0 max-h-0 mb-0 py-0 overflow-hidden border-transparent"
        {{ $attributes->merge(['class' => 'relative overflow-hidden bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm py-2.5 px-3.5 rounded-xl mb-4 font-bold flex items-center justify-between gap-2 shadow-sm']) }}>
        <div class="flex items-center gap-2">
            <i class="fa-solid fa-circle-check text-emerald-500 text-base shrink-0"></i>
            <span>{{ $status }}</span>
        </div>
        <button type="button" @click="if (interval) clearInterval(interval); show = false" class="text-emerald-500 hover:text-emerald-700 p-0.5 cursor-pointer shrink-0" title="Dismiss">
            <i class="fa fa-times text-xs"></i>
        </button>
        <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-emerald-200 overflow-hidden">
            <div class="h-full bg-emerald-500 transition-all duration-75 ease-linear" :style="'width: ' + progress + '%'"></div>
        </div>
    </div>
@endif
