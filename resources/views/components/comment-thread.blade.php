@php
    $rawComments = $item->comments ?? collect();
    $threadComments = collect($rawComments)
        ->unique(function ($c) {
            $bodyClean = trim(preg_replace('/\s+/', ' ', strip_tags($c->body ?? '')));
            $timeKey = $c->created_at ? $c->created_at->format('Y-m-d H:i') : '';
            return ($c->user_id ?? 0) . '|' . $bodyClean . '|' . $timeKey;
        })
        ->sortBy('created_at')
        ->values();
@endphp

<div class="mt-4 pt-1">
    <div class="bg-slate-50/70 border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-xs">
        
        <!-- Header -->
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200/80">
            <div class="flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-blue-50 border border-blue-200/70 text-[#3B82F6] flex items-center justify-center text-xs shadow-2xs">
                    <i class="fa-solid fa-comments"></i>
                </span>
                <h5 class="font-black text-xs sm:text-[13px] text-[#1E2657] uppercase tracking-wider m-0">
                    Comments & Notes
                </h5>
                <span id="comment-count-{{ $item->id }}" class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200/80 text-slate-700">
                    {{ $threadComments->count() }}
                </span>
            </div>
            <div class="text-[11px] font-semibold text-slate-400 hidden sm:block">
                Contextual Discussion
            </div>
        </div>

        <!-- Chat History -->
        <div id="chat-history-{{ $item->id }}" class="chat-history space-y-3.5 overflow-y-auto pr-1" style="max-height: 380px;">
            @forelse($threadComments as $comment)
                @php
                    $isOwn = $comment->user_id === Auth::id();
                    $authorName = $comment->user->name ?? 'User';
                    $authorRole = $comment->user->role ?? 'user';
                    $nameParts = array_filter(explode(' ', trim($authorName)));
                    $initials = '';
                    foreach ($nameParts as $p) {
                        $initials .= strtoupper(substr($p, 0, 1));
                        if (strlen($initials) >= 2) break;
                    }
                    if (!$initials) $initials = 'U';
                @endphp
                <div class="comment-bubble-item flex {{ $isOwn ? 'justify-end' : 'justify-start' }} gap-2.5 items-end">
                    @if(!$isOwn)
                        <!-- Other User Avatar -->
                        <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-700 font-extrabold text-[11px] flex items-center justify-center shrink-0 shadow-2xs border border-white" title="{{ $authorName }}">
                            {{ $initials }}
                        </div>
                    @endif

                    <div class="max-w-[88%] sm:max-w-[80%]">
                        <!-- Metadata Header -->
                        <div class="flex items-center gap-1.5 mb-1 {{ $isOwn ? 'justify-end' : 'justify-start' }}">
                            @if($isOwn)
                                <span class="text-[10px] font-bold text-slate-400">{{ $comment->created_at ? $comment->created_at->diffForHumans() : 'Just now' }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-blue-100/90 text-blue-700 border border-blue-200/70">You</span>
                                <span class="text-xs font-bold text-slate-700">{{ $authorName }}</span>
                            @else
                                <span class="text-xs font-bold text-slate-800">{{ $authorName }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold {{ $authorRole === 'guardian' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                                    {{ ucfirst($authorRole) }}
                                </span>
                                <span class="text-[10px] font-bold text-slate-400">{{ $comment->created_at ? $comment->created_at->diffForHumans() : 'Just now' }}</span>
                            @endif
                        </div>

                        <!-- Bubble Card: Subtle light-blue tint for own, clean white for other -->
                        <div class="p-3.5 rounded-2xl text-[13px] sm:text-sm leading-relaxed shadow-2xs transition-all
                            {{ $isOwn 
                                ? 'bg-blue-50/80 border border-blue-200/90 text-slate-800 rounded-tr-xs' 
                                : 'bg-white border border-slate-200/90 text-slate-800 rounded-tl-xs' }}">
                            <div class="rich-text-content m-0 font-medium">
                                {!! $comment->body !!}
                            </div>
                        </div>
                    </div>

                    @if($isOwn)
                        <!-- Own Avatar -->
                        <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 font-extrabold text-[11px] flex items-center justify-center shrink-0 shadow-2xs border border-white" title="You ({{ $authorName }})">
                            {{ $initials }}
                        </div>
                    @endif
                </div>
            @empty
                <div id="no-comments-{{ $item->id }}" class="text-center py-8 px-4 bg-white/70 rounded-xl border border-dashed border-slate-200/90 my-2">
                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2 text-base">
                        <i class="fa-regular fa-comment-dots"></i>
                    </div>
                    <p class="text-xs font-bold text-slate-600 m-0 mb-0.5">No comments recorded yet</p>
                    <p class="text-[11px] font-medium text-slate-400 m-0">Leave an observation, clarification, or note below.</p>
                </div>
            @endforelse
        </div>

        <!-- Composer Box -->
        <div class="mt-3.5 pt-3 border-t border-slate-200/80">
            <form action="{{ route('comments.store') }}" method="POST" id="comment-form-{{ $item->id }}" onsubmit="submitCommentAjax(event, '{{ $item->id }}')">
                @csrf
                <input type="hidden" name="commentable_type" value="{{ get_class($item) }}">
                <input type="hidden" name="commentable_id" value="{{ $item->id }}">
                <input type="hidden" name="body" id="hidden-body-{{ $item->id }}">

                <div class="rounded-xl border border-slate-300 bg-white overflow-hidden shadow-2xs focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100 transition-all">
                    <div id="editor-{{ $item->id }}"></div>
                </div>

                <div class="mt-2.5 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400 font-medium hidden sm:inline">
                        Use the editor to compose your message
                    </span>
                    <button type="submit" id="btn-submit-{{ $item->id }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#1E2657] hover:bg-[#12163A] text-white text-xs font-black rounded-xl shadow-xs transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer ml-auto">
                        <i class="fa-solid fa-paper-plane text-[10px]"></i>
                        <span>Post Comment</span>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

<style>
    .rich-text-content img { max-width: 100%; height: auto; border-radius: 8px; margin-top: 8px; }
    .rich-text-content p { margin-bottom: 0.35rem; }
    .rich-text-content p:last-child { margin-bottom: 0; }
    .rich-text-content a { color: #2563EB; text-decoration: underline; }
    
    .ql-toolbar.ql-snow {
        border: none !important;
        border-bottom: 1px solid #E2E8F0 !important;
        background-color: #F8FAFC !important;
        padding: 6px 10px !important;
    }
    .ql-container.ql-snow {
        border: none !important;
        font-family: inherit !important;
        font-size: 0.875rem !important;
    }
    .ql-editor {
        min-height: 70px !important;
        max-height: 140px !important;
        padding: 8px 12px !important;
    }
    .ql-editor.ql-blank::before {
        font-style: normal !important;
        color: #94A3B8 !important;
        font-size: 0.8125rem !important;
    }
    
    @keyframes commentFadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-comment-in {
        animation: commentFadeIn 0.22s ease-out forwards;
    }
</style>

@push('scripts')
    @once
        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

        <script>
            window.activeQuillInstances = window.activeQuillInstances || {};

            function initQuillEditors() {
                if (typeof Quill === 'undefined') return;
                document.querySelectorAll('[id^="editor-"]').forEach(function(editorEl) {
                    if (!editorEl.classList.contains('ql-container')) {
                        let q = new Quill('#' + editorEl.id, {
                            theme: 'snow',
                            placeholder: 'Write a comment or clinical note...',
                            modules: { 
                                toolbar: [ 
                                    ['bold', 'italic', 'underline', 'strike'], 
                                    ['link'] 
                                ] 
                            } 
                        });
                        window.activeQuillInstances[editorEl.id] = q;
                    }
                });

                document.querySelectorAll('.chat-history').forEach(function(box) {
                    box.scrollTop = box.scrollHeight;
                });
            }

            if (document.readyState === 'loading') {
                document.addEventListener("DOMContentLoaded", initQuillEditors);
            } else {
                initQuillEditors();
            }

            // Ensure dynamic/Alpine modals trigger Quill initialization if opened
            document.addEventListener("click", function(e) {
                if (e.target.closest('[id^="editor-"]') || e.target.closest('button') || e.target.closest('a')) {
                    setTimeout(initQuillEditors, 50);
                    setTimeout(initQuillEditors, 200);
                }
            });

            window.submitCommentAjax = async function(e, itemId) {
                e.preventDefault(); 

                let form = document.getElementById('comment-form-' + itemId);
                if (!form) return;

                let submitBtn = document.getElementById('btn-submit-' + itemId) || form.querySelector('button[type="submit"]');
                let hiddenBody = document.getElementById('hidden-body-' + itemId);
                let editorId = 'editor-' + itemId;
                let quill = window.activeQuillInstances[editorId];

                if (!quill && typeof Quill !== 'undefined') {
                    initQuillEditors();
                    quill = window.activeQuillInstances[editorId];
                }

                let htmlContent = '';
                let textLength = 0;
                if (quill) {
                    htmlContent = quill.root.innerHTML;
                    textLength = quill.getText().trim().length;
                } else {
                    let rawEl = document.querySelector('#' + editorId + ' .ql-editor');
                    if (rawEl) {
                        htmlContent = rawEl.innerHTML;
                        textLength = rawEl.textContent.trim().length;
                    } else if (hiddenBody) {
                        htmlContent = hiddenBody.value;
                        textLength = htmlContent.trim().length;
                    }
                }

                if (textLength === 0 || htmlContent === '<p><br></p>' || htmlContent.trim() === '') {
                    if (window.showToast) {
                        window.showToast('Please type a comment before posting.', 'warning');
                    }
                    return;
                }
                
                if (hiddenBody) {
                    hiddenBody.value = htmlContent;
                }

                let originalBtnHtml = submitBtn ? submitBtn.innerHTML : '<i class="fa-solid fa-paper-plane text-[10px]"></i> <span>Post Comment</span>';
                if (submitBtn) {
                    submitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin text-[10px]"></i> <span>Posting...</span>';
                    submitBtn.disabled = true;
                }

                try {
                    let csrfToken = form.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    let headers = { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    };
                    if (csrfToken) {
                        headers['X-CSRF-TOKEN'] = csrfToken;
                    }

                    let response = await fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: headers
                    });

                    if (!response.ok) {
                        let errorMsg = 'Could not save comment.';
                        try {
                            let errJson = await response.json();
                            if (errJson.message) errorMsg = errJson.message;
                        } catch(err) {}
                        if (window.showToast) {
                            window.showToast(errorMsg, 'error');
                        }
                        return;
                    }

                    let currentChatHistory = document.getElementById('chat-history-' + itemId);
                    let countEl = document.getElementById('comment-count-' + itemId);

                    // Check if JSON response returned
                    let contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        let data = await response.json();
                        if (data.success && data.comment) {
                            // Remove empty placeholder if present
                            let noCommentsEl = document.getElementById('no-comments-' + itemId);
                            if (noCommentsEl) {
                                noCommentsEl.remove();
                            }

                            // Compute initials
                            let nameParts = (data.comment.user_name || 'You').trim().split(' ').filter(Boolean);
                            let initials = '';
                            for (let p of nameParts) {
                                initials += p.charAt(0).toUpperCase();
                                if (initials.length >= 2) break;
                            }
                            if (!initials) initials = 'U';

                            // Append new bubble dynamically with smooth animation
                            let newBubble = document.createElement('div');
                            newBubble.className = 'comment-bubble-item flex justify-end gap-2.5 items-end animate-comment-in';
                            newBubble.innerHTML = `
                                <div class="max-w-[88%] sm:max-w-[80%]">
                                    <div class="flex items-center gap-1.5 mb-1 justify-end">
                                        <span class="text-[10px] font-bold text-slate-400">Just now</span>
                                        <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-blue-100/90 text-blue-700 border border-blue-200/70">You</span>
                                        <span class="text-xs font-bold text-slate-700">${data.comment.user_name || 'You'}</span>
                                    </div>
                                    <div class="p-3.5 rounded-2xl text-[13px] sm:text-sm leading-relaxed shadow-2xs transition-all bg-blue-50/80 border border-blue-200/90 text-slate-800 rounded-tr-xs">
                                        <div class="rich-text-content m-0 font-medium">
                                            ${data.comment.body}
                                        </div>
                                    </div>
                                </div>
                                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 font-extrabold text-[11px] flex items-center justify-center shrink-0 shadow-2xs border border-white" title="You">
                                    ${initials}
                                </div>
                            `;

                            if (currentChatHistory) {
                                currentChatHistory.appendChild(newBubble);
                                currentChatHistory.scrollTop = currentChatHistory.scrollHeight;
                            }

                            // Clear Editor
                            if (quill) {
                                quill.setText('');
                            } else {
                                let rawEl = document.querySelector('#' + editorId + ' .ql-editor');
                                if (rawEl) rawEl.innerHTML = '';
                            }
                            if (hiddenBody) hiddenBody.value = '';

                            // Increment counter
                            if (countEl) {
                                let curr = parseInt(countEl.textContent.trim()) || 0;
                                countEl.textContent = curr + 1;
                            }

                            if (window.showToast) {
                                window.showToast('Comment posted successfully!', 'success');
                            }
                            return;
                        }
                    }

                    // Fallback HTML parsing if response wasn't JSON
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

                    if (newChatHistory && currentChatHistory) {
                        currentChatHistory.innerHTML = newChatHistory.innerHTML;
                        if (quill) {
                            quill.setText('');
                        }
                        if (hiddenBody) {
                            hiddenBody.value = '';
                        }
                        currentChatHistory.scrollTop = currentChatHistory.scrollHeight;
                        if (window.showToast) {
                            window.showToast('Comment posted!', 'success');
                        }
                    }

                } catch (error) {
                    console.error('Comment submission error:', error);
                    if (window.showToast) {
                        window.showToast('Connection error. Please try again.', 'error');
                    }
                } finally {
                    if (submitBtn) {
                        submitBtn.innerHTML = originalBtnHtml;
                        submitBtn.disabled = false;
                    }
                }
            };
        </script>
    @endonce
@endpush
