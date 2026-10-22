(function () {
    'use strict';

    function isDownloadLink(link, url) {
        if (link.hasAttribute('data-native-download')) return false;
        if (link.hasAttribute('download')) return true;

        return /(?:^|\/)download(?:[-_/]|\/|$)/i.test(url.pathname)
            || /(?:^|\/)export(?:[-_/]|\/|$)/i.test(url.pathname);
    }

    function getFilename(response, url, link) {
        var disposition = response.headers.get('Content-Disposition') || '';
        var encodedName = disposition.match(/filename\*\s*=\s*UTF-8''([^;]+)/i);
        if (encodedName) {
            try { return decodeURIComponent(encodedName[1].replace(/^['"]|['"]$/g, '')); } catch (error) {}
        }

        var plainName = disposition.match(/filename\s*=\s*(?:"([^"]+)"|([^;]+))/i);
        if (plainName) return (plainName[1] || plainName[2]).trim();
        if (link.download) return link.download;

        var lastPart = url.pathname.split('/').filter(Boolean).pop() || 'download';
        try { return decodeURIComponent(lastPart); } catch (error) { return lastPart; }
    }

    function showDownloadError(message) {
        if (window.GenzToast && typeof window.GenzToast.error === 'function') {
            window.GenzToast.error(message);
        }
    }

    document.addEventListener('click', async function (event) {
        if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;

        var link = event.target.closest && event.target.closest('a[href]');
        if (!link) return;

        var url;
        try { url = new URL(link.href, window.location.href); } catch (error) { return; }
        if (!isDownloadLink(link, url)) return;
        if (url.protocol === 'http:' || url.protocol === 'https:') {
            if (url.origin !== window.location.origin) return;
        } else if (!link.hasAttribute('download') || !['blob:', 'data:'].includes(url.protocol)) {
            return;
        }

        event.preventDefault();
        event.stopImmediatePropagation();

        // Open synchronously from the click so popup blockers allow it. The tab
        // is closed as soon as the file download has been handed to the browser.
        var temporaryTab = window.open('about:blank', '_blank');
        if (temporaryTab) {
            try { temporaryTab.document.title = 'Starting download'; } catch (error) {}
        }

        try {
            var response = await fetch(url.href, {
                credentials: 'same-origin',
                cache: 'no-store',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!response.ok) throw new Error('The file could not be downloaded. Please try again.');
            if (response.redirected || (response.headers.get('Content-Type') || '').toLowerCase().includes('text/html')) {
                throw new Error('The download link expired or your session needs to be refreshed.');
            }

            var blob = await response.blob();
            if (!blob.size) throw new Error('The downloaded file is empty. Please try again.');

            var objectUrl = URL.createObjectURL(blob);
            var downloadLink = document.createElement('a');
            downloadLink.href = objectUrl;
            downloadLink.download = getFilename(response, url, link);
            downloadLink.setAttribute('data-native-download', '');
            downloadLink.style.display = 'none';
            document.body.appendChild(downloadLink);
            downloadLink.click();
            downloadLink.remove();

            window.setTimeout(function () { URL.revokeObjectURL(objectUrl); }, 60000);
            if (temporaryTab && !temporaryTab.closed) window.setTimeout(function () { temporaryTab.close(); }, 250);
        } catch (error) {
            if (temporaryTab && !temporaryTab.closed) temporaryTab.close();
            showDownloadError(error.message || 'The file could not be downloaded. Please try again.');
        }
    }, true);
})();
