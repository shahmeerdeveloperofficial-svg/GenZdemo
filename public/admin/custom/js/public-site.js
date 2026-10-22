(function () {
    'use strict';
    const root = document.getElementById('public-site-editor');
    if (!root) return;
    const boot = JSON.parse(document.getElementById('public-site-data').textContent);
    const form = document.getElementById('public-site-form');
    const host = document.getElementById('public-site-fields');
    const status = document.getElementById('public-site-status');
    const saveButton = document.getElementById('public-site-save');
    const clone = value => JSON.parse(JSON.stringify(value));
    let content = clone(boot.content);
    let dirty = false;
    let saving = false;
    let uploadCount = 0;
    let serial = 0;
    let openGroups = new Set();
    let revision = 0;

    function element(tag, className, text) {
        const node = document.createElement(tag);
        if (className) node.className = className;
        if (text !== undefined) node.textContent = text;
        return node;
    }
    function title(value) {
        return String(value).replace(/([a-z0-9])([A-Z])/g, '$1 $2').replace(/[_-]+/g, ' ')
            .replace(/\b\w/g, character => character.toUpperCase()).replace(/\bCta\b/g, 'Button')
            .replace(/\bCeo\b/g, 'CEO').replace(/\bUrl\b/g, 'URL').replace(/\bFaq\b/g, 'FAQ');
    }
    function get(path) { return path.reduce((value, key) => value && value[key], content); }
    function set(path, value) {
        let target = content;
        path.slice(0, -1).forEach((key, index) => {
            if (!target[key] || typeof target[key] !== 'object') target[key] = typeof path[index + 1] === 'number' ? [] : {};
            target = target[key];
        });
        target[path[path.length - 1]] = value;
        revision++;
        dirty = true;
        announce('You have unsaved changes.');
    }
    function announce(message, kind) {
        status.textContent = message;
        status.dataset.kind = kind || '';
    }
    function action(text, callback, className) {
        const button = element('button', 'site-small-button ' + (className || ''), text);
        button.type = 'button';
        button.addEventListener('click', callback);
        return button;
    }
    function safePreview(value) {
        return typeof value === 'string' && /^(\/(?!\/)|https?:\/\/)/i.test(value) && !/[\\\r\n]/.test(value);
    }
    function blank(template, schema) {
        if (schema && schema.options && schema.options.length) return schema.options[0];
        if (Array.isArray(template)) return [];
        if (template && typeof template === 'object') {
            return Object.fromEntries(Object.entries(template).map(([key, value]) => [
                key, /^(id|key)$/i.test(key) ? 'item-' + Date.now().toString(36) + '-' + (++serial) : blank(value, schema && schema.fields ? schema.fields[key] : null)
            ]));
        }
        if (typeof template === 'boolean') return true;
        if (typeof template === 'number') return 0;
        return '';
    }
    function labelForItem(value, index) {
        if (value && typeof value === 'object') {
            return value.title || value.name || value.label || value.studentName || value.id || 'Item ' + (index + 1);
        }
        return 'Item ' + (index + 1);
    }
    function render() {
        const opened = new Set(Array.from(host.querySelectorAll('details[open]')).map(node => node.dataset.group));
        openGroups = opened;
        const firstRender = !host.children.length;
        host.replaceChildren();
        Object.entries(boot.schema.fields || {}).forEach(([key, schema], index) => {
            const group = element('details', 'site-group');
            group.dataset.group = key;
            group.open = opened.has(key) || (firstRender && index === 0);
            const heading = element('summary', '', title(key));
            group.append(heading);
            const body = element('div', 'site-group-body');
            if (key === 'sections') body.append(element('p', 'site-help', 'Enable or hide each homepage section and change its order.'));
            if (/brand|header|footer/i.test(key)) body.append(element('p', 'site-help', 'Use /student-registration for the existing student registration form and /login for account access.'));
            body.append(renderNode([key], schema, title(key), true));
            group.append(body);
            host.append(group);
        });
    }
    function renderNode(path, schema, name, groupRoot) {
        const value = get(path);
        if (schema.type === 'object') {
            if (!Object.keys(schema.fields || {}).length) return element('div');
            if (path[0] === 'sections' && path.length === 2) {
                const section = element('details', 'site-group site-wide');
                section.dataset.group = path.join('.');
                section.open = openGroups.has(section.dataset.group);
                section.append(element('summary', '', name));
                const body = element('div', 'site-group-body');
                const fields = element('div', 'site-fields');
                Object.entries(schema.fields || {}).forEach(([key, child]) => fields.append(renderNode(path.concat(key), child, title(key), false)));
                body.append(fields);
                section.append(body);
                return section;
            }
            const container = element(groupRoot ? 'div' : 'fieldset', groupRoot ? '' : 'site-object site-wide');
            if (!groupRoot) container.append(element('legend', '', name));
            const fields = element('div', 'site-fields');
            Object.entries(schema.fields || {}).forEach(([key, child]) => {
                fields.append(renderNode(path.concat(key), child, title(key), false));
            });
            container.append(fields);
            return container;
        }
        if (schema.type === 'list') {
            const wrapper = element('fieldset', 'site-object site-wide');
            wrapper.append(element('legend', '', name));
            const list = element('div', 'site-list');
            const items = Array.isArray(value) ? value : [];
            items.forEach((item, index) => {
                const card = element('div', 'site-list-item');
                const heading = element('div', 'site-item-heading');
                heading.append(element('strong', '', labelForItem(item, index)));
                const actions = element('div', 'site-item-actions');
                const move = offset => {
                    const next = items.slice();
                    [next[index], next[index + offset]] = [next[index + offset], next[index]];
                    set(path, next);
                    render();
                };
                const up = action('↑', () => move(-1));
                up.title = 'Move up';
                up.setAttribute('aria-label', 'Move item up');
                up.disabled = index === 0;
                const down = action('↓', () => move(1));
                down.title = 'Move down';
                down.setAttribute('aria-label', 'Move item down');
                down.disabled = index === items.length - 1;
                const remove = action('Remove', () => {
                    set(path, items.filter((_, itemIndex) => index !== itemIndex));
                    render();
                }, 'site-remove');
                actions.append(up, down, remove);
                heading.append(actions);
                card.append(heading, renderNode(path.concat(index), schema.item, 'Value', true));
                list.append(card);
            });
            if (!items.length) list.append(element('p', 'site-help', 'No items. Add an item to begin.'));
            const add = action('+ Add item', () => {
                set(path, items.concat([blank(schema.template, schema.item)]));
                render();
            }, 'site-add');
            add.disabled = items.length >= 150;
            list.append(add);
            wrapper.append(list);
            return wrapper;
        }
        const wrapper = element('div', 'site-field');
        const key = String(path[path.length - 1]);
        const fieldPath = path.join('.');
        const inputId = 'site-field-' + (++serial);
        const label = element('label', '', key === 'route' ? 'Page destination' : fieldPath === 'brand.seoSource' ? 'Homepage SEO source' : name);
        label.htmlFor = inputId;
        const input = element(schema.options && schema.options.length ? 'select' : (schema.type === 'string' && (/description|message|quote|copy|body|detail|excerpt|paragraph|intro|text/i.test(key) || String(value || '').length > 180) ? 'textarea' : 'input'), 'form-control genz-field');
        input.id = inputId;
        input.dataset.path = 'content.' + path.join('.');
        if (schema.type === 'boolean') {
            input.type = 'checkbox';
            input.className = 'form-check-input m-0';
            input.checked = Boolean(value);
            const check = element('div', 'site-check');
            check.append(input, element('span', '', /enabled|visible|show/i.test(key) ? 'Show on website' : name));
            wrapper.append(label, check);
            input.addEventListener('change', () => set(path, input.checked));
            return wrapper;
        }
        if (schema.type === 'number') {
            input.type = 'number';
            input.step = 'any';
            input.min = '-1000000000';
            input.max = '1000000000';
            input.value = value === undefined ? 0 : value;
            input.addEventListener('input', () => set(path, input.value === '' ? 0 : Number(input.value)));
        } else {
            if (input.tagName === 'SELECT') {
                schema.options.forEach(option => {
                    const optionLabel = fieldPath === 'brand.seoSource'
                        ? (option === 'backend' ? 'Existing SEO Settings' : 'Custom title and description below')
                        : option === '' ? 'Use a custom link' : title(option);
                    const node = element('option', '', optionLabel);
                    node.value = option;
                    input.append(node);
                });
            }
            if (input.tagName === 'TEXTAREA') {
                input.rows = Math.min(8, Math.max(3, String(value || '').split('\n').length));
                wrapper.classList.add('site-wide');
            } else if (input.tagName !== 'SELECT') input.type = 'text';
            input.maxLength = 20000;
            input.value = value == null ? '' : value;
            input.addEventListener('input', () => set(path, input.value));
        }
        wrapper.append(label, input);
        if (fieldPath === 'brand.seoSource') wrapper.append(element('p', 'site-help', 'Existing SEO Settings uses the Home entry on the SEO page. Custom uses Page title and Description in this Brand section.'));
        if (fieldPath === 'brand.pageTitle' || fieldPath === 'brand.description') wrapper.append(element('p', 'site-help', 'Used for homepage SEO when Homepage SEO source is set to Custom.'));
        if (key === 'route') wrapper.append(element('p', 'site-help', 'Choose a page, or choose Custom link and enter its URL below when that field is available.'));
        if (schema.format) {
            input.maxLength = 2048;
            wrapper.append(element('p', 'site-help', 'Website link, /page path or #section. Leave blank when optional.'));
        }
        if (schema.format === 'media') {
            const row = element('div', 'site-media-row');
            const fileInput = element('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/jpeg,image/png,image/webp,image/gif,application/pdf';
            fileInput.hidden = true;
            const upload = action('Upload image / PDF', () => fileInput.click());
            const preview = element('img', 'site-preview');
            preview.alt = 'Current image';
            preview.loading = 'lazy';
            function refreshPreview() {
                preview.hidden = !safePreview(input.value) || /\.pdf(?:[?#]|$)/i.test(input.value);
                if (!preview.hidden) preview.src = input.value.startsWith('/assets/') ? '/site' + input.value : input.value;
            }
            preview.addEventListener('error', () => { preview.hidden = true; });
            refreshPreview();
            input.addEventListener('change', refreshPreview);
            fileInput.addEventListener('change', async () => {
                if (!fileInput.files[0]) return;
                upload.disabled = true;
                upload.textContent = 'Uploading…';
                uploadCount++;
                saveButton.disabled = true;
                const body = new FormData();
                body.append('file', fileInput.files[0]);
                try {
                    const response = await fetch(root.dataset.mediaUrl, {
                        method: 'POST', credentials: 'same-origin',
                        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': form.elements._token.value },
                        body
                    });
                    const result = await response.json();
                    if (!response.ok) throw new Error(result.errors ? Object.values(result.errors).flat().join('\n') : result.message || 'Upload failed.');
                    input.value = result.url;
                    set(path, result.url);
                    refreshPreview();
                    announce(result.message, 'success');
                } catch (error) { announce(error.message || 'Could not upload this file. Please try again.', 'error'); }
                finally {
                    upload.disabled = false;
                    upload.textContent = 'Upload image / PDF';
                    uploadCount--;
                    saveButton.disabled = saving || uploadCount > 0;
                    fileInput.value = '';
                }
            });
            row.append(upload, fileInput, preview);
            wrapper.append(row, element('p', 'site-help', 'JPG, PNG, WebP, GIF or PDF. Maximum 10 MB. Save changes after uploading.'));
        }
        return wrapper;
    }
    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (saving || uploadCount) return;
        saving = true;
        const submittedRevision = revision;
        saveButton.disabled = true;
        saveButton.textContent = 'Saving…';
        root.querySelectorAll('.site-invalid').forEach(node => node.classList.remove('site-invalid'));
        announce('Saving website changes…');
        try {
            const response = await fetch(root.dataset.saveUrl, {
                method: 'POST', credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': form.elements._token.value },
                body: JSON.stringify({ content })
            });
            const result = await response.json();
            if (!response.ok) {
                const messages = [];
                Object.entries(result.errors || {}).forEach(([path, errors]) => {
                    const input = Array.from(root.querySelectorAll('[data-path]')).find(node => node.dataset.path === path);
                    if (input) {
                        input.classList.add('site-invalid');
                        let group = input.parentElement;
                        while (group && group !== root) {
                            if (group.tagName === 'DETAILS') group.open = true;
                            group = group.parentElement;
                        }
                    }
                    messages.push(title(path.replace(/^content\./, '').replace(/\./g, ' / ')) + ': ' + errors.join(' '));
                });
                throw new Error(messages.join('\n') || result.message || 'Could not save website changes.');
            }
            if (revision === submittedRevision) {
                content = result.content;
                dirty = false;
                announce(result.message, 'success');
                render();
            } else announce('Saved. You also have newer edits; save again to publish those.');
        } catch (error) { announce(error.message || 'Could not save. Please try again.', 'error'); }
        finally {
            saving = false;
            saveButton.disabled = uploadCount > 0;
            saveButton.textContent = 'Save website changes';
        }
    });
    window.addEventListener('beforeunload', event => {
        if (!dirty) return;
        event.preventDefault();
        event.returnValue = '';
    });
    render();
}());
