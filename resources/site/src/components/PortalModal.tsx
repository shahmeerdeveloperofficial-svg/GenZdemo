import React, { useEffect, useRef, useState } from 'react';
import { X, GraduationCap, Users, ArrowRight, Lock } from 'lucide-react';
import { route, safeUrl, siteConfig, siteText } from '../content';

interface PortalModalProps {
  isOpen: boolean; onClose: () => void; initialRole?: 'student' | 'crm' | 'faculty' | 'parent';
}

export const PortalModal: React.FC<PortalModalProps> = ({ isOpen, onClose, initialRole = 'student' }) => {
  const [role,setRole] = useState<'student'|'crm'>('student');
  const closeRef=useRef<HTMLButtonElement>(null);
  useEffect(() => { setRole(initialRole === 'crm' || initialRole === 'faculty' ? 'crm' : 'student'); }, [initialRole,isOpen]);
  useEffect(() => {
    if (!isOpen) return;
    const previous = document.activeElement as HTMLElement|null;
    closeRef.current?.focus();
    const listener=(event: KeyboardEvent) => { if(event.key==='Escape') onClose(); };
    document.addEventListener('keydown',listener);
    return () => { document.removeEventListener('keydown',listener); previous?.focus(); };
  },[isOpen,onClose]);
  if(!isOpen) return null;
  return <div className="fixed inset-0 z-[70] p-4 flex items-center justify-center bg-navy-dark/85 backdrop-blur-md" role="dialog" aria-modal="true" aria-labelledby="portal-title" onClick={onClose} data-lenis-prevent>
    <div className="relative max-w-xl w-full max-h-[90dvh] overflow-y-auto bg-navy-dark text-white p-7 sm:p-10 rounded-3xl border border-cyan/30 shadow-2xl" onClick={e=>e.stopPropagation()}>
      <button ref={closeRef} onClick={onClose} className="absolute top-5 right-5 p-2 rounded-full hover:bg-white/10" aria-label={siteText('portal','closeLabel','Close portal')}><X size={20}/></button>
      <Lock className="text-cyan mb-4" size={30}/>
      <h2 id="portal-title" className="font-display text-3xl font-bold pr-7">{siteText('portal','title','Your GenZ portal')}</h2>
      <p className="text-white/70 mt-3 text-sm leading-relaxed">{siteText('portal','description','Sign in using your existing account to access your dashboard.')}</p>
      {siteConfig.auth.authenticated ? <div className="mt-7">
        <p className="text-white/70 mb-4">{siteText('portal','authenticatedLabel','Signed in as')} <strong className="text-white">{siteConfig.auth.name}</strong></p>
        <a href={safeUrl(siteConfig.auth.dashboard || route('dashboard'))} className="flex justify-center items-center gap-2 bg-cyan px-5 py-3 rounded-xl font-semibold">{siteText('portal','dashboardLabel','Open my dashboard')}<ArrowRight size={18}/></a>
      </div> : <>
        <div className="grid grid-cols-2 gap-3 my-6">
          {(['student','crm'] as const).map(key => <button key={key} onClick={()=>setRole(key)} className={`p-4 rounded-xl border text-sm flex items-center justify-center gap-2 ${role===key?'bg-cyan/20 border-cyan text-cyan-light':'bg-white/5 border-white/15'}`}>
            {key==='student'?<GraduationCap size={20}/>:<Users size={20}/>}
            {siteText('portal',key==='student'?'studentLabel':'teamLabel',key==='student'?'Student portal':'Team & CRM')}
          </button>)}
        </div>
        <p className="text-white/75 text-sm mb-6 min-h-12">{siteText('portal',role==='student'?'studentDescription':'teamDescription','Access your authorized GenZ account.')}</p>
        <a href={route('login')} className="flex justify-center items-center gap-2 bg-cyan px-5 py-3 rounded-xl font-semibold hover:bg-cyan-vibrant">{siteText('portal','loginLabel','Continue to login')}<ArrowRight size={18}/></a>
        <a href={route('registration')} className="block mt-3 text-center border border-white/20 rounded-xl px-5 py-3 text-sm hover:bg-white/10">{siteText('portal','registerLabel','New student registration')}</a>
        <a href={route('register')} className="block mt-4 text-center text-sm text-cyan-light hover:text-white">{siteText('portal','createAccountLabel','Create student account')}</a>
        <a href={route('forgotPassword')} className="block mt-5 text-center text-sm text-white/60 hover:text-cyan-light">{siteText('portal','forgotLabel','Forgot password?')}</a>
      </>}
    </div>
  </div>;
};
