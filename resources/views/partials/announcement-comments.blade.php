<!-- FACEBOOK-STYLE COMMENTS SECTION -->
<div class="mt-4 pt-3 border-t border-black/5" x-data="{ openComments: true }">
    <!-- Action Bar / Comment Counter -->
    <div class="flex items-center justify-between text-[12px] font-bold text-[#6B7086] px-1 py-1 mb-3">
        <div class="flex items-center gap-1.5 text-[#1E2657]">
            <span class="w-6 h-6 rounded-full bg-[#8B5CF6]/10 text-[#8B5CF6] flex items-center justify-center text-[10px]">
                <i class="fa-solid fa-comments"></i>
            </span>
            <span>{{ $announcement->comments->count() }} {{ \Illuminate\Support\Str::plural('Comment', $announcement->comments->count()) }}</span>
        </div>
        <button type="button" @click="openComments = !openComments" class="text-[#8B5CF6] hover:underline flex items-center gap-1">
            <i class="fa-regular fa-message text-[11px]"></i>
            <span x-text="openComments ? 'Hide Comments' : 'View Comments'">View Comments</span>
        </button>
    </div>

    <div x-show="openComments" class="space-y-3">
        <!-- List of Existing Comments -->
        @if($announcement->comments->count() > 0)
            <div class="space-y-2.5 max-h-[350px] overflow-y-auto pr-1">
                @foreach($announcement->comments as $comment)
                    <div class="flex items-start gap-2.5">
                        <!-- User Avatar Initials -->
                        @php
                            $user = $comment->user;
                            $roleBadgeColor = match($user->role ?? 'guardian') {
                                'admin'   => 'bg-purple-100 text-purple-700 border-purple-200',
                                'teacher' => 'bg-blue-100 text-blue-700 border-blue-200',
                                default   => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                            };
                            $roleLabel = match($user->role ?? 'guardian') {
                                'admin'   => 'Admin',
                                'teacher' => 'Teacher',
                                default   => 'Parent/Guardian',
                            };
                            $nameParts = explode(' ', $user->name ?? 'User');
                            $initials = strtoupper(substr($nameParts[0] ?? 'U', 0, 1) . substr($nameParts[1] ?? '', 0, 1));
                        @endphp
                        <div class="w-8 h-8 rounded-full bg-[#1E2657] text-white flex items-center justify-center font-bold text-[11px] shrink-0 shadow-sm mt-0.5">
                            {{ $initials ?: 'U' }}
                        </div>

                        <!-- Comment Bubble -->
                        <div class="flex-1">
                            <div class="bg-[#F0F2F5] hover:bg-[#E4E6EB] transition-colors rounded-2xl rounded-tl-none px-3.5 py-2 inline-block max-w-full">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="font-extrabold text-[12px] text-[#1E2657]">{{ $user->name ?? 'User' }}</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded border {{ $roleBadgeColor }}">
                                        {{ $roleLabel }}
                                    </span>
                                </div>
                                <p class="text-[13px] font-medium text-[#1C1E21] leading-snug whitespace-pre-line m-0">{{ $comment->body }}</p>
                            </div>
                            <div class="ml-2 mt-0.5 text-[10px] font-semibold text-[#8A8D91]">
                                {{ $comment->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Add New Comment Input Box -->
        <form action="{{ route('comments.store') }}" method="POST" class="flex items-center gap-2 mt-3 pt-2">
            @csrf
            <input type="hidden" name="commentable_id" value="{{ $announcement->id }}">
            <input type="hidden" name="commentable_type" value="App\Models\Announcement">

            <!-- Current User Avatar -->
            @php
                $currentUser = auth()->user();
                $curNameParts = explode(' ', $currentUser->name ?? 'Me');
                $curInitials = strtoupper(substr($curNameParts[0] ?? 'M', 0, 1) . substr($curNameParts[1] ?? '', 0, 1));
            @endphp
            <div class="w-8 h-8 rounded-full bg-[#8B5CF6] text-white flex items-center justify-center font-bold text-[11px] shrink-0 shadow-sm">
                {{ $curInitials ?: 'M' }}
            </div>

            <!-- Pill-shaped Input Field -->
            <div class="relative flex-1">
                <input type="text" name="body" required placeholder="Write a comment..."
                    class="w-full bg-[#F0F2F5] hover:bg-[#E4E6EB] focus:bg-white text-[13px] text-[#1C1E21] font-medium rounded-full pl-4 pr-10 py-2 border border-transparent focus:border-[#8B5CF6] focus:ring-2 focus:ring-[#8B5CF6]/20 outline-none transition-all placeholder:text-[#8A8D91]">
                <button type="submit" class="absolute right-1.5 top-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-[#8B5CF6] text-white flex items-center justify-center text-[11px] hover:bg-[#7C3AED] transition-colors shadow-sm cursor-pointer">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </form>
    </div>
</div>
