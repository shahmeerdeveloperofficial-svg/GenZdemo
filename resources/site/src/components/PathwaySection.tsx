import React from 'react';
import { motion } from 'framer-motion';
import { Sparkles, ArrowRight, GraduationCap, ShieldCheck, TrendingUp, BookOpen, Star } from 'lucide-react';
import { getContent, siteAsset } from '../content';

interface PathwaySectionProps {
  onOpenModal?: () => void;
}

export const PathwaySection: React.FC<PathwaySectionProps> = ({ onOpenModal }) => {
  const defaultSteps = [
    {
      step: '01',
      title: 'Student Counseling',
      desc: 'Tailored advice to align your academic aspirations and strengths with premier global university opportunities.',
      image: '/assets/images/pathway-counseling.png',
      fallbackIcon: GraduationCap,
      color: 'emerald',
      topBar: 'bg-gradient-to-r from-emerald-400 via-teal-500 to-emerald-600',
      borderStyle: 'border-2 border-emerald-200/90 hover:border-emerald-500',
      numberColor: 'text-emerald-500/20 group-hover:text-emerald-500/40',
      badgeClass: 'bg-emerald-100 text-emerald-800 border border-emerald-200',
      iconBox: 'bg-emerald-50 text-emerald-600 border border-emerald-200 group-hover:bg-emerald-500 group-hover:text-white',
      shadowHover: 'hover:shadow-[0_20px_45px_rgba(16,185,129,0.22)]',
    },
    {
      step: '02',
      title: 'Visa Application',
      desc: 'Expert guidance to navigate the complexities of international student visas, CAS, financial docs, and interviews.',
      image: '/assets/images/pathway-visa.png',
      fallbackIcon: ShieldCheck,
      color: 'rose',
      topBar: 'bg-gradient-to-r from-rose-400 via-pink-500 to-rose-600',
      borderStyle: 'border-2 border-rose-200/90 hover:border-rose-500',
      numberColor: 'text-rose-500/20 group-hover:text-rose-500/40',
      badgeClass: 'bg-rose-100 text-rose-800 border border-rose-200',
      iconBox: 'bg-rose-50 text-rose-600 border border-rose-200 group-hover:bg-rose-500 group-hover:text-white',
      shadowHover: 'hover:shadow-[0_20px_45px_rgba(244,63,94,0.22)]',
    },
    {
      step: '03',
      title: 'Career Advice',
      desc: 'Strategic insights and post-study work roadmap to architect high-income career pathways across the UK, USA, and Europe.',
      image: '/assets/images/pathway-career.png',
      fallbackIcon: TrendingUp,
      color: 'purple',
      topBar: 'bg-gradient-to-r from-purple-400 via-indigo-500 to-purple-600',
      borderStyle: 'border-2 border-purple-200/90 hover:border-purple-500',
      numberColor: 'text-purple-500/20 group-hover:text-purple-500/40',
      badgeClass: 'bg-purple-100 text-purple-800 border border-purple-200',
      iconBox: 'bg-purple-50 text-purple-600 border border-purple-200 group-hover:bg-purple-600 group-hover:text-white',
      shadowHover: 'hover:shadow-[0_20px_45px_rgba(147,51,234,0.22)]',
    },
    {
      step: '04',
      title: 'IELTS / PTE Prep',
      desc: 'Comprehensive English language coaching, mock exams, and test strategy to achieve target band scores with confidence.',
      image: '/assets/images/pathway-ielts.png',
      fallbackIcon: BookOpen,
      color: 'amber',
      topBar: 'bg-gradient-to-r from-amber-400 via-orange-400 to-amber-500',
      borderStyle: 'border-2 border-amber-200/90 hover:border-amber-500',
      numberColor: 'text-amber-500/20 group-hover:text-amber-500/40',
      badgeClass: 'bg-amber-100 text-amber-900 border border-amber-200',
      iconBox: 'bg-amber-50 text-amber-700 border border-amber-200 group-hover:bg-amber-500 group-hover:text-slate-950',
      shadowHover: 'hover:shadow-[0_20px_45px_rgba(245,158,11,0.22)]',
    },
  ];

  const steps = getContent('sections.pathway.steps', defaultSteps);
  const headline = getContent('sections.pathway.headline', 'Your Pathway to Academic & Global Career Success');
  const subheadline = getContent(
    'sections.pathway.subheadline',
    'Guiding scholars seamlessly from their first inquiry to landing at top universities abroad.'
  );

  return (
    <section className="py-24 sm:py-32 bg-gradient-to-b from-white via-[#FAF7F2] to-[#F5F8FA] relative overflow-hidden" id="pathway">
      {/* Decorative ambient multi-color light glows */}
      <div className="absolute top-1/4 left-10 w-96 h-96 bg-emerald-300/15 rounded-full blur-[140px] pointer-events-none" />
      <div className="absolute top-1/2 right-10 w-96 h-96 bg-rose-300/15 rounded-full blur-[140px] pointer-events-none" />
      <div className="absolute bottom-10 left-1/3 w-[30rem] h-[30rem] bg-amber-300/15 rounded-full blur-[160px] pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-20">
          <motion.div
            initial={{ opacity: 0, y: -10 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gradient-to-r from-emerald-500/15 via-rose-500/15 to-amber-500/15 border border-slate-300/60 text-slate-800 text-xs font-display font-bold uppercase tracking-wider mb-4 shadow-sm"
          >
            <Sparkles className="w-3.5 h-3.5 text-amber-600 animate-pulse" />
            <span>( Global Continuum Journey )</span>
          </motion.div>

          <motion.h2
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#0B2545] tracking-tight leading-[1.16] mb-5"
          >
            Your Pathway to Academic &{' '}
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 via-purple-600 to-amber-600">
              Global Career Success
            </span>
          </motion.h2>

          <motion.p
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.2 }}
            className="text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed max-w-2xl mx-auto"
          >
            {subheadline}
          </motion.p>
        </div>

        {/* 4 Connected Pathway Cards with 4 Distinct Colors */}
        <div className="relative">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 items-stretch relative z-10">
            {steps.map((item: any, idx: number) => {
              const FallbackIcon = defaultSteps[idx]?.fallbackIcon || GraduationCap;
              const config = defaultSteps[idx] || defaultSteps[0];
              const hasNext = idx < steps.length - 1;

              return (
                <div key={item.step || idx} className="relative flex flex-col">
                  <motion.div
                    initial={{ opacity: 0, y: 30 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ delay: idx * 0.12, duration: 0.55 }}
                    whileHover={{ y: -8, transition: { duration: 0.25 } }}
                    onClick={onOpenModal}
                    className={`relative flex-1 bg-white rounded-3xl p-7 sm:p-8 ${config.borderStyle} ${config.shadowHover} transition-all duration-300 group cursor-pointer flex flex-col justify-between overflow-hidden`}
                  >
                    {/* Top Vibrant Color Strip */}
                    <div className={`absolute top-0 left-0 right-0 h-2.5 ${config.topBar}`} />

                    {/* Top Watermark Step Number & Icon */}
                    <div>
                      <div className="flex items-start justify-between mb-5 mt-1">
                        <span className={`font-display font-black text-5xl sm:text-6xl transition-colors duration-300 ${config.numberColor}`}>
                          {item.step}
                        </span>

                        {/* Icon Circle */}
                        <div className={`w-16 h-16 rounded-2xl p-2.5 ${config.iconBox} shadow-sm flex items-center justify-center group-hover:scale-110 group-hover:rotate-3 transition-all duration-300`}>
                          {item.image ? (
                            <img
                              src={siteAsset(item.image)}
                              alt={item.title}
                              className="w-full h-full object-contain filter drop-shadow-sm"
                              loading="lazy"
                              onError={(e) => {
                                (e.target as HTMLElement).style.display = 'none';
                              }}
                            />
                          ) : (
                            <FallbackIcon className="w-7 h-7" />
                          )}
                        </div>
                      </div>

                      {/* Title & Desc */}
                      <span className={`inline-block text-[10px] font-extrabold uppercase tracking-widest px-2.5 py-1 rounded-full mb-3 ${config.badgeClass}`}>
                        Phase 0{idx + 1}
                      </span>
                      <h3 className="font-display font-black text-xl sm:text-2xl text-slate-900 group-hover:text-navy transition-colors mb-2.5">
                        {item.title}
                      </h3>
                      <p className="text-slate-600 text-xs sm:text-sm leading-relaxed mb-6">
                        {item.desc}
                      </p>
                    </div>

                    {/* Bottom Action Trigger */}
                    <div className="pt-4 border-t border-slate-100 flex items-center justify-between text-xs font-bold text-slate-800 group-hover:text-navy transition-colors">
                      <span className="font-display uppercase tracking-wider">Explore Services</span>
                      <div className="w-7 h-7 rounded-full bg-slate-100 group-hover:bg-slate-900 group-hover:text-white flex items-center justify-center transition-colors">
                        <ArrowRight className="w-3.5 h-3.5 transform group-hover:translate-x-0.5 transition-transform" />
                      </div>
                    </div>
                  </motion.div>

                  {/* Connecting Squiggly Arrow (Desktop Only) */}
                  {hasNext && (
                    <div className="hidden lg:block absolute -right-6 top-1/2 -translate-y-1/2 z-20 pointer-events-none w-10">
                      <img
                        src={siteAsset('/assets/images/swirl-arrow.webp')}
                        alt="Path connection"
                        className="w-10 h-auto opacity-70 transform rotate-12"
                        loading="lazy"
                        onError={(e) => {
                          (e.target as HTMLElement).style.display = 'none';
                        }}
                      />
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        </div>
      </div>
    </section>
  );
};
