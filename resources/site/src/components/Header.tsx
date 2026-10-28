import React, { useState } from 'react';
import { Menu, X, ArrowUpRight, GraduationCap, Lock } from 'lucide-react';
import { contentLink, getContent, route, siteAsset, siteConfig, siteText } from '../content';

interface HeaderProps {
  onOpenModal: () => void;
  onOpenPortalModal?: (role: 'student' | 'crm') => void;
}

export const Header: React.FC<HeaderProps> = ({ onOpenModal, onOpenPortalModal }) => {
  const [open, setOpen] = useState(false);
  const links = getContent<{label: string; route?: string; href?: string}[]>('navigation.links', [])
    .filter(link => typeof link?.label === 'string' && !!link.label.trim() && !!(link.route || link.href));
  const dashboard = siteConfig.auth.authenticated;
  const logo = siteAsset(siteText('brand', 'logo', '/assets/images/genz-logo.png'));
  return <>
    <div className="genz-public-topbar xl:hidden bg-gradient-to-r from-[#06162b] via-[#0b2d55] to-[#06162b] text-white text-xs border-b border-cyan/30">
      <div className="max-w-[1400px] mx-auto px-4 sm:px-7 py-2.5 flex items-center justify-between gap-3">
        <a href={route('registration')} className="min-w-0 inline-flex items-center gap-2.5 text-cyan-light font-semibold hover:text-white transition-colors">
          <span className="relative flex w-2.5 h-2.5 shrink-0"><span className="absolute inline-flex h-full w-full rounded-full bg-cyan opacity-60 animate-ping"/><span className="relative inline-flex w-2.5 h-2.5 rounded-full bg-cyan"/></span>
          <span className="truncate">{siteText('navigation', 'announcement', 'Admissions are open')}</span>
        </a>
        <div className="flex shrink-0 items-center gap-2 sm:gap-3">
          <button onClick={onOpenModal} className="hidden sm:inline-flex items-center rounded-full px-3.5 py-1.5 font-semibold text-white/90 hover:bg-white/10 hover:text-cyan-light transition-colors">{siteText('navigation', 'inquiryLabel', 'Book a consultation')}</button>
          <button onClick={() => onOpenPortalModal?.('student')} className="inline-flex items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1.5 font-semibold text-white/95 hover:border-cyan/50 hover:bg-white/10 transition-colors whitespace-nowrap">
            <Lock size={13} className="text-cyan-light" />{siteText('navigation', dashboard ? 'dashboardLabel' : 'portalLabel', dashboard ? 'My dashboard' : 'Login / Portal')}
          </button>
        </div>
      </div>
    </div>
    <header className="genz-public-nav sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 shadow-[0_8px_24px_rgba(11,37,69,0.07)] backdrop-blur-xl">
      <nav className="genz-public-nav-shell max-w-[1312px] min-h-[82px] mx-auto px-4 sm:px-7 flex items-center justify-between gap-3 lg:gap-5" aria-label={siteText('navigation', 'menuLabel', 'Main navigation')}>
        <a href={route('home')} className="shrink-0 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan">
          {logo ? <img src={logo} alt={siteText('brand', 'name', 'GenZ')} className="w-32 sm:w-36 2xl:w-40 h-[64px] object-contain" /> : <span className="font-display text-2xl font-bold">{siteText('brand','name','GenZ')}</span>}
        </a>
        <div className="genz-public-nav-links hidden xl:flex min-w-0 flex-1 items-center justify-center gap-0.5 2xl:gap-1">
          {links.map((link, index) => {
            const href = contentLink(link);
            const current = (link.route && route(link.route) === window.location.pathname) || (link.href?.startsWith('#') && window.location.hash === link.href);
            return <a key={`${href}-${index}`} href={href} aria-current={current ? 'page' : undefined} className={`genz-public-nav-link relative shrink-0 whitespace-nowrap rounded-full px-2 2xl:px-2.5 py-3 text-[12px] 2xl:text-[13px] font-semibold transition-all duration-200 hover:bg-cyan/10 hover:text-[#007bd1] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan ${current ? 'bg-cyan/10 text-[#007bd1]' : 'text-slate-700'}`}>{link.label}</a>;
          })}
        </div>
        <div className="flex shrink-0 items-center gap-2">
          <a href={route('registration')} className="hidden sm:inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-navy to-[#0e4b8b] px-3 2xl:px-4 py-3 text-xs font-bold text-white shadow-md shadow-navy/15 transition-all hover:-translate-y-0.5 hover:from-cyan hover:to-blue-600 hover:shadow-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan focus-visible:ring-offset-2">
            <GraduationCap size={17}/><span>{siteText('navigation', 'registerLabel', 'Student registration')}</span><ArrowUpRight size={15}/>
          </a>
          <button className="xl:hidden p-2.5 text-navy rounded-xl border border-slate-200 bg-white shadow-sm hover:border-cyan hover:text-cyan transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-cyan" aria-label={siteText('navigation','menuLabel','Open navigation')} aria-expanded={open} aria-controls="site-mobile-navigation" onClick={() => setOpen(!open)}>
            {open ? <X size={23}/> : <Menu size={23}/>}
          </button>
        </div>
      </nav>
      {open && <div id="site-mobile-navigation" className="xl:hidden max-h-[75vh] overflow-y-auto border-t border-slate-100 bg-white/95 px-4 sm:px-7 py-5 shadow-xl">
        <div className="grid grid-cols-2 gap-2.5">{links.map((link,index) => <a key={index} href={contentLink(link)} onClick={() => setOpen(false)} className="flex min-h-12 items-center rounded-xl border border-slate-100 bg-slate-50/80 px-4 py-3 text-sm font-semibold text-slate-700 transition-colors hover:border-cyan/30 hover:bg-cyan/5 hover:text-[#007bd1]">{link.label}</a>)}</div>
        <div className="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2.5">
          <a href={route('registration')} className="flex min-h-12 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-navy to-[#0e4b8b] p-3 text-center text-sm font-bold text-white"><GraduationCap size={17}/>{siteText('navigation','registerLabel','Student registration')}<ArrowUpRight size={15}/></a>
          <button onClick={() => { setOpen(false); onOpenModal(); }} className="min-h-12 rounded-xl border border-cyan/25 bg-cyan/5 p-3 text-sm font-bold text-[#007bd1] hover:bg-cyan/10">{siteText('navigation','inquiryLabel','Book a consultation')}</button>
        </div>
      </div>}
    </header>
  </>;
};
