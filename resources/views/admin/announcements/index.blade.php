@extends('layouts.app') 

@section('content')
<div class="container mx-auto mt-10 max-w-5xl">
    
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 mb-6">
        <h2 class="text-2xl font-bold text-[#1E2657]">Manage Announcements</h2>
        <button type="button" onclick="openModal('createModal')" class="bg-[#4F46E5] hover:bg-[#4338ca] text-white font-bold py-2.5 px-4 rounded-xl shadow-sm transition-colors flex items-center gap-2 text-sm w-full sm:w-auto justify-center active:scale-95">
            <i class="fa-solid fa-plus"></i> Post New Announcement
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="p-4 font-bold">Image</th>
                    <th class="p-4 font-bold">Type</th>
                    <th class="p-4 font-bold">Title</th>
                    <th class="p-4 font-bold">Date Posted</th>
                    <th class="p-4 font-bold text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $announcement)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="p-4">
                            @if($announcement->image_path)
                                <img src="{{ asset('storage/' . $announcement->image_path) }}" alt="Banner" class="w-16 h-10 object-cover rounded-md shadow-sm">
                            @else
                                <div class="w-16 h-10 bg-gray-100 rounded-md flex items-center justify-center text-gray-400 text-xs">None</div>
                            @endif
                        </td>
                        <td class="p-4 text-sm">
                            <span class="bg-[#4F46E5]/10 text-[#4F46E5] text-[11px] font-bold px-2.5 py-1 rounded-md uppercase tracking-wide">{{ $announcement->type ?? 'General' }}</span>
                        </td>
                        <td class="p-4 text-sm font-bold text-gray-800">{{ $announcement->title }}</td>
                        <td class="p-4 text-sm text-gray-500">{{ $announcement->created_at->format('M d, Y h:i A') }}</td>
                        <td class="p-4 text-center">
                            <div class="flex justify-center gap-2">
                                <button type="button" onclick="openModal('commentsModal-{{ $announcement->id }}')" class="text-purple-600 hover:text-purple-800 font-medium text-sm bg-purple-50 px-3 py-1 rounded-md transition-colors flex items-center gap-1">
                                    <i class="fa-solid fa-comments text-xs"></i> {{ $announcement->comments->count() }} Comments
                                </button>

                                <button type="button" onclick="openModal('editModal-{{ $announcement->id }}')" class="text-blue-500 hover:text-blue-700 font-medium text-sm bg-blue-50 px-3 py-1 rounded-md transition-colors">
                                    Edit
                                </button>
                                
                                <form action="{{ route('admin.announcements.destroy', $announcement->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-sm bg-red-50 px-3 py-1 rounded-md transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400 font-medium">No announcements found. Click the button above to create one!</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- CREATE ANNOUNCEMENT MODAL -->
<!-- ========================================== -->
<div id="createModal" class="fixed inset-0 z-50 hidden bg-gray-900/40 backdrop-blur-sm overflow-y-auto h-full w-full flex items-center justify-center">
    <div class="relative w-full max-w-2xl bg-white rounded-xl shadow-2xl p-8 m-4">
        
        <button type="button" onclick="closeModal('createModal')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>

        <div class="flex items-center gap-2 border-b pb-4 mb-6">
            <i class="fa-solid fa-bullhorn text-[#4F46E5] text-xl"></i>
            <h2 class="text-xl font-bold text-gray-800">Post a New Announcement</h2>
        </div>

        <form action="{{ route('admin.announcements.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="type" class="block text-sm font-bold text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                    <select name="type" id="type" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F46E5] bg-white" required>
                        <option value="Center Update">Center Update</option>
                        <option value="Class Suspension">Class Suspension</option>
                        <option value="Holiday">Holiday</option>
                        <option value="Event">Event</option>
                    </select>
                </div>
                <div>
                    <label for="image" class="block text-sm font-bold text-gray-700 mb-2">Banner Image (Optional)</label>
                    <input type="file" name="image" id="create_announcement_image" onchange="handleFileInputChange(this, 'create_announcement_image_preview', 'create_announcement_image_name')" accept="image/jpeg, image/png" class="w-full px-2 py-1.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F46E5] text-sm announcement-image-input">
                    <div id="create_announcement_image_preview" class="hidden mt-2 p-2 bg-blue-50/80 border border-blue-200 rounded-lg flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-2 overflow-hidden">
                            <i class="fa fa-image text-[#4F46E5] text-xs"></i>
                            <span id="create_announcement_image_name" class="text-[11px] font-extrabold text-[#1E2657] truncate"></span>
                        </div>
                        <button type="button" onclick="clearSelectedFile('create_announcement_image', 'create_announcement_image_preview')" class="px-2 py-0.5 bg-white border border-rose-200 text-rose-600 text-[10px] font-extrabold rounded hover:bg-rose-600 hover:text-white transition-colors cursor-pointer shrink-0 flex items-center gap-1">
                            <i class="fa fa-times"></i> Remove
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Announcement Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent" placeholder="e.g., Classes Suspended Tomorrow" required>
            </div>
            
            <div class="mb-8">
                <label for="content" class="block text-sm font-bold text-gray-700 mb-2">Announcement Details <span class="text-red-500">*</span></label>
                <textarea name="content" id="content" rows="4" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent" placeholder="Type the full announcement here..." required></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="closeModal('createModal')" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-bold transition-colors">Cancel</button>
                <button type="submit" class="bg-[#4F46E5] hover:bg-[#4338ca] text-white font-bold py-2 px-6 rounded-lg transition-colors flex items-center gap-2"><i class="fa-solid fa-save"></i> Post Announcement</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- EDIT ANNOUNCEMENT MODALS -->
