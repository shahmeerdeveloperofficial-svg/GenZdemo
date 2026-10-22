export interface SiteConfig {
  content: Record<string, any>;
  routes: Record<string, string>;
  forms: Record<string, string>;
  csrf: string;
  catalog: Record<string, any[]>;
  auth: { authenticated: boolean; name?: string; dashboard?: string };
  registration?: { branches?: string[] };
  recaptcha?: { enabled: boolean; siteKey: string };
}

declare global {
  interface Window { __GENZ_SITE__?: SiteConfig }
}

export const siteConfig: SiteConfig = window.__GENZ_SITE__ ?? {
  content: {}, routes: {}, forms: {}, csrf: '', catalog: {}, auth: { authenticated: false },
};

export function getContent<T>(path: string, fallback: T): T {
  const result = path.split('.').reduce<any>((value, key) => value?.[key], siteConfig.content);
  return result === undefined || result === null ? fallback : result as T;
}

export function siteText(section: string, key: string, fallback: string): string {
  const value = getContent<unknown>(`${section}.${key}`, fallback);
  return typeof value === 'string' || typeof value === 'number' ? String(value) : fallback;
}

/** CMS links accept web URLs, site-relative links and contact links only. */
export function safeUrl(value: string, fallback = '#'): string {
  if (!value || typeof value !== 'string') return fallback;
  const trimmed = value.trim();
  if (/^(?:https?:\/\/|mailto:|tel:|#|\/(?!\/))/i.test(trimmed)) return trimmed;
  return fallback;
}

export function siteAsset(path: string): string {
  if (!path) return '';
  return safeUrl(path.startsWith('/assets/') ? `/site${path}` : path, '');
}

export function route(name: string): string {
  const fallback: Record<string, string> = {
    home: '/', registration: '/student-registration', register: '/register', login: '/login',
    dashboard: '/student/dashboard', forgotPassword: '/forgot-password', contact: '/contact-us',
    blogs: '/blogs', events: '/events', courses: '/courses', consultations: '/consultations',
    academicPrograms: '/academic-programs', universities: '/universities-list', subjects: '/subject-list',
    scholarships: '/scholarship-list', about: '/about-us',
    privacy: '/privacy-policy', terms: '/terms-conditions', refund: '/refund-policy',
  };
  return safeUrl(siteConfig.routes[name] || fallback[name] || '/', '/');
}

export function contentLink(item: { route?: string; href?: string }): string {
  return item.route ? route(item.route) : safeUrl(item.href ?? '#');
}

export class SiteFormError extends Error {
  errors: Record<string, string[]>;
  constructor(message: string, errors: Record<string, string[]> = {}) {
    super(message);
    this.errors = errors;
  }
}

type RecaptchaClient = { ready: (callback: () => void) => void; execute: (key: string, options: {action:string}) => Promise<string> };
let captchaLoader: Promise<RecaptchaClient> | null = null;
async function recaptchaToken(action: string): Promise<string | undefined> {
  const config = siteConfig.recaptcha;
  if (!config?.enabled) return;
  if (!config.siteKey) throw new SiteFormError('Verification is unavailable. Please contact our team.');
  if (!captchaLoader) {
    captchaLoader = new Promise<RecaptchaClient>((resolve, reject) => {
      const fail = () => reject(new SiteFormError('Verification could not load. Check your connection and try again.'));
      const timer = window.setTimeout(fail, 15000);
      const ready = () => {
        const client = (window as Window & {grecaptcha?:RecaptchaClient}).grecaptcha;
        if (!client) { clearTimeout(timer); fail(); return; }
        client.ready(() => { clearTimeout(timer); resolve(client); });
      };
      if ((window as Window & {grecaptcha?:RecaptchaClient}).grecaptcha) { ready(); return; }
      const script = document.createElement('script');
      script.src = `https://www.google.com/recaptcha/api.js?render=${encodeURIComponent(config.siteKey)}`;
      script.async = true;
      script.onload = ready;
      script.onerror = () => { clearTimeout(timer); fail(); };
      document.head.appendChild(script);
    }).catch(error => { captchaLoader = null; throw error; });
  }
  const client = await captchaLoader;
  return await new Promise<string>((resolve, reject) => {
    const timer = window.setTimeout(() => reject(new SiteFormError('Verification timed out. Please try again.')), 15000);
    client.execute(config.siteKey, {action}).then(token => {
      clearTimeout(timer);
      if(token) resolve(token); else reject(new SiteFormError('Verification failed. Please try again.'));
    }, () => { clearTimeout(timer); reject(new SiteFormError('Verification failed. Please try again.')); });
  });
}

export async function postSiteForm(name: 'inquiry' | 'newsletter', payload: Record<string, unknown>) {
  const token = await recaptchaToken(name === 'inquiry' ? 'site_inquiry' : 'newsletter');
  const controller = new AbortController();
  const timeout = window.setTimeout(() => controller.abort(), 20000);
  try {
    const response = await fetch(siteConfig.forms[name] || `/site/${name === 'inquiry' ? 'inquiries' : 'newsletter'}`, {
      method: 'POST', credentials: 'same-origin', signal: controller.signal,
      headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': siteConfig.csrf },
      body: JSON.stringify({...payload, ...(token ? {recaptcha_token:token} : {})}),
    });
    const isJson = response.headers.get('content-type')?.includes('application/json');
    const data = isJson ? await response.json().catch(() => ({})) : {};
    if (!response.ok || response.redirected || !isJson || data.status !== true) {
      const errors = data.errors || {};
      const first = Object.values(errors).flat()[0];
      throw new SiteFormError(
        typeof first === 'string' ? first :
          data.message || (response.status === 419 ? 'Your session expired. Refresh this page and try again.' :
            response.status === 429 ? 'Please wait a moment before submitting again.' : 'Unable to submit. Please try again.'),
        errors,
      );
    }
    return data as { status: boolean; message: string };
  } catch (error) {
    if (controller.signal.aborted) throw new SiteFormError('The request timed out. Please try again.');
    throw error;
  } finally {
    clearTimeout(timeout);
  }
}
