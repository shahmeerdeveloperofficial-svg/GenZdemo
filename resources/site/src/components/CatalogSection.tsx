import React from 'react';
import { ArrowUpRight, Compass, Globe2 } from 'lucide-react';
import { getContent, route, safeUrl, siteAsset, siteConfig, siteText } from '../content';
export const CatalogSection: React.FC = ()=>{
  const services=siteConfig.catalog.services || [];
  const countries=siteConfig.catalog.countries || [];
  if(!services.length&&!countries.length)return null;
  return <section id="serviceSection" className="py-20 sm:py-24 bg-white">
    <div className="max-w-7xl mx-auto px-6">
      {services.length>0&&getContent('catalog.showServices',true)&&<div id="services">
        <p className="text-cyan font-bold uppercase tracking-wider text-xs mb-3">{siteText('catalog','servicesBadge','Support for your next step')}</p>
        <div className="flex flex-wrap justify-between items-end gap-5 mb-8"><div><h2 className="font-display text-3xl sm:text-4xl font-extrabold">{siteText('catalog','servicesTitle','Explore our services')}</h2><p className="mt-3 text-slate-500 text-sm max-w-2xl">{siteText('catalog','servicesDescription','Discover guidance and support available through GenZ.')}</p></div><a href={route('consultations')} className="flex items-center gap-2 text-sm text-cyan font-bold">{siteText('catalog','consultationLabel','Find an adviser')}<ArrowUpRight size={18}/></a></div>
        <div className="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">{services.map(item=><a key={item.id} href={safeUrl(item.url,route('contact'))} className="group rounded-2xl border border-navy/10 bg-slate-50 p-6 hover:shadow-lg hover:border-cyan/40 transition-all">
          {item.image?<img src={siteAsset(item.image)} alt="" loading="lazy" className="w-12 h-12 object-contain mb-5"/>:<Compass className="text-cyan mb-5" size={32}/>}
          <h3 className="font-display text-xl font-bold mb-3 group-hover:text-cyan">{item.title||item.name}</h3><p className="text-sm text-slate-600 leading-relaxed line-clamp-3">{item.description}</p><span className="inline-flex items-center gap-2 text-xs font-bold text-cyan mt-5">{siteText('catalog','detailsLabel','Explore service')}<ArrowUpRight size={16}/></span>
        </a>)}</div>
      </div>}
      {countries.length>0&&getContent('catalog.showCountries',true)&&<div id="destinations" className={services.length?'mt-16':''}>
        <h2 className="font-display text-3xl font-extrabold mb-3">{siteText('catalog','countriesTitle','Find your study destination')}</h2><p className="text-sm text-slate-500 mb-7">{siteText('catalog','countriesDescription','Explore available destinations and their education pathways.')}</p>
        <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">{countries.map(item=><a key={item.id} href={safeUrl(item.url,route('academicPrograms'))} className="flex items-center gap-3 p-4 bg-white border border-navy/10 rounded-xl hover:border-cyan hover:shadow-md transition-all">{item.image?<img src={siteAsset(item.image)} alt="" loading="lazy" className="w-10 h-10 object-contain rounded-lg"/>:<Globe2 size={28} className="text-cyan"/>}<span className="font-semibold text-sm flex-1">{item.name||item.title}</span><ArrowUpRight size={16} className="text-cyan"/></a>)}</div>
      </div>}
    </div>
  </section>;
};
