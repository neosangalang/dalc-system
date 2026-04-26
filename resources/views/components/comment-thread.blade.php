<div class="card shadow-sm mt-4 border-0">
    <div class="card-header bg-white border-bottom pb-2 pt-3">
        <h5 class="fw-bold m-0"><i class="fa fa-comments me-2 text-primary"></i> Comments</h5>
    </div>
    
    <div class="card-body p-0">
        <div class="chat-history p-4" style="max-height: 400px; overflow-y: auto; background-color: #f8f9fa;">
            
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
            
            <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">

            <form action="{{ route('comments.store') }}" method="POST" id="comment-form-{{ $item->id }}" onsubmit="prepareComment('{{ $item->id }}')">
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
            
            <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    // Initialize the editor with a specific ID so multiple boxes on one page don't break
                    var quill = new Quill('#editor-{{ $item->id }}', {
                        theme: 'snow',
                        placeholder: 'Write a comment...',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline', 'strike'], // toggled buttons
                                ['link', 'image', 'video']                 // link and media
                            ]
                        }
                    });

                    // Remove the top border radius of the toolbar to look clean
                    document.querySelector('#editor-{{ $item->id }}').previousSibling.style.borderRadius = '5px 5px 0 0';
                    document.querySelector('#editor-{{ $item->id }}').previousSibling.style.backgroundColor = '#f8f9fa';
                });

                // Before form submits, grab the HTML inside the editor and put it in our hidden input
                function prepareComment(itemId) {
                    var htmlContent = document.querySelector('#editor-' + itemId + ' .ql-editor').innerHTML;
                    
                    // Prevent empty submissions (Quill puts <p><br></p> when empty)
                    if (htmlContent === '<p><br></p>') {
                        document.getElementById('hidden-body-' + itemId).value = '';
                    } else {
                        document.getElementById('hidden-body-' + itemId).value = htmlContent;
                    }
                }
            </script>

        </div>
    </div>
</div>

<style>
    /* Ensure images pasted into the rich text editor don't overflow the chat bubbles */
    .rich-text-content img {
        max-width: 100%;
        height: auto;
        border-radius: 5px;
        margin-top: 10px;
    }
    .rich-text-content p { margin-bottom: 0.5rem; }
</style>