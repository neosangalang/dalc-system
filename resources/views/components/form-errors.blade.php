@if ($errors->any())
<div x-data="{ 
        showErr: true, 
        duration: 7500, 
        remaining: 7500, 
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
                        this.showErr = false;
                    }
                }
            }, step);
        }
    }"
    x-show="showErr"
    x-cloak
    @mouseenter="paused = true"
    @mouseleave="paused = false"
    x-transition:leave="transition-all ease-in-out duration-300"
    x-transition:leave-start="opacity-100 max-h-60 mb-4 py-3"
    x-transition:leave-end="opacity-0 max-h-0 mb-0 py-0 overflow-hidden border-transparent"
    class="relative overflow-hidden bg-rose-50 border border-rose-200 text-rose-800 px-4 py-3 rounded-xl shadow-sm mb-4">
    <div class="flex items-start justify-between">
        <div>
            <div class="flex items-center mb-1 font-extrabold text-[13px]">
                <i class="fa fa-exclamation-triangle mr-2 text-rose-500"></i> Please fix the following errors:
            </div>
            <ul class="list-disc pl-5 text-[12px] font-medium m-0 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        <button type="button" @click="if (interval) clearInterval(interval); showErr = false" class="text-rose-400 hover:text-rose-600 p-1 cursor-pointer shrink-0" title="Dismiss">
            <i class="fa fa-times text-xs"></i>
        </button>
    </div>
    <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-rose-200 overflow-hidden">
        <div class="h-full bg-rose-500 transition-all duration-75 ease-linear" :style="'width: ' + progress + '%'"></div>
    </div>
</div>
@endif

