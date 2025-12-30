// Shared utilities to reduce code duplication
export const showToast = (type, text, timeout = 3200) => {
    const toastRoot = document.getElementById('toastRoot');
    if (!toastRoot || !text) return;
    
    const el = document.createElement('div');
    el.className = 'pointer-events-auto flex items-center gap-2 px-4 py-2 rounded-lg shadow-lg text-sm';
    
    let base = 'bg-gray-900 text-white';
    if (type === 'success') base = 'bg-emerald-600 text-white';
    else if (type === 'error') base = 'bg-red-600 text-white';
    else if (type === 'warning') base = 'bg-amber-500 text-black';
    else if (type === 'info') base = 'bg-blue-600 text-white';
    
    el.className += ' ' + base;
    
    const span = document.createElement('span');
    span.textContent = text;
    el.appendChild(span);
    toastRoot.appendChild(el);
    
    setTimeout(() => { el.style.opacity = '1'; }, 10);
    setTimeout(() => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(-4px)';
        setTimeout(() => { 
            if (el.parentNode) el.parentNode.removeChild(el); 
        }, 300);
    }, timeout);
};

export const setupFlashMessages = () => {
    const flashMessages = Array.isArray(window.__flash) ? window.__flash : [];
    flashMessages.forEach(m => showToast(m.type, m.text));
};

export const copyToClipboard = (text, successMessage = 'Copied to clipboard') => {
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => { 
        showToast('success', successMessage); 
    });
};

export const isIp = (value) => {
    const ipPattern = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/;
    if (!ipPattern.test(value)) return false;
    
    const parts = value.split('.');
    return parts.every(part => {
        const num = parseInt(part, 10);
        return num >= 0 && num <= 255 && part === num.toString();
    });
};

export const slideUp = (element, duration = 300) => {
    element.style.transition = `height ${duration}ms ease, opacity ${duration}ms ease`;
    element.style.height = `${element.offsetHeight}px`;
    element.offsetHeight; // Trigger reflow
    element.style.height = '0';
    element.style.opacity = '0';
    element.style.overflow = 'hidden';
    setTimeout(() => { element.style.display = 'none'; }, duration);
};

export const slideDown = (element, duration = 300) => {
    element.style.display = 'block';
    element.style.transition = `height ${duration}ms ease, opacity ${duration}ms ease`;
    element.style.height = '0';
    element.style.opacity = '0';
    element.offsetHeight; // Trigger reflow
    const height = element.scrollHeight;
    element.style.height = `${height}px`;
    element.style.opacity = '1';
    setTimeout(() => { element.style.height = ''; }, duration);
};