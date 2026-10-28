document.addEventListener('DOMContentLoaded', function () {
  const logoutBtn = document.getElementById('logoutBtn');

  if (!logoutBtn) return;

  logoutBtn.addEventListener('click', function (e) {
    e.preventDefault();

    const proceedToLogout = () => {
      window.location.href = 'logout.php';
    };

    if (typeof Swal !== 'undefined') {
      Swal.fire({
        title: 'Are you sure?',
        text: "You’ll be logged out.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Logout',
      }).then((result) => {
        if (result.isConfirmed) {
          const loader = document.getElementById('global-loader');
          if (loader) {
            loader.style.display = 'none';
            loader.style.opacity = '0';
          }

          proceedToLogout();
        }
      });
      return;
    }

    const confirmed = window.confirm('You will be logged out. Continue?');
    if (confirmed) {
      proceedToLogout();
    }
  });
});