import $ from 'jquery';
window.$ = window.jQuery = $; // 👈 บรรทัดนี้สำคัญมาก ต้องมี!

import select2 from 'select2';
select2();

$(document).ready(function() {
    if ($('.select2').length > 0) {
        $('.select2').select2({
            placeholder: "🔍 Type to search product...",
            allowClear: true,
            width: '100%'
        });

        $('.select2').on('select2:select', function (e) {
            document.getElementById('qty_input').focus();
        });
    }
});