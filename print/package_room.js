function printRoomDetails() {
  const modalContent = document.querySelector("#packageDetailsModal .modal-body").innerHTML;
  const customerName = document.getElementById('guestName').textContent || "N/A";

  // Get current date and time
  const now = new Date();
  const currentDate = now.toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  });

  const currentTime = now.toLocaleTimeString();
  
  // Generate receipt number
  const receiptNumber = Math.floor(100000 + Math.random() * 900000);

  const printWindow = window.open('', '', 'width=1000,height=600');
  printWindow.document.write(`
    <html>
      <head>
        <title>Virgo Hotel Reservation</title>
        <style>
          html, body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #fff;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
          }

          .receipt-container {
            width: 100%;
            max-width: 600px;
            padding: 30px;
            background: white;
            box-shadow: 0 0 5px rgba(0,0,0,0.1);
            box-sizing: border-box;
          }

          .hotel-header {
            text-align: center;
            margin-bottom: 20px;
          }

          .hotel-name {
            font-size: 26px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
          }

          .hotel-address {
            font-size: 13px;
            color: #666;
          }

          .receipt-title {
            font-size: 16px;
            margin-top: 10px;
            color: #555;
          }

          .receipt-meta {
            margin-top: 50px;
            font-size: 14px;
          }

          .receipt-meta span {
            display: block;
            margin-top: 10px;
            margin-bottom: 4px;
          }

          table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
          }

          th, td {
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ccc;
            text-align: left;
            vertical-align: top;
          }

          th {
            background-color: #f9f9f9;
            color: #333;
            width: 40%;
          }

          td {
            color: #444;
          }

          hr {
            margin: 30px 0;
            border: none;
            border-top: 1px solid #bbb;
          }

          .footer-message {
            text-align: center;
            font-size: 14px;
            color: #555;
            margin-top: 10px;
            line-height: 1.5;
          }

          .print-time {
            text-align: center;
            font-size: 12px;
            color: #999;
            margin-top: 5px;
          }

          .customer-name {
            display: flex;
            justify-content: space-between;
            margin-top: 80px;
            font-size: 15px;
            color: #555;
          }

          .customer-name div {
            width: 45%;
            text-align: center;
          }

          .customer-name h6 {
            font-weight: bold;
            margin: 0;
          }

          .customer-name small {
            display: block;
            color: #888;
          }

          .qr-code {
            margin-top: 20px;
            text-align: center;
          }

          @media print {
            html, body {
              height: auto;
              background: white;
              display: block;
            }

            .receipt-container {
              margin: 0 auto;
              box-shadow: none;
              width: 600px;
            }

            .role {
              font-weight: bold;
            }

            .name {
              font-size: 16px;
              font-weight: normal;
            }
          }
        </style>
      </head>
      <body onload="window.print(); window.close();">
        <div class="receipt-container">
          <div class="hotel-header">
            <div class="hotel-name">Virgo Hotel</div>
            <div class="hotel-address">1234 Virgo St., Malabon City, Philippines<br>Tel: (02) 8123-4567</div>
            <div class="receipt-title">Official Receipt / Booking Confirmation</div>
          </div>

          <div class="receipt-meta">
            <span><strong>Receipt No.:</strong> #${receiptNumber}</span>
            <span><strong>Date Issued:</strong> ${currentDate}</span>
            <span><strong>Time Issued:</strong> ${currentTime}</span>
          </div>

          <table>
          ${modalContent.replace(/<div class="hotel-header[^>]*>[\s\S]*?<\/div>/i, '').trim()}
          </table>

          <hr />

          <div class="footer-message">
            Thank you for choosing Virgo Hotel!<br>
            We hope you enjoyed your stay. Please visit us again.
          </div>
          <div class="print-time">
            Printed on: ${currentDate} at ${currentTime}
          </div>

          <div class="customer-name">
            <div>
              <h6 class="name">${customerName}</h6>
              <small class="role" style="border-top: 1px solid #000; display: inline-block; width: 200px; text-align: center;">Customer</small>
            </div>
            <div>
              <h6 class="name">Briant Polvorosa</h6>
              <small class="role" style="border-top: 1px solid #000; display: inline-block; width: 200px; text-align: center;">Receptionist</small>
            </div>
          </div>
        </div>
      </body>
    </html>
  `);
  printWindow.document.close();
}
