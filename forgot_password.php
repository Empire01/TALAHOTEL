<?php
require 'vendor/autoload.php';
include 'config/config.php';
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Forgot Password</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
</head>

<body>
  <div class="container mt-5 d-flex justify-content-center">
    <form id="forgotPasswordForm" class="p-5 shadow bg-light w-50 rounded-4 text-center" autocomplete="off">
      <h2>Reset your password</h2>
      <p>Enter your email to get a reset link.</p>
      <input type="email" name="email" id="email" class="form-control my-3" placeholder="Enter your email" required>
      <button type="submit" id="sendBtn" class="btn btn-primary w-100">Send Reset Link</button>
    </form>
  </div>

  <?php include 'footer.php'; ?>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>

  <script>
    $(function() {
      $('#forgotPasswordForm').on('submit', function(e) {
        e.preventDefault();
        var email = $('#email').val().trim();
        if (!email) {
          Toastify({ text: 'Please enter your email.', duration: 3000, gravity: 'top', position: 'right', backgroundColor: 'red' }).showToast();
          return;
        }

        $('#sendBtn').prop('disabled', true).text('Sending...');

        $.post('send_reset_link.php', { email: email }, function(resp) {
          try {
            var json = (typeof resp === 'string') ? JSON.parse(resp) : resp;
          } catch (e) {
            Toastify({ text: 'Unexpected server response.', duration: 4000, gravity: 'top', position: 'right', backgroundColor: 'red' }).showToast();
            $('#sendBtn').prop('disabled', false).text('Send Reset Link');
            return;
          }

          var bg = json.success ? 'green' : 'red';
          Toastify({ text: json.message || 'Check your email for the link.', duration: 3000, gravity: 'top', position: 'right', backgroundColor: bg }).showToast();

          if (json.debug) console.debug('mail debug:', json.debug);

          if (json.success) {
            setTimeout(function() {
              window.location.href = 'customer_login.php';
            }, 2000);
          }

          $('#sendBtn').prop('disabled', false).text('Send Reset Link');
        }).fail(function(xhr) {
          Toastify({ text: 'Could not contact server. Try again later.', duration: 4000, gravity: 'top', position: 'right', backgroundColor: 'red' }).showToast();
          $('#sendBtn').prop('disabled', false).text('Send Reset Link');
        });
      });
    });
  </script>

</body>

</html>