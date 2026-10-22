import React from 'react';
import { Download, GraduationCap, MessageSquare, Sparkles } from 'lucide-react';
import { route, siteAsset, siteText } from '../content';
export const CallToAction: React.FC<{onOpenModal:()=>void}> = ({onOpenModal})=>{
  const prospectus=siteAsset(siteText('callToAction','prospectusUrl',''));
  const hasProspectus=!!prospectus&&/\.pdf(?:[?#].*)?$/i.test(prospectus);
  return <section className="py-20 bg-white"><div className="max-w-7xl mx-auto px-6"><div className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-navy via-navy-light to-navy-dark px-7 py-14 sm:p-16 text-center text-white shadow-2xl border border-cyan/15">
    <div className="absolute -top-32 -right-32 w-96 h-96 rounded-full bg-cyan/20 blur-3xl pointer-events-none"/>
    <div className="relative z-10 max-w-3xl mx-auto">
      <div className="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 text-cyan-light text-xs font-bold mb-6"><Sparkles size={15}/>{siteText('callToAction','badge','Start your next chapter')}</div>
      <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold leading-tight mb-6">{siteText('callToAction','title','Ready to begin your education journey?')}</h2>
      <p className="text-white/75 mb-9 max-w-2xl mx-auto leading-relaxed">{siteText('callToAction','description','Register as a student or speak with our team about the right pathway for you.')}</p>
      <div className="flex flex-wrap justify-center gap-3">
        <a href={route('registration')} className="inline-flex items-center gap-2 bg-cyan hover:bg-cyan-vibrant px-7 py-4 rounded-full text-sm font-bold"><GraduationCap size={18}/>{siteText('callToAction','registerLabel','Student registration')}</a>
        <button onClick={onOpenModal} className="inline-flex items-center gap-2 border border-white/25 bg-white/10 hover:bg-white/20 px-7 py-4 rounded-full text-sm font-bold"><MessageSquare size={18}/>{siteText('callToAction','inquiryLabel','Talk to an adviser')}</button>
        {hasProspectus&&<a href={prospectus} target="_blank" rel="noopener noreferrer" className="inline-flex items-center gap-2 border border-white/25 px-7 py-4 rounded-full text-sm font-bold"><Download size={18}/>{siteText('callToAction','prospectusLabel','Download prospectus')}</a>}
      </div>
    </div>
  </div></div></section>;
};
