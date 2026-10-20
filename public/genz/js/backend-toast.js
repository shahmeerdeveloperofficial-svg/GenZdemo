(function () {
    'use strict';

    var labels = window.GenzToastLabels || {};
    var defaults = {
        success: { title: labels.success || 'Saved', duration: 4200, icon: '\u2713' },
        info: { title: labels.info || 'Information', duration: 5200, icon: '\u2139' },
        warning: { title: labels.warning || 'Please note', duration: 6500, icon: '\u26a0' },
        error: { title: labels.error || 'Something went wrong', duration: 8000, icon: '\u2715' },
        loading: { title: labels.loading || 'Working', duration: 0, icon: '' }
    };
    var active = new Map();
    var sequence = 0;
    var host = document.getElementById('genz-toast-host');

    if (!host) {
        host = document.createElement('div');
        host.id = 'genz-toast-host';
        host.className = 'genz-toast-stack';
        host.setAttribute('role', 'region');
        host.setAttribute('aria-label', labels.notifications || 'Notifications');
        document.body.appendChild(host);
    }
    if (document.querySelector('.zMain-wrap')) host.classList.add('genz-toast-stack--portal');

    function asText(value) {
        if (value === null || value === undefined) return '';
        return typeof value === 'string' ? value : String(value);
    }

    function normalize(type, options) {
        if (typeof options === 'string' || typeof options === 'number') {
            options = { message: asText(options) };
        }
        options = options || {};
        var preset = defaults[type] || defaults.info;
        return {
            type: type in defaults ? type : 'info',
            title: asText(options.title || preset.title),
            message: asText(options.message),
            duration: options.duration === undefined ? preset.duration : Math.max(0, Number(options.duration) || 0),
            action: options.action || null
        };
    }

    function makeButton(item, action) {
        var button = document.createElement('button');
        button.type = 'button';
        button.className = 'genz-toast__action';
        button.textContent = asText(action.label || labels.open || 'Open');
        button.addEventListener('click', function () {
            try {
                if (typeof action.onClick === 'function') action.onClick();
            } finally {
                if (action.dismiss !== false) dismiss(item.id);
            }
        });
        return button;
    }

    function pause(item) {
        if (!item.interval) return;
        item.remaining = Math.max(0, item.remaining - (Date.now() - item.startedAt));
        window.clearInterval(item.interval);
        item.interval = null;
        item.progress.style.transform = 'scaleX(' + (item.duration ? item.remaining / item.duration : 0) + ')';
    }

    function start(item) {
        if (!item.duration || item.paused || item.interval) return;
        item.startedAt = Date.now();
        item.interval = window.setInterval(function () {
            var remaining = Math.max(0, item.remaining - (Date.now() - item.startedAt));
            item.progress.style.transform = 'scaleX(' + (remaining / item.duration) + ')';
            if (remaining <= 0) dismiss(item.id);
        }, 50);
    }

    function resetTimer(item, duration) {
        if (item.interval) window.clearInterval(item.interval);
        item.interval = null;
        item.duration = duration;
        item.remaining = duration;
        item.progress.hidden = duration <= 0;
        item.progress.style.transform = 'scaleX(1)';
        start(item);
    }

    function render(item, options) {
        var next = normalize(options.type || item.type, options);
        var wasVisible = item.card.classList.contains('is-visible');
        item.type = next.type;
        item.card.className = 'genz-toast genz-toast--' + next.type;
        if (wasVisible) item.card.classList.add('is-visible');
        item.card.setAttribute('role', next.type === 'error' ? 'alert' : 'status');
        item.card.setAttribute('aria-live', next.type === 'error' ? 'assertive' : 'polite');
        item.icon.textContent = defaults[next.type].icon;
        item.title.textContent = next.title;
        item.message.textContent = next.message;
        item.message.hidden = !next.message;
        item.actions.replaceChildren();

        if (next.action && (typeof next.action.onClick === 'function' || next.action.href)) {
            if (next.action.href && !next.action.onClick) {
                try {
                    var target = new URL(next.action.href, window.location.href);
                    if (target.origin === window.location.origin) {
                        var link = document.createElement('a');
                        link.className = 'genz-toast__action';
                        link.href = target.href;
                        link.textContent = asText(next.action.label || labels.open || 'Open');
                        item.actions.appendChild(link);
                    }
                } catch (error) {
                    // Invalid action URLs are deliberately omitted.
                }
            } else {
                item.actions.appendChild(makeButton(item, next.action));
            }
        }

        resetTimer(item, next.duration);
    }

    function createItem(id) {
        var card = document.createElement('article');
        card.className = 'genz-toast';

        var icon = document.createElement('span');
        icon.className = 'genz-toast__icon';
        icon.setAttribute('aria-hidden', 'true');

        var content = document.createElement('div');
        content.className = 'genz-toast__content';

        var title = document.createElement('strong');
        title.className = 'genz-toast__title';

        var message = document.createElement('p');
        message.className = 'genz-toast__message';

        var actions = document.createElement('div');
        actions.className = 'genz-toast__actions';
        content.append(title, message, actions);

        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'genz-toast__close';
        close.setAttribute('aria-label', labels.close || 'Close notification');
        close.textContent = asText(labels.closeButton || 'Close');

        var progress = document.createElement('span');
        progress.className = 'genz-toast__progress';
        progress.setAttribute('aria-hidden', 'true');

        card.append(icon, content, close, progress);
        var item = { id: id, type: 'info', card: card, icon: icon, title: title, message: message, actions: actions, close: close, progress: progress, interval: null, duration: 0, remaining: 0, startedAt: 0, paused: false };
        close.addEventListener('click', function () { dismiss(id); });
        card.addEventListener('pointerenter', function () { item.paused = true; pause(item); });
        card.addEventListener('pointerleave', function () { item.paused = false; start(item); });
        card.addEventListener('focusin', function () { item.paused = true; pause(item); });
        card.addEventListener('focusout', function () {
            window.setTimeout(function () {
                if (!card.contains(document.activeElement)) {
                    item.paused = false;
                    start(item);
                }
            }, 0);
        });
        return item;
    }

    function show(type, options) {
        if (typeof type === 'object') {
            options = type;
            type = options.type || 'info';
        }
        var settings = normalize(type || 'info', options);
        var duplicateKey = settings.type + '|' + settings.title + '|' + settings.message;
        var duplicate = Array.from(active.values()).find(function (item) {
            return item.duplicateKey === duplicateKey && Date.now() - item.createdAt < 1800;
        });
        if (duplicate) return duplicate.id;

        while (active.size >= 5) dismiss(active.keys().next().value);
        var id = options && options.id ? asText(options.id) : 'genz-toast-' + (++sequence);
        var item = createItem(id);
        item.duplicateKey = duplicateKey;
        item.createdAt = Date.now();
        active.set(id, item);
        host.appendChild(item.card);
        render(item, settings);
        window.requestAnimationFrame(function () { item.card.classList.add('is-visible'); });
        return id;
    }

    function dismiss(id) {
        var item = active.get(id);
        if (!item) return;
        if (item.interval) window.clearInterval(item.interval);
        item.interval = null;
        active.delete(id);
        item.card.classList.remove('is-visible');
        item.card.classList.add('is-leaving');
        window.setTimeout(function () { item.card.remove(); }, 220);
    }

    function update(id, options) {
        var item = active.get(id);
        if (!item) return show(options.type || 'info', options);
        render(item, options);
        return id;
    }

    var api = {
        show: show,
        success: function (message, options) { return show('success', typeof message === 'object' ? message : Object.assign({}, options, { message: message })); },
        info: function (message, options) { return show('info', typeof message === 'object' ? message : Object.assign({}, options, { message: message })); },
        warning: function (message, options) { return show('warning', typeof message === 'object' ? message : Object.assign({}, options, { message: message })); },
        error: function (message, options) { return show('error', typeof message === 'object' ? message : Object.assign({}, options, { message: message })); },
        loading: function (message, options) { return show('loading', Object.assign({}, options, { message: message })); },
        update: update,
        dismiss: dismiss,
        clear: function () { Array.from(active.keys()).forEach(dismiss); }
    };

    window.GenzToast = api;

    // Keep existing CRM/LMS toastr.success/error calls on the same branded UI.
    if (window.toastr && !window.toastr.__genzToastBridge) {
        ['success', 'info', 'warning', 'error'].forEach(function (type) {
            window.toastr[type] = function (message, title, options) {
                var settings = options || {};
                if (title && typeof title === 'object') {
                    settings = title;
                    title = '';
                }
                if ((message === '' || message === null || message === undefined) && typeof title === 'string') {
                    message = title;
                    title = '';
                }
                return api.show(type, {
                    title: asText(title),
                    message: asText(message),
                    duration: settings.timeOut === undefined ? undefined : Number(settings.timeOut)
                });
            };
        });
        window.toastr.__genzToastBridge = true;
    }
}());
