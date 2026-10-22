import React, { useState } from 'react';
import { ArrowUp, ArrowUpRight, Mail, MapPin, Phone } from 'lucide-react';
import { contentLink, getContent, postSiteForm, safeUrl, siteAsset, siteText } from '../content';

type Link = {label:string;route?:string;href?:string};
export const Footer: React.FC<{onOpenModal:()=>void}> = ({onOpenModal}) => {
  const [email,setEmail]=useState('');
  const [website,setWebsite]=useState('');
  const [consent,setConsent]=useState(false);
  const [busy,setBusy]=useState(false);
  const [status,setStatus]=useState('');
  const [error,setError]=useState('');
  const branches=getContent<{name:string;address:string;phone:string}[]>('contact.branches',[]);
  const socials=getContent<{label:string;url:string}[]>('contact.socialLinks',[]);
  const background=siteAsset(siteText('footer','backgroundImage','/assets/images/hero-campus.jpg'));
  const logo=siteAsset(siteText('brand','logo','/assets/images/genz-logo.png'));
  const subscribe=async(e:React.FormEvent)=>{
    e.preventDefault();if(busy)return;setBusy(true);setError('');setStatus('');
    try {const result=await postSiteForm('newsletter',{email,consent,website});setStatus(siteText('footer','newsletterSuccess','').trim()||result.message||'Your subscription has been saved.');setEmail('');setConsent(false);}
    catch(err){setError(err instanceof Error?err.message:'Unable to subscribe. Please try again.');}
    finally{setBusy(false);}
  };
  return <footer className="relative overflow-hidden bg-navy-dark text-white pt-16 pb-8 border-t border-cyan/20">
    {background&&<img src={background} alt="" loading="lazy" className="absolute inset-0 w-full h-full object-cover opacity-[0.07] pointer-events-none"/>}
    <div className="max-w-7xl mx-auto px-6 relative z-10">
      {getContent('footer.newsletterEnabled',true)&&<div className="rounded-3xl border border-white/15 bg-white/5 p-6 sm:p-8 mb-14 grid lg:grid-cols-2 gap-6 items-center">
        <div><h2 className="font-display text-2xl font-bold mb-2">{siteText('footer','newsletterTitle','Stay connected with GenZ')}</h2><p className="text-white/65 text-sm">{siteText('footer','newsletterDescription','Receive education news and updates.')}</p></div>
        <form onSubmit={subscribe}>
          <label htmlFor="site-newsletter-email" className="sr-only">{siteText('footer','newsletterPlaceholder','Your email address')}</label>
          <div className="flex flex-col sm:flex-row gap-2"><input id="site-newsletter-email" name="email" type="email" autoComplete="email" required maxLength={190} placeholder={siteText('footer','newsletterPlaceholder','Your email address')} value={email} onChange={e=>setEmail(e.target.value)} className="min-w-0 flex-1 rounded-xl bg-white/10 text-white placeholder:text-white/50 border border-white/20 px-4 py-3 focus:ring-2 focus:ring-cyan outline-none"/><button disabled={busy} className="rounded-xl px-5 py-3 bg-cyan hover:bg-cyan-vibrant disabled:opacity-60 font-semibold text-sm">{siteText('footer',busy?'newsletterBusy':'newsletterButton',busy?'Subscribing…':'Subscribe')}</button></div>
          <label className="flex items-start gap-2 mt-3 text-xs text-white/75"><input type="checkbox" required checked={consent} onChange={e=>setConsent(e.target.checked)} className="mt-0.5 accent-cyan"/>{siteText('footer','newsletterConsent','I agree to receive email updates.')}</label>
          <div className="absolute -left-[10000px] w-px h-px overflow-hidden" aria-hidden="true"><label>Website<input name="website" value={website} onChange={e=>setWebsite(e.target.value)} tabIndex={-1} autoComplete="off"/></label></div>
          {status&&<p role="status" className="mt-3 text-xs text-emerald-300">{status}</p>}{error&&<p role="alert" className="mt-3 text-xs text-red-300">{error}</p>}
        </form>
      </div>}
      <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-10 pb-12">
        <div><a href={contentLink({route:'home'})}>{logo?<img src={logo} alt={siteText('brand','name','GenZ')} loading="lazy" className="w-40 h-24 object-contain bg-white rounded-xl p-2 mb-5"/>:<span className="block font-display text-2xl font-bold mb-5">{siteText('brand','name','GenZ')}</span>}</a><p className="text-sm text-white/65 leading-relaxed">{siteText('footer','description','Education guidance and student support in one place.')}</p><button onClick={onOpenModal} className="inline-flex items-center gap-2 text-cyan-light text-sm font-semibold mt-5">{siteText('navigation','inquiryLabel','Book a consultation')}<ArrowUpRight size={16}/></button></div>
        {([{heading:'linksHeading',key:'links'},{heading:'supportHeading',key:'supportLinks'}]).map(group=><div key={group.key}><h3 className="font-semibold mb-5 text-sm">{siteText('footer',group.heading,'Explore')}</h3><ul className="space-y-3">{getContent<Link[]>(`footer.${group.key}`,[]).map((link,index)=><li key={index}><a href={contentLink(link)} className="text-sm text-white/65 hover:text-cyan-light">{link.label}</a></li>)}</ul></div>)}
        <div><h3 className="font-semibold mb-5 text-sm">{siteText('footer','contactHeading','Get in touch')}</h3><div className="space-y-4">
          {branches.map((branch,index)=><div key={index} className="border border-white/10 bg-white/5 p-3 rounded-xl"><p className="text-xs font-semibold text-cyan-light mb-2">{branch.name}</p><p className="flex items-start gap-2 text-xs text-white/65"><MapPin size={14} className="shrink-0"/>{branch.address}</p>{branch.phone&&<a href={safeUrl(`tel:${branch.phone}`)} className="block mt-2 text-xs">{branch.phone}</a>}</div>)}
          {siteText('contact','email','')&&<a className="flex items-center gap-2 text-sm break-all" href={safeUrl(`mailto:${siteText('contact','email','')}`)}><Mail size={16} className="shrink-0 text-cyan"/>{siteText('contact','email','')}</a>}
          {siteText('contact','phone','')&&<a className="flex items-center gap-2 text-sm" href={safeUrl(`tel:${siteText('contact','phone','')}`)}><Phone size={16} className="text-cyan"/>{siteText('contact','phone','')}</a>}
          <div className="flex flex-wrap gap-2">{socials.map((social,index)=><a key={index} href={safeUrl(social.url)} target="_blank" rel="noopener noreferrer" className="text-xs border border-white/20 rounded-full px-3 py-2 hover:bg-cyan/20">{social.label}</a>)}</div>
        </div></div>
      </div>
      <div className="border-t border-white/10 pt-6 flex flex-wrap items-center justify-between gap-5 text-xs text-white/60">
        <span>© {new Date().getFullYear()} {siteText('brand','name','GenZ')}. {siteText('footer','copyright','All rights reserved.')}</span>
        <div className="flex flex-wrap gap-4">{getContent<Link[]>('footer.legalLinks',[]).map((link,index)=><a key={index} href={contentLink(link)} className="hover:text-cyan-light">{link.label}</a>)}</div>
        <button onClick={()=>window.__lenis?window.__lenis.scrollTo(0):window.scrollTo({top:0,behavior:'smooth'})} className="inline-flex items-center gap-2 hover:text-cyan-light">{siteText('footer','backToTop','Back to top')}<ArrowUp size={15}/></button>
      </div>
    </div>
  </footer>;
};
