<!-- admin/sms_history.php -->
<!doctype html>
<html lang="fa">
<head>
  <meta charset="utf-8">
  <title>تاریخچه پیامک‌ها</title>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>table{border-collapse:collapse;width:100%}th,td{padding:6px;border:1px solid #ddd}</style>
</head>
<body>
  <h2>تاریخچه پیامک‌ها</h2>
  <p><button id="refresh">بارگذاری</button></p>
  <table id="tbl" width="100%">
    <thead><tr><th>id</th><th>invoice</th><th>tel</th><th>message</th><th>status</th><th>created</th><th>sent_at</th><th>عملیات</th></tr></thead>
    <tbody></tbody>
  </table>

<script>
function loadHistory(){
  $('#tbl tbody').html('<tr><td colspan="8">در حال بارگذاری...</td></tr>');
  $.get('../api/sms_history.php?limit=500', function(resp){
    if (!resp.ok) { alert(resp.message || 'خطا'); return; }
    const body = $('#tbl tbody').empty();
    resp.data.forEach(r=>{
      const tr = $('<tr>');
      tr.append(`<td>${r.id}</td>`);
      tr.append(`<td>${r.invoice_num||''}</td>`);
      tr.append(`<td>${r.tel}</td>`);
      tr.append(`<td>${r.message}</td>`);
      tr.append(`<td>${r.status}</td>`);
      tr.append(`<td>${r.created_at}</td>`);
      tr.append(`<td>${r.sent_at||''}</td>`);
      tr.append(`<td>
        <button class="requeue" data-id="${r.id}">ارسال مجدد</button>
      </td>`);
      body.append(tr);
    });
  }, 'json');
}

$(document).on('click', '.requeue', function(){
  const id = $(this).data('id');
  if (!confirm('آیا می‌خواهید این پیام دوباره در صف قرار گیرد؟')) return;
  $.post('../api/requeue_sms.php', { id: id }, function(resp){
    alert(resp.message || 'انجام شد');
    loadHistory();
  }, 'json');
});

$('#refresh').on('click', loadHistory);
$(document).ready(loadHistory);
</script>
</body>
</html>
