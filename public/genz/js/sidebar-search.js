document.addEventListener('DOMContentLoaded', function () {
    var search = document.getElementById('genz-navigation-search');
    var menu = document.getElementById('sidebarMenu');

    if (!search || !menu) return;

    var items = Array.prototype.slice.call(menu.children);

    search.addEventListener('input', function () {
        var query = search.value.trim().toLocaleLowerCase();

        items.forEach(function (item) {
            if (item.classList.contains('sidebar-section-label')) {
                item.hidden = query !== '';
                return;
            }

            item.hidden = query !== '' && !item.textContent.toLocaleLowerCase().includes(query);
        });
    });
});
