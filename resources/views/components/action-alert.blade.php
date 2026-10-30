{{-- 
    Dynamic Action Alert Component
    Auto-dismisses confirmations, errors, mistakes, warnings, and status notifications without manual interaction.
    Features:
    - Visual countdown progress bar
    - Pause on hover (when reading)
    - Resume on mouse leave
    - Instant manual dismiss button
    - Smooth slide & collapse exit animation
    - Zero height/DOM impact when all alerts dismiss
--}}
@php
    $hasSuccess = session()->has('success');
    $hasStatus  = session()->has('status');
    $hasError   = session()->has('error');
    $hasWarning = session()->has('warning');
    $hasInfo    = session()->has('info');
    $hasErrors  = $errors->any() && !($ignoreErrors ?? false);
    $totalAlertsCount = ($hasSuccess ? 1 : 0) + ($hasStatus && !$hasSuccess ? 1 : 0) + ($hasError ? 1 : 0) + ($hasWarning ? 1 : 0) + ($hasInfo ? 1 : 0) + ($hasErrors ? 1 : 0);
@endphp

@if($totalAlertsCount > 0)
<div x-data="{
        remainingCount: {{ $totalAlertsCount }},
        dismissOne() {
            this.remainingCount = Math.max(0, this.remainingCount - 1);
        }
    }"
    @alert-dismissed="dismissOne()"
    x-show="remainingCount > 0"
    x-transition:leave="transition-all ease-in-out duration-300"
    x-transition:leave-start="opacity-100 max-h-[800px] mb-6"
    x-transition:leave-end="opacity-0 max-h-0 mb-0 overflow-hidden"
    class="space-y-3 mb-6 w-full"
    id="dynamic-action-alerts-container">

    {{-- 1. SUCCESS / CONFIRMATION ALERT --}}
    @if($hasSuccess)
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
                            this.dismiss();
                        }
                    }
                }, step);
            },
            pause() { this.paused = true; },
            resume() { this.paused = false; },
            dismiss() {
                if (this.interval) clearInterval(this.interval);
                this.show = false;
                this.$dispatch('alert-dismissed');
            }
        }"
        x-show="show"
        x-cloak
        @mouseenter="pause()"
        @mouseleave="resume()"
        x-transition:enter="transition-all ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition-all ease-in-out duration-300"
        x-transition:leave-start="opacity-100 max-h-40 translate-y-0 mb-3 py-4"
        x-transition:leave-end="opacity-0 max-h-0 -translate-y-2 mb-0 py-0 overflow-hidden border-transparent"
        class="relative overflow-hidden bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm flex flex-col justify-center"
        role="alert">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <i class="fa fa-check-circle text-emerald-500 text-lg shrink-0"></i>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 block mb-0.5">Confirmation</span>
                    <span class="font-extrabold text-sm leading-snug">{{ session('success') }}</span>
                </div>
            </div>
            <button @click="dismiss()" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors p-1 rounded-lg hover:bg-emerald-100/50 cursor-pointer ml-4 shrink-0" title="Dismiss immediately">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
        <!-- Dynamic Countdown Progress Bar -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-200/50 overflow-hidden">
            <div class="h-full bg-emerald-500 transition-all duration-75 ease-linear" :style="'width: ' + progress + '%'"></div>
        </div>
    </div>
    @endif

    {{-- 2. STATUS ALERT (e.g. Auth status, password reset, profile updated) --}}
    @if($hasStatus && !$hasSuccess)
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
                            this.dismiss();
                        }
                    }
                }, step);
            },
            pause() { this.paused = true; },
            resume() { this.paused = false; },
            dismiss() {
                if (this.interval) clearInterval(this.interval);
                this.show = false;
                this.$dispatch('alert-dismissed');
            }
        }"
        x-show="show"
        x-cloak
        @mouseenter="pause()"
        @mouseleave="resume()"
        x-transition:enter="transition-all ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition-all ease-in-out duration-300"
        x-transition:leave-start="opacity-100 max-h-40 translate-y-0 mb-3 py-4"
        x-transition:leave-end="opacity-0 max-h-0 -translate-y-2 mb-0 py-0 overflow-hidden border-transparent"
        class="relative overflow-hidden bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm flex flex-col justify-center"
        role="alert">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <i class="fa fa-check-circle text-emerald-500 text-lg shrink-0"></i>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-emerald-700 block mb-0.5">Status Update</span>
                    <span class="font-extrabold text-sm leading-snug">{{ session('status') }}</span>
                </div>
            </div>
            <button @click="dismiss()" type="button" class="text-emerald-500 hover:text-emerald-700 transition-colors p-1 rounded-lg hover:bg-emerald-100/50 cursor-pointer ml-4 shrink-0" title="Dismiss immediately">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
        <!-- Dynamic Countdown Progress Bar -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-emerald-200/50 overflow-hidden">
            <div class="h-full bg-emerald-500 transition-all duration-75 ease-linear" :style="'width: ' + progress + '%'"></div>
        </div>
    </div>
    @endif

    {{-- 3. ERROR ALERT --}}
    @if($hasError)
    <div x-data="{
            show: true,
            duration: 6000,
            remaining: 6000,
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
                            this.dismiss();
                        }
                    }
                }, step);
            },
            pause() { this.paused = true; },
            resume() { this.paused = false; },
            dismiss() {
                if (this.interval) clearInterval(this.interval);
                this.show = false;
                this.$dispatch('alert-dismissed');
            }
        }"
        x-show="show"
        x-cloak
        @mouseenter="pause()"
        @mouseleave="resume()"
        x-transition:enter="transition-all ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition-all ease-in-out duration-300"
        x-transition:leave-start="opacity-100 max-h-40 translate-y-0 mb-3 py-4"
        x-transition:leave-end="opacity-0 max-h-0 -translate-y-2 mb-0 py-0 overflow-hidden border-transparent"
        class="relative overflow-hidden bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-sm flex flex-col justify-center"
        role="alert">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <i class="fa fa-circle-exclamation text-rose-500 text-lg shrink-0"></i>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-rose-700 block mb-0.5">Action Error</span>
                    <span class="font-extrabold text-sm leading-snug">{{ session('error') }}</span>
                </div>
            </div>
            <button @click="dismiss()" type="button" class="text-rose-500 hover:text-rose-700 transition-colors p-1 rounded-lg hover:bg-rose-100/50 cursor-pointer ml-4 shrink-0" title="Dismiss immediately">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
        <!-- Dynamic Countdown Progress Bar -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-rose-200/50 overflow-hidden">
            <div class="h-full bg-rose-500 transition-all duration-75 ease-linear" :style="'width: ' + progress + '%'"></div>
        </div>
    </div>
    @endif

    {{-- 4. WARNING ALERT --}}
    @if($hasWarning)
    <div x-data="{
            show: true,
            duration: 5500,
            remaining: 5500,
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
                            this.dismiss();
                        }
                    }
                }, step);
            },
            pause() { this.paused = true; },
            resume() { this.paused = false; },
            dismiss() {
                if (this.interval) clearInterval(this.interval);
                this.show = false;
                this.$dispatch('alert-dismissed');
            }
        }"
        x-show="show"
        x-cloak
        @mouseenter="pause()"
        @mouseleave="resume()"
        x-transition:enter="transition-all ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition-all ease-in-out duration-300"
        x-transition:leave-start="opacity-100 max-h-40 translate-y-0 mb-3 py-4"
        x-transition:leave-end="opacity-0 max-h-0 -translate-y-2 mb-0 py-0 overflow-hidden border-transparent"
        class="relative overflow-hidden bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-2xl shadow-sm flex flex-col justify-center"
        role="alert">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <i class="fa fa-triangle-exclamation text-amber-500 text-lg shrink-0"></i>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-amber-700 block mb-0.5">Warning</span>
                    <span class="font-extrabold text-sm leading-snug">{{ session('warning') }}</span>
                </div>
            </div>
            <button @click="dismiss()" type="button" class="text-amber-500 hover:text-amber-700 transition-colors p-1 rounded-lg hover:bg-amber-100/50 cursor-pointer ml-4 shrink-0" title="Dismiss immediately">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
        <!-- Dynamic Countdown Progress Bar -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-amber-200/50 overflow-hidden">
            <div class="h-full bg-amber-500 transition-all duration-75 ease-linear" :style="'width: ' + progress + '%'"></div>
        </div>
    </div>
    @endif

    {{-- 5. INFO ALERT --}}
    @if($hasInfo)
    <div x-data="{
            show: true,
            duration: 5000,
            remaining: 5000,
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
                            this.dismiss();
                        }
                    }
                }, step);
            },
            pause() { this.paused = true; },
            resume() { this.paused = false; },
            dismiss() {
                if (this.interval) clearInterval(this.interval);
                this.show = false;
                this.$dispatch('alert-dismissed');
            }
        }"
        x-show="show"
        x-cloak
        @mouseenter="pause()"
        @mouseleave="resume()"
        x-transition:enter="transition-all ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition-all ease-in-out duration-300"
        x-transition:leave-start="opacity-100 max-h-40 translate-y-0 mb-3 py-4"
        x-transition:leave-end="opacity-0 max-h-0 -translate-y-2 mb-0 py-0 overflow-hidden border-transparent"
        class="relative overflow-hidden bg-blue-50 border border-blue-200 text-[#1E2657] px-5 py-4 rounded-2xl shadow-sm flex flex-col justify-center"
        role="alert">
        <div class="flex items-center justify-between w-full">
            <div class="flex items-center gap-3">
                <i class="fa fa-info-circle text-[#3B82F6] text-lg shrink-0"></i>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-blue-700 block mb-0.5">Information</span>
                    <span class="font-extrabold text-sm leading-snug">{{ session('info') }}</span>
                </div>
            </div>
            <button @click="dismiss()" type="button" class="text-[#3B82F6] hover:text-[#1E2657] transition-colors p-1 rounded-lg hover:bg-blue-100/50 cursor-pointer ml-4 shrink-0" title="Dismiss immediately">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
        <!-- Dynamic Countdown Progress Bar -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-blue-200/50 overflow-hidden">
            <div class="h-full bg-blue-500 transition-all duration-75 ease-linear" :style="'width: ' + progress + '%'"></div>
        </div>
    </div>
    @endif

    {{-- 6. MISTAKES / VALIDATION ERRORS LIST --}}
    @if($hasErrors)
    <div x-data="{
            show: true,
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
                            this.dismiss();
                        }
                    }
                }, step);
            },
            pause() { this.paused = true; },
            resume() { this.paused = false; },
            dismiss() {
                if (this.interval) clearInterval(this.interval);
                this.show = false;
                this.$dispatch('alert-dismissed');
            }
        }"
        x-show="show"
        x-cloak
        @mouseenter="pause()"
        @mouseleave="resume()"
        x-transition:enter="transition-all ease-out duration-300"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-98"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition-all ease-in-out duration-300"
        x-transition:leave-start="opacity-100 max-h-96 translate-y-0 mb-3 py-4"
        x-transition:leave-end="opacity-0 max-h-0 -translate-y-2 mb-0 py-0 overflow-hidden border-transparent"
        class="relative overflow-hidden bg-rose-50 border border-rose-200 text-rose-800 px-5 py-4 rounded-2xl shadow-sm flex flex-col justify-center"
        role="alert">
        <div class="flex items-start justify-between w-full">
            <div class="flex items-start gap-3">
                <i class="fa fa-triangle-exclamation text-rose-500 text-lg mt-0.5 shrink-0"></i>
                <div>
                    <span class="text-[10px] font-black uppercase tracking-wider text-rose-700 block mb-0.5">Action Notice</span>
                    <span class="font-extrabold text-sm block leading-snug">Please fix the following mistakes:</span>
                    <ul class="list-disc pl-5 text-sm space-y-1 font-medium mt-1.5 m-0 text-rose-700">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button @click="dismiss()" type="button" class="text-rose-500 hover:text-rose-700 transition-colors p-1 rounded-lg hover:bg-rose-100/50 cursor-pointer ml-4 shrink-0" title="Dismiss immediately">
                <i class="fa fa-times text-lg"></i>
            </button>
        </div>
        <!-- Dynamic Countdown Progress Bar -->
        <div class="absolute bottom-0 left-0 right-0 h-1 bg-rose-200/50 overflow-hidden">
            <div class="h-full bg-rose-500 transition-all duration-75 ease-linear" :style="'width: ' + progress + '%'"></div>
        </div>
    </div>
    @endif

</div>
@endif
