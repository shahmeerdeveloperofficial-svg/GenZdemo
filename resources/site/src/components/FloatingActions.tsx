import React, { useEffect, useState } from 'react';
import { ArrowUp, MessageCircle, MessageSquare } from 'lucide-react';
import { getContent, safeUrl, siteText } from '../content';
export const FloatingActions: React.FC<{onOpenModal:()=>void}> = ({onOpenModal})=>{
  const [showTop,setShowTop]=useState(false);
  useEffect(()=>{const scroll=()=>setShowTop(window.scrollY>600);window.addEventListener('scroll',scroll,{passive:true});return()=>window.removeEventListener('scroll',scroll);},[]);
  if(!getContent('floatingActions.enabled',true))return null;
  const whatsapp=siteText('contact','whatsapp','').replace(/\D/g,'');
  return <div className="fixed bottom-5 right-4 sm:right-6 z-30 flex flex-col items-end gap-2">
    {showTop&&<button onClick={()=>window.__lenis?window.__lenis.scrollTo(0):window.scrollTo({top:0,behavior:'smooth'})} aria-label={siteText('floatingActions','backToTopLabel','Back to top')} className="w-10 h-10 flex items-center justify-center bg-white text-navy border border-navy/15 rounded-full shadow-lg"><ArrowUp size={18}/></button>}
    {whatsapp&&<a href={safeUrl(`https://wa.me/${whatsapp}`)} target="_blank" rel="noopener noreferrer" aria-label={siteText('floatingActions','whatsappLabel','Chat on WhatsApp')} className="w-12 h-12 flex items-center justify-center rounded-full bg-emerald-600 text-white shadow-lg"><MessageCircle size={23}/></a>}
    <button onClick={onOpenModal} className="flex items-center gap-2 px-4 py-3 rounded-full bg-navy text-white shadow-xl border border-cyan/30 hover:bg-cyan"><MessageSquare size={18}/><span className="text-xs font-bold">{siteText('floatingActions','inquiryLabel','Ask GenZ')}</span></button>
  </div>;
};
