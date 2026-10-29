<!-- Terms and Conditions Modal Popup -->
<div id="dalcTermsModal" class="p-2 sm:p-4" style="display: none; position: fixed; inset: 0; z-index: 99999; overflow-y: auto; background-color: rgba(0, 0, 0, 0.55); align-items: center; justify-content: center;" role="dialog" aria-modal="true" aria-labelledby="termsModalTitle">
    <div style="position: fixed; inset: 0; background-color: rgba(0, 0, 0, 0.55);" onclick="closeTermsModal()"></div>
    
    <div class="rounded-2xl sm:rounded-xl max-h-[92dvh] sm:max-h-[90vh]" style="position: relative; background-color: #ffffff; width: 100%; max-width: 780px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25); display: flex; flex-direction: column; overflow: hidden; margin: auto; z-index: 10;">
        
        <!-- Modal Top Bar -->
        <div class="px-4 py-3 sm:px-6 sm:py-3.5" style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #e5e7eb; background: #ffffff;">
            <div id="termsModalTitle" style="font-size: 13px; font-weight: 700; color: #111827; text-transform: uppercase; letter-spacing: 0.05em;">
                Terms and Conditions
            </div>
            <button type="button" onclick="closeTermsModal()" aria-label="Close" class="active:scale-90 transition-transform" style="background: transparent; border: none; font-size: 24px; line-height: 1; color: #6b7280; cursor: pointer; padding: 2px 8px; border-radius: 6px;" onmouseover="this.style.color='#111827'; this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.color='#6b7280'; this.style.backgroundColor='transparent'">
                &times;
            </button>
        </div>

        <!-- Modal Body (Clean Plain White Scrollable Document) -->
        <div class="p-4 sm:p-7" style="overflow-y: auto; background-color: #ffffff; flex: 1 1 auto;">
            @include('legal.terms-content')
        </div>

        <!-- Modal Bottom Bar -->
        <div class="px-4 py-3 sm:px-6" style="display: flex; justify-content: flex-end; border-top: 1px solid #e5e7eb; background-color: #ffffff;">
            <button type="button" onclick="closeTermsModal()" class="w-full sm:w-auto active:scale-95 transition-all" style="background-color: #111827; color: #ffffff; border: none; padding: 8px 22px; font-size: 13px; font-weight: 600; border-radius: 8px; cursor: pointer;" onmouseover="this.style.backgroundColor='#374151'" onmouseout="this.style.backgroundColor='#111827'">
                Close
            </button>
        </div>

    </div>
</div>

<script>
    if (typeof window.openTermsModal !== 'function') {
        window.openTermsModal = function(e) {
            if (e && typeof e.preventDefault === 'function') {
                e.preventDefault();
            }
            var modal = document.getElementById('dalcTermsModal');
            if (modal) {
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
            }
            return false;
        };

        window.closeTermsModal = function() {
            var modal = document.getElementById('dalcTermsModal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        };

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' || e.keyCode === 27) {
                window.closeTermsModal();
            }
        });
    }
</script>

