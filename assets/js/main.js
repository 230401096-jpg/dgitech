// Minimal JS helpers for DGITECH
document.addEventListener('DOMContentLoaded', function(){
  // Simple mobile nav toggle (if present)
  var toggle = document.querySelector('.nav-toggle');
  if (toggle) {
    toggle.addEventListener('click', function(){
      var nav = document.querySelector('.nav-list');
      if (nav) nav.style.display = nav.style.display === 'block' ? '' : 'block';
    });
  }

  // Confirm dialogs for admin approve/reject buttons (if any)
  document.querySelectorAll('button.approve, button.reject').forEach(function(btn){
    btn.addEventListener('click', function(){
      var ok = confirm('Anda yakin?');
      if (!ok) event.preventDefault();
    });
  });
});
