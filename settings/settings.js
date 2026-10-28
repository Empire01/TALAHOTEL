// Check for saved dark mode preference in localStorage
if (localStorage.getItem('dark-mode') === 'enabled') {
  document.body.classList.add('dark-mode');
  document.getElementById('toggle-dark-mode').textContent = "Disable Dark Mode";
}

// Toggle dark mode on button click
document.getElementById('toggle-dark-mode').addEventListener('click', function() {
  if (document.body.classList.contains('dark-mode')) {
    document.body.classList.remove('dark-mode');
    localStorage.setItem('dark-mode', 'disabled');
    this.textContent = "Enable Dark Mode";
  } else {
    document.body.classList.add('dark-mode');
    localStorage.setItem('dark-mode', 'enabled');
    this.textContent = "Disable Dark Mode";
  }
});