<!-- ========================================== -->
@foreach($announcements as $announcement)
    <div id="editModal-{{ $announcement->id }}" class="fixed inset-0 z-50 hidden bg-gray-900/40 backdrop-blur-sm overflow-y-auto h-full w-full flex items-center justify-center">
        <div class="relative w-full max-w-2xl bg-white rounded-xl shadow-2xl p-8 m-4">
            
            <button type="button" onclick="closeModal('editModal-{{ $announcement->id }}')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="flex items-center gap-2 border-b pb-4 mb-6">
                <i class="fa-solid fa-pen text-[#4F46E5] text-xl"></i>
                <h2 class="text-xl font-bold text-gray-800">Edit Announcement</h2>
            </div>

            <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Category <span class="text-red-500">*</span></label>
                        <select name="type" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F46E5] bg-white" required>
                            <option value="Center Update" {{ $announcement->type == 'Center Update' ? 'selected' : '' }}>Center Update</option>
                            <option value="Class Suspension" {{ $announcement->type == 'Class Suspension' ? 'selected' : '' }}>Class Suspension</option>
                            <option value="Holiday" {{ $announcement->type == 'Holiday' ? 'selected' : '' }}>Holiday</option>
                            <option value="Event" {{ $announcement->type == 'Event' ? 'selected' : '' }}>Event</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Banner Image</label>
                        <input type="file" name="image" id="edit_announcement_image_{{ $announcement->id }}" onchange="handleFileInputChange(this, 'edit_announcement_image_preview_{{ $announcement->id }}', 'edit_announcement_image_name_{{ $announcement->id }}')" accept="image/jpeg, image/png" class="w-full px-2 py-1.5 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F46E5] text-sm announcement-image-input">
                        <div id="edit_announcement_image_preview_{{ $announcement->id }}" class="hidden mt-2 p-2 bg-blue-50/80 border border-blue-200 rounded-lg flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-2 overflow-hidden">
                                <i class="fa fa-image text-[#4F46E5] text-xs"></i>
                                <span id="edit_announcement_image_name_{{ $announcement->id }}" class="text-[11px] font-extrabold text-[#1E2657] truncate"></span>
                            </div>
                            <button type="button" onclick="clearSelectedFile('edit_announcement_image_{{ $announcement->id }}', 'edit_announcement_image_preview_{{ $announcement->id }}')" class="px-2 py-0.5 bg-white border border-rose-200 text-rose-600 text-[10px] font-extrabold rounded hover:bg-rose-600 hover:text-white transition-colors cursor-pointer shrink-0 flex items-center gap-1">
                                <i class="fa fa-times"></i> Remove
                            </button>
                        </div>
                        @if($announcement->image_path)
                            <p class="text-[10px] text-green-600 mt-1"><i class="fa-solid fa-check-circle"></i> Image currently attached</p>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Announcement Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ $announcement->title }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent" required>
                </div>
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Announcement Details <span class="text-red-500">*</span></label>
                    <textarea name="content" rows="4" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent" required>{{ $announcement->content }}</textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModal('editModal-{{ $announcement->id }}')" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-bold transition-colors">Cancel</button>
                    <button type="submit" class="bg-[#4F46E5] hover:bg-[#4338ca] text-white font-bold py-2 px-6 rounded-lg transition-colors flex items-center gap-2"><i class="fa-solid fa-save"></i> Update</button>
                </div>
            </form>
        </div>
    </div>

    <!-- COMMENTS MODAL FOR ADMIN -->
    <div id="commentsModal-{{ $announcement->id }}" class="fixed inset-0 z-50 hidden bg-gray-900/40 backdrop-blur-sm overflow-y-auto h-full w-full flex items-center justify-center">
        <div class="relative w-full max-w-2xl bg-white rounded-xl shadow-2xl p-6 m-4">
            <button type="button" onclick="closeModal('commentsModal-{{ $announcement->id }}')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            <div class="flex items-center gap-2 border-b pb-3 mb-4">
                <i class="fa-solid fa-comments text-[#8B5CF6] text-xl"></i>
                <div>
                    <h3 class="text-lg font-bold text-gray-800">{{ $announcement->title }}</h3>
                    <p class="text-xs text-gray-500">Announcement Comments & Discussion</p>
                </div>
            </div>

            @include('partials.announcement-comments', ['announcement' => $announcement])
        </div>
    </div>
@endforeach

<script>
    function openModal(modalId) { document.getElementById(modalId).classList.remove('hidden'); }
    function closeModal(modalId) { document.getElementById(modalId).classList.add('hidden'); }

    document.addEventListener("DOMContentLoaded", function() {
        const imageInputs = document.querySelectorAll('.announcement-image-input');
        imageInputs.forEach(input => {
            input.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const validTypes = ['image/jpeg', 'image/png'];
                    if (!validTypes.includes(file.type)) {
                        alert('Please select a valid image file (JPEG or PNG only).');
                        this.value = ''; 
                    } else if (file.size > 5 * 1024 * 1024) {
                        alert('File is too large. Please select an image smaller than 5MB.');
                        this.value = ''; 
                    }
                }
            });
        });
    });
</script>
@endsection