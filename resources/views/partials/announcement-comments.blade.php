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
                        $canEdit = auth()->check() && auth()->id() === $comment->user_id;
                        $canDelete = auth()->check() && (auth()->id() === $comment->user_id || auth()->user()->role === 'admin');
                    @endphp
                    <div class="flex items-start gap-2.5" x-data="{ isEditing: false }">
                        <!-- User Avatar Initials -->
                        <div class="w-8 h-8 rounded-full bg-[#1E2657] text-white flex items-center justify-center font-bold text-[11px] shrink-0 shadow-sm mt-0.5">
                            {{ $initials ?: 'U' }}
                        </div>

                        <!-- Comment Bubble & Editing Form -->
                        <div class="flex-1 min-w-0">
                            <!-- Normal Display Mode -->
                            <div x-show="!isEditing">
                                <div class="bg-[#F0F2F5] hover:bg-[#E4E6EB] transition-colors rounded-2xl rounded-tl-none px-3.5 py-2 inline-block max-w-full">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="font-extrabold text-[12px] text-[#1E2657]">{{ $user->name ?? 'User' }}</span>
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded border {{ $roleBadgeColor }}">
                                            {{ $roleLabel }}
                                        </span>
                                    </div>
                                    <p class="text-[13px] font-medium text-[#1C1E21] leading-snug whitespace-pre-line m-0">{{ $comment->body }}</p>
                                </div>
                                <div class="ml-2 mt-0.5 flex items-center gap-2 text-[10px] font-semibold text-[#8A8D91]">
                                    <span>{{ $comment->created_at->diffForHumans() }}</span>
                                    @if($comment->updated_at && $comment->updated_at->gt($comment->created_at))
                                        <span class="text-[#9CA3AF] italic text-[9px]">(edited)</span>
                                    @endif
                                    @if($canEdit)
                                        <span>•</span>
                                        <button type="button" @click="isEditing = true" class="text-[#8B5CF6] hover:underline cursor-pointer font-bold">
                                            Edit
                                        </button>
                                    @endif
                                    @if($canDelete)
                                        <span>•</span>
                                        <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('Are you sure you want to delete this comment?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-500 hover:underline cursor-pointer font-bold">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            <!-- Inline Edit Mode -->
                            <div x-show="isEditing" x-cloak class="mt-0.5">
                                <form action="{{ route('comments.update', $comment->id) }}" method="POST" class="w-full">
                                    @csrf
                                    @method('PUT')
                                    <div class="relative">
                                        <input type="text" name="body" value="{{ $comment->body }}" required
                                            class="w-full bg-white text-[13px] text-[#1C1E21] font-medium rounded-2xl px-3.5 py-2 border border-[#8B5CF6] focus:ring-2 focus:ring-[#8B5CF6]/20 outline-none shadow-sm">
                                    </div>
                                    <div class="flex items-center gap-2 mt-1.5 ml-1">
                                        <button type="submit" class="px-2.5 py-1 bg-[#8B5CF6] text-white text-[11px] font-bold rounded-lg hover:bg-[#7C3AED] transition-colors shadow-sm cursor-pointer">
                                            Save
                                        </button>
                                        <button type="button" @click="isEditing = false" class="px-2 py-1 text-[11px] font-bold text-[#6B7086] hover:text-[#1E2657] transition-colors cursor-pointer">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
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

