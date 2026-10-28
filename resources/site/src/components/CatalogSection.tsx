import React from 'react';
import {
  ArrowUpRight,
  Compass,
  Globe2,
  FileCheck2,
  PlaneTakeoff,
  GraduationCap,
  Award,
  ShieldCheck,
  CheckCircle2,
  Sparkles,
  ArrowRight,
  HelpCircle
} from 'lucide-react';
import { getContent, route, safeUrl, siteAsset, siteConfig, siteText } from '../content';

export const CatalogSection: React.FC = () => {
  const services = siteConfig.catalog.services || [];
  const countries = siteConfig.catalog.countries || [];

  if (!services.length && !countries.length) return null;

  // Curated metadata and visual presets for each service
  const getServiceMetadata = (title: string, index: number) => {
    const lower = title.toLowerCase();

    if (lower.includes('document')) {
      return {
        icon: FileCheck2,
        tag: 'Verification & SOP',
        badgeColor: 'bg-emerald-50 text-emerald-800 border-emerald-200',
        iconBg: 'bg-gradient-to-br from-emerald-500 to-teal-600',
        hoverBorder: 'hover:border-emerald-400',
        defaultDesc: 'Comprehensive drafting, notarization, and verification of SOPs, transcripts, and letters of recommendation tailored to target universities.',
      };
    }
    if (lower.includes('departure')) {
      return {
        icon: PlaneTakeoff,
        tag: 'Overseas Transition',
        badgeColor: 'bg-cyan-50 text-cyan-800 border-cyan-200',
        iconBg: 'bg-gradient-to-br from-cyan to-blue-600',
        hoverBorder: 'hover:border-cyan',
        defaultDesc: 'Seamless briefings on international student housing, health insurance, SIM cards, overseas banking, and flight reception.',
      };
    }
    if (lower.includes('professor') || lower.includes('matching')) {
      return {
        icon: GraduationCap,
        tag: 'Research & Faculty',
        badgeColor: 'bg-purple-50 text-purple-800 border-purple-200',
        iconBg: 'bg-gradient-to-br from-purple-500 to-indigo-600',
        hoverBorder: 'hover:border-purple-400',
        defaultDesc: 'Direct liaison with tenured university professors, thesis supervisors, and funded laboratory research groups across leading institutions.',
      };
    }
    if (lower.includes('scholarship')) {
      return {
        icon: Award,
        tag: 'Merit & Financial Aid',
        badgeColor: 'bg-amber-50 text-amber-900 border-amber-200',
        iconBg: 'bg-gradient-to-br from-amber-400 via-amber-500 to-yellow-500',
        hoverBorder: 'hover:border-amber-400',
        defaultDesc: 'Specialized mentorship targeting full-tuition waivers, Erasmus Mundus fellowships, and global governmental academic awards.',
      };
    }
    if (lower.includes('counseling') || lower.includes('admission')) {
      return {
        icon: Compass,
        tag: 'Course & Campus Fit',
        badgeColor: 'bg-blue-50 text-blue-800 border-blue-200',
        iconBg: 'bg-gradient-to-br from-blue-600 to-indigo-700',
        hoverBorder: 'hover:border-blue-400',
        defaultDesc: 'One-on-one portfolio assessment to match your career strengths with elite universities across the UK, USA, Canada, and Europe.',
      };
    }
    if (lower.includes('visa')) {
      return {
        icon: ShieldCheck,
        tag: 'Immigration & Compliance',
        badgeColor: 'bg-rose-50 text-rose-800 border-rose-200',
        iconBg: 'bg-gradient-to-br from-rose-500 via-pink-500 to-red-600',
        hoverBorder: 'hover:border-rose-400',
        defaultDesc: 'Rigorous CAS verification, embassy file compilation, financial proof certification, and mock interview drills for 99%+ success.',
      };
    }

    const fallbacks = [
      { icon: Compass, tag: 'Academic Advisory', badgeColor: 'bg-cyan-50 text-cyan-800 border-cyan-200', iconBg: 'bg-gradient-to-br from-cyan to-blue-600' },
      { icon: Award, tag: 'Global Opportunity', badgeColor: 'bg-amber-50 text-amber-900 border-amber-200', iconBg: 'bg-gradient-to-br from-amber-500 to-orange-500' },
      { icon: ShieldCheck, tag: 'Student Success', badgeColor: 'bg-emerald-50 text-emerald-800 border-emerald-200', iconBg: 'bg-gradient-to-br from-emerald-500 to-teal-600' }
    ];

    const chosen = fallbacks[index % fallbacks.length];
    return {
      ...chosen,
      hoverBorder: 'hover:border-cyan',
      defaultDesc: 'Comprehensive international educational advisory designed to guide scholars seamlessly towards premier university placements.'
    };
  };

  return (
    <section id="serviceSection" className="py-24 sm:py-32 bg-gradient-to-b from-[#F9FBFC] via-white to-[#FAF7F2] relative overflow-hidden">
      {/* Background ambient multi-color light glows */}
      <div className="absolute top-10 left-10 w-96 h-96 bg-cyan/10 rounded-full blur-[140px] pointer-events-none" />
      <div className="absolute bottom-10 right-10 w-96 h-96 bg-amber-400/10 rounded-full blur-[140px] pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Services Showcase */}
        {services.length > 0 && getContent('catalog.showServices', true) && (
          <div id="services">
            {/* Header with Tag and CTA */}
            <div className="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-16">
              <div>
                <div className="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-cyan/15 border border-cyan/30 text-cyan text-xs font-display font-extrabold uppercase tracking-wider mb-4 shadow-2xs">
                  <Sparkles className="w-3.5 h-3.5 text-cyan" />
                  <span>{siteText('catalog', 'servicesBadge', 'Support for your next step')}</span>
                </div>
                <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-navy tracking-tight leading-tight">
                  {siteText('catalog', 'servicesTitle', 'Explore Our Services')}
                  <span className="text-cyan">.</span>
                </h2>
                <p className="mt-3 text-slate-600 text-sm sm:text-base max-w-2xl leading-relaxed">
                  {siteText('catalog', 'servicesDescription', 'End-to-end guidance and verified institutional support designed to accelerate your global study journey.')}
                </p>
              </div>

              <a
                href={route('consultations')}
                className="shrink-0 px-6 py-3.5 rounded-full bg-white hover:bg-cyan text-navy hover:text-white border-2 border-slate-200/90 hover:border-cyan shadow-[0_6px_20px_rgba(11,37,69,0.08)] hover:shadow-[0_10px_30px_rgba(0,153,255,0.3)] transition-all font-display font-bold text-xs sm:text-sm uppercase tracking-wider flex items-center gap-2 hover:-translate-y-0.5 cursor-pointer"
              >
                <span>{siteText('catalog', 'consultationLabel', 'Find an Adviser')}</span>
                <ArrowUpRight size={18} />
              </a>
            </div>

            {/* 6 High-Impact Modern Service Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
              {services.map((item, index) => {
                const title = item.title || item.name || 'Advisory Service';
                const meta = getServiceMetadata(title, index);
                const Icon = meta.icon;
                const hasCustomDesc = item.description && item.description.trim() !== '' && item.description.toLowerCase() !== 'details';
                const description = hasCustomDesc ? item.description : meta.defaultDesc;

                return (
                  <a
                    key={item.id || index}
                    href={safeUrl(item.url, route('contact'))}
                    className={`group bg-white rounded-3xl p-7 sm:p-8 border-2 border-slate-200/80 shadow-[0_10px_30px_rgba(11,37,69,0.07)] hover:shadow-[0_20px_45px_rgba(0,153,255,0.22)] ${meta.hoverBorder} hover:-translate-y-2 transition-all duration-300 flex flex-col justify-between relative overflow-hidden cursor-pointer`}
                  >
                    {/* Top Ambient Glow on Hover */}
                    <div className="absolute top-0 right-0 w-32 h-32 bg-cyan/5 rounded-full blur-2xl group-hover:bg-cyan/15 transition-all pointer-events-none" />

                    <div>
                      {/* Top Row: Icon Squircle Box & Category Pill */}
                      <div className="flex items-center justify-between gap-3 mb-6">
                        <div className={`w-14 h-14 rounded-2xl ${meta.iconBg} text-white shadow-md flex items-center justify-center group-hover:scale-110 group-hover:rotate-3 transition-all duration-300`}>
                          <Icon className="w-7 h-7" />
                        </div>

                        <span className={`text-[10px] font-extrabold uppercase tracking-widest px-3 py-1 rounded-full border ${meta.badgeColor}`}>
                          {meta.tag}
                        </span>
                      </div>

                      {/* Service Title */}
                      <h3 className="font-display text-xl sm:text-2xl font-black text-navy group-hover:text-cyan transition-colors mb-3 leading-snug">
                        {title}
                      </h3>

                      {/* Descriptive Paragraph */}
                      <p className="text-slate-600 text-xs sm:text-sm leading-relaxed mb-6">
                        {description}
                      </p>
                    </div>

                    {/* Bottom Action Strip with Arrow */}
                    <div className="pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-display font-bold text-navy group-hover:text-cyan transition-colors">
                      <span className="flex items-center gap-1.5">
                        <span>{siteText('catalog', 'detailsLabel', 'Explore Service Details')}</span>
                      </span>
                      <div className="w-8 h-8 rounded-full bg-cyan/10 group-hover:bg-cyan group-hover:text-white flex items-center justify-center transition-all group-hover:scale-110 shadow-2xs">
                        <ArrowUpRight size={16} />
                      </div>
                    </div>
                  </a>
                );
              })}
            </div>
          </div>
        )}

        {/* Global Destinations Grid */}
        {countries.length > 0 && getContent('catalog.showCountries', true) && (
          <div id="destinations" className={services.length ? 'mt-24 pt-16 border-t border-slate-200/80' : ''}>
            <div className="mb-10 text-center max-w-2xl mx-auto">
              <h3 className="font-display text-2xl sm:text-3xl font-extrabold text-navy tracking-tight mb-2">
                {siteText('catalog', 'countriesTitle', 'Find Your Global Study Destination')}
              </h3>
              <p className="text-sm text-slate-500">
                {siteText('catalog', 'countriesDescription', 'Explore available destinations and their worldwide education pathways.')}
              </p>
            </div>

            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
              {countries.map((item) => (
                <a
                  key={item.id}
                  href={safeUrl(item.url, route('academicPrograms'))}
                  className="flex items-center gap-3 p-4 bg-white border-2 border-slate-200/80 rounded-2xl shadow-xs hover:border-cyan hover:shadow-md hover:-translate-y-1 transition-all group cursor-pointer"
                >
                  {item.image ? (
                    <img
                      src={siteAsset(item.image)}
                      alt=""
                      loading="lazy"
                      className="w-10 h-10 object-contain rounded-xl shadow-2xs"
                    />
                  ) : (
                    <div className="w-10 h-10 rounded-xl bg-cyan/10 flex items-center justify-center text-cyan group-hover:bg-cyan group-hover:text-white transition-colors">
                      <Globe2 size={22} />
                    </div>
                  )}
                  <span className="font-bold text-xs sm:text-sm text-navy group-hover:text-cyan transition-colors flex-1 truncate">
                    {item.name || item.title}
                  </span>
                  <ArrowUpRight size={16} className="text-cyan group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform shrink-0" />
                </a>
              ))}
            </div>
          </div>
        )}
      </div>
    </section>
  );
};
