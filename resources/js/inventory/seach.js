import $ from 'jquery';
window.$ = window.jQuery = $;

import select2 from 'select2';
select2(); // 👈 ต้องรันคำสั่งนี้เพื่อให้ jQuery รู้จักฟังก์ชัน .select2()

$(document).ready(function() {
    if ($('.select2').length > 0) {
        $('.select2').select2({
            placeholder: "🔍 Type to search product...",
            allowClear: true,
            width: '100%', // บังคับให้ขนาดเท่ากับช่องเดิม
            containerCssClass: ":all:", // ให้คัดลอก Class จากช่องเดิมมาใช้
        });

        // แก้ปัญหาช่องพิมไม่ขึ้นเมื่อกด (Focus Conflict)
        $(document).on('select2:open', () => {
            document.querySelector('.select2-search__field').focus();
        });

        $('.select2').on('select2:select', function (e) {
            $(this).closest('form').focus();
        });
    }
});