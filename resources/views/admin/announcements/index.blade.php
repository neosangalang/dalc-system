@extends('layouts.app') 

@section('content')
<div class="container mx-auto mt-10 max-w-5xl">
    
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-[#1E2657]">Manage Announcements</h2>
        <button type="button" onclick="openModal('createModal')" class="bg-[#4F46E5] hover:bg-[#4338ca] text-white font-bold py-2 px-4 rounded-lg shadow-sm transition-colors flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Post New Announcement
        </button>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-sm">
                    <th class="p-4 font-bold">Title</th>
                    <th class="p-4 font-bold">Date Posted</th>
                    <th class="p-4 font-bold text-center">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($announcements as $announcement)
                    <tr class="border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <td class="p-4 text-sm font-bold text-gray-800">{{ $announcement->title }}</td>
                        <td class="p-4 text-sm text-gray-500">{{ $announcement->created_at->format('M d, Y h:i A') }}</td>
                        <td class="p-4 text-center">
                            <div class="flex justify-center gap-2">
                                <!-- The Edit Button -->
                                <button type="button" onclick="openModal('editModal-{{ $announcement->id }}')" class="text-blue-500 hover:text-blue-700 font-medium text-sm bg-blue-50 px-3 py-1 rounded-md transition-colors">
                                    Edit
                                </button>
                                
                                <!-- The Delete Button -->
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
                        <td colspan="3" class="p-8 text-center text-gray-400 font-medium">No announcements found. Click the button above to create one!</td>
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
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <div class="flex items-center gap-2 border-b pb-4 mb-6">
            <i class="fa-solid fa-bullhorn text-[#4F46E5] text-xl"></i>
            <h2 class="text-xl font-bold text-gray-800">Post a New Announcement</h2>
        </div>

        <form action="{{ route('admin.announcements.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Announcement Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" id="title" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent" placeholder="e.g., Classes Suspended Tomorrow" required>
            </div>
            <div class="mb-8">
                <label for="content" class="block text-sm font-bold text-gray-700 mb-2">Announcement Details <span class="text-red-500">*</span></label>
                <textarea name="content" id="content" rows="4" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent" placeholder="Type the full announcement here..." required></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="closeModal('createModal')" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-bold transition-colors">
                    Cancel
                </button>
                <button type="submit" class="bg-[#4F46E5] hover:bg-[#4338ca] text-white font-bold py-2 px-6 rounded-lg transition-colors flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Post Announcement
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================== -->
<!-- EDIT ANNOUNCEMENT MODALS (Generated for each) -->
<!-- ========================================== -->
@foreach($announcements as $announcement)
    <div id="editModal-{{ $announcement->id }}" class="fixed inset-0 z-50 hidden bg-gray-900/40 backdrop-blur-sm overflow-y-auto h-full w-full flex items-center justify-center">
        <div class="relative w-full max-w-2xl bg-white rounded-xl shadow-2xl p-8 m-4">
            
            <button type="button" onclick="closeModal('editModal-{{ $announcement->id }}')" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>

            <div class="flex items-center gap-2 border-b pb-4 mb-6">
                <i class="fa-solid fa-pen text-[#4F46E5] text-xl"></i>
                <h2 class="text-xl font-bold text-gray-800">Edit Announcement</h2>
            </div>

            <form action="{{ route('admin.announcements.update', $announcement->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Announcement Title <span class="text-red-500">*</span></label>
                    <!-- Pre-fills the existing title -->
                    <input type="text" name="title" value="{{ $announcement->title }}" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent" required>
                </div>
                <div class="mb-8">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Announcement Details <span class="text-red-500">*</span></label>
                    <!-- Pre-fills the existing content -->
                    <textarea name="content" rows="4" class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#4F46E5] focus:border-transparent" required>{{ $announcement->content }}</textarea>
                </div>
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeModal('editModal-{{ $announcement->id }}')" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-bold transition-colors">
                        Cancel
                    </button>
                    <button type="submit" class="bg-[#4F46E5] hover:bg-[#4338ca] text-white font-bold py-2 px-6 rounded-lg transition-colors flex items-center gap-2">
                        <i class="fa-solid fa-save"></i> Update Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
@endforeach

<!-- ========================================== -->
<!-- THE CORRECTED JAVASCRIPT -->
<!-- ========================================== -->
<script>
    // Notice the 'modalId' inside the parenthesis! This is what was missing.
    function openModal(modalId) {
        document.getElementById(modalId).classList.remove('hidden');
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
@endsection