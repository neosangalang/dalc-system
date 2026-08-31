<div class="card shadow-sm mt-4 border-0">
    <div class="card-header bg-white border-bottom pb-2 pt-3">
        <h5 class="fw-bold m-0"><i class="fa fa-comments me-2 text-primary"></i> Comments</h5>
    </div>
    
    <div class="card-body p-0">
        <div id="chat-history-{{ $item->id }}" class="chat-history p-4" style="max-height: 400px; overflow-y: auto; background-color: #f8f9fa;">
            
            @forelse(collect($item->comments)->reverse() as $comment) 
                <div class="mb-3 {{ $comment->user_id === Auth::id() ? 'text-end' : 'text-start' }}">
                    <div class="d-inline-block p-3 rounded shadow-sm" 
                         style="max-width: 80%; text-align: left; 
                         {{ $comment->user_id === Auth::id() ? 'background-color: #0d6efd; color: white;' : 'background-color: white; border: 1px solid #e9ecef;' }}">
                        
                        <strong style="font-size: 0.85rem; {{ $comment->user->role == 'guardian' ? 'color: #d0e8ff;' : 'color: #6c757d;' }}">
                            {{ $comment->user->name }}
                        </strong>
                        
                        <div class="m-0 mt-1 rich-text-content" style="font-size: 0.95rem;">
                            {!! $comment->body !!}
                        </div>
                    </div>
                    <div class="text-muted mt-1" style="font-size: 0.75rem;">{{ $comment->created_at->diffForHumans() }}</div>
                </div>
            @empty
                <div class="text-center text-muted my-4">
                    <i class="fa fa-comment-dots fa-2x mb-2 text-light"></i>
                    <p class="m-0 fst-italic">No comments yet. Start the conversation!</p>
                </div>
            @endforelse
        </div>

        <div class="p-3 bg-white border-top">
            <form action="{{ route('comments.store') }}" method="POST" id="comment-form-{{ $item->id }}" onsubmit="submitCommentAjax(event, '{{ $item->id }}')">
                @csrf
                <input type="hidden" name="commentable_type" value="{{ get_class($item) }}">
                <input type="hidden" name="commentable_id" value="{{ $item->id }}">
                <input type="hidden" name="body" id="hidden-body-{{ $item->id }}">

                <div class="mb-2">
                    <div id="editor-{{ $item->id }}" style="height: 120px; border-radius: 0 0 5px 5px;"></div>
                </div>

                <div class="d-flex justify-content-start">
                    <button type="submit" class="btn text-primary fw-bold p-0" style="background: none; border: none; font-size: 1.1rem;">
                        Post
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .rich-text-content img { max-width: 100%; height: auto; border-radius: 5px; margin-top: 10px; }
    .rich-text-content p { margin-bottom: 0.5rem; }
</style>

@push('scripts')
    @once
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.querySelectorAll('[id^="editor-"]').forEach(function(editorEl) {
                    if (!editorEl.classList.contains('ql-container')) {
                        new Quill('#' + editorEl.id, {
                            theme: 'snow',
                            placeholder: 'Write a comment...',
                            // Removed 'image' and 'video' from the toolbar array below
                            modules: { toolbar: [ ['bold', 'italic', 'underline', 'strike'], ['link'] ] } 
                        });
                        if (editorEl.previousSibling) {
                            editorEl.previousSibling.style.borderRadius = '5px 5px 0 0';
                            editorEl.previousSibling.style.backgroundColor = '#f8f9fa';
                        }
                    }
                });

                document.querySelectorAll('.chat-history').forEach(function(box) {
                    box.scrollTop = box.scrollHeight;
                });
            });

            window.submitCommentAjax = async function(e, itemId) {
                e.preventDefault(); 

                let form = document.getElementById('comment-form-' + itemId);
                let submitBtn = form.querySelector('button[type="submit"]');
                let hiddenBody = document.getElementById('hidden-body-' + itemId);
                let quillEditor = document.querySelector('#editor-' + itemId + ' .ql-editor');

                let htmlContent = quillEditor.innerHTML;
                if (htmlContent === '<p><br></p>' || htmlContent.trim() === '') return; 
                
                hiddenBody.value = htmlContent;

                let originalBtnText = submitBtn.innerHTML;
                submitBtn.innerHTML = 'Posting...';
                submitBtn.disabled = true;

                try {
                    let response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (!response.ok) {
                        alert('Backend error: Could not save comment.');
                        submitBtn.innerHTML = originalBtnText;
                        submitBtn.disabled = false;
                        return;
                    }

                    let text = await response.text();
                    let doc = new DOMParser().parseFromString(text, 'text/html');

                    let newChatHistory = doc.getElementById('chat-history-' + itemId);
                    
                    if (!newChatHistory) {
                        let templates = doc.querySelectorAll('template');
                        for (let t of templates) {
                            let found = t.content.getElementById('chat-history-' + itemId);
                            if (found) {
                                newChatHistory = found;
                                break;
                            }
                        }
                    }

                    let currentChatHistory = document.getElementById('chat-history-' + itemId);

                    if (newChatHistory && currentChatHistory) {
                        currentChatHistory.innerHTML = newChatHistory.innerHTML;
                        quillEditor.innerHTML = '';
                        hiddenBody.value = '';
                        currentChatHistory.scrollTop = currentChatHistory.scrollHeight;
                    } else {
                        window.location.reload();
                    }
                } catch (error) {
                    console.error(error);
                    form.submit();
                } finally {
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                }
            };
        </script>
    @endonce
@endpush