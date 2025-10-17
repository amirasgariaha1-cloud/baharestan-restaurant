<!-- admin/invoices_today.php -->
<?php // فقط فایل استاتیک؛ کانفیگ توسط API استفاده می‌شود ?>
<!doctype html>
<html lang="fa">
<head>
  <meta charset="utf-8">
  <title>فاکتورهای امروز</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    table{border-collapse: collapse;width:100%}
    th,td{padding:8px;border:1px solid #ddd;text-align:left}
    th{background:#f5f5f5}
    .btn{padding:6px 10px;margin:2px;cursor:pointer}
  </style>
</head>
<body>
  <h2>فاکتورهای امروز</h2>
  <p><button id="refresh">بارگذاری مجدد</button> <button id="bulk_send">ارسال گروهی برای انتخاب‌شده</button></p>
  <table id="tbl">
    <thead>
      <tr><th><input type="checkbox" id="chk_all"></th><th>Num</th><th>نام</th><th>تلفن</th><th>مبلغ</th><th>عضو؟</th><th>عملیات</th></tr>
    </thead>
    <tbody></tbody>
  </table>

<script>
function loadInvoices(){
  $('#tbl tbody').html('<tr><td colspan="7">در حال بارگذاری...</td></tr>');
  $.get('../api/get_today_invoices.php', function(resp){
    if (!resp.ok) { alert(resp.error || 'خطا در دریافت'); return; }
    const rows = resp.data || [];
    const tbody = $('#tbl tbody').empty();
    if (rows.length === 0) {
      tbody.append('<tr><td colspan="7">فاکتوری برای امروز یافت نشد.</td></tr>');
      return;
    }
    rows.forEach(r=>{
      const tel = r.TelNormalized || r.Tel || '';
      const isSub = r.is_subscriber ? 'بله' : 'خیر';
      const tr = $('<tr>');
      tr.append(`<td><input type="checkbox" class="chk" data-num="${r.Num}" data-branch="${r.BranchCode}" data-tel="${tel}"></td>`);
      tr.append(`<td>${r.Num}</td>`);
      tr.append(`<td>${r.Name||''}</td>`);
      tr.append(`<td>${tel}</td>`);
      tr.append(`<td>${r.Price||''}</td>`);
      tr.append(`<td>${isSub}</td>`);
      tr.append(`<td>
        <button class="btn send" data-num="${r.Num}" data-branch="${r.BranchCode}" data-tel="${tel}">ارسال پیام</button>
        <button class="btn add" data-tel="${tel}" data-name="${r.Name||''}">افزودن مشترک</button>
      </td>`);
      tbody.append(tr);
    });
  }, 'json').fail(function(){ alert('خطا در برقراری ارتباط'); });
}

$(document).on('click', '.send', function(){
  const btn = $(this);
  const num = btn.data('num'), branch = btn.data('branch'), tel = btn.data('tel');
  if (!tel || !tel.startsWith('09')) { alert('شماره موبایل نامعتبر'); return; }
  const message = prompt('متن پیام را وارد کنید:', `با تشکر از خرید شما. سفارش شماره ${num}.`);
  if (!message) return;
  $.post('../api/send_sms.php', { invoiceNum: num, branchCode: branch, tel: tel, message: message }, function(resp){
    alert(resp.message || 'انجام شد');
  }, 'json');
});

$(document).on('click', '.add', function(){
  const tel = $(this).data('tel'), name = $(this).data('name');
  $.post('../api/add_subscriber.php', { tel: tel, name: name }, function(resp){
    alert(resp.message || 'انجام شد');
  }, 'json');
});

$('#refresh').on('click', loadInvoices);
$('#chk_all').on('change', function(){ $('.chk').prop('checked', $(this).prop('checked')); });

$('#bulk_send').on('click', function(){
  const checked = $('.chk:checked');
  if (!checked.length) { alert('ابتدا ردیف‌ها را انتخاب کنید'); return; }
  const message = prompt('متن پیام را وارد کنید (برای همه انتخاب‌شده‌ها):', 'با تشکر از خرید شما.');
  if (!message) return;
  checked.each(function(){
    const el = $(this);
    const num = el.data('num'), branch = el.data('branch'), tel = el.data('tel');
    if (!tel || !tel.startsWith('09')) return;
    $.post('../api/send_sms.php', { invoiceNum: num, branchCode: branch, tel: tel, message: message }, function(resp){ /* optionally handle */ }, 'json');
  });
  alert('پیام‌ها در صف قرار گرفتند (منتظر پردازش worker)');
});

$(document).ready(loadInvoices);
</script>
</body>
</html>
