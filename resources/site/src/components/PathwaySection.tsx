import React from 'react';
import { motion } from 'framer-motion';
import { Sparkles, ArrowRight, GraduationCap, ShieldCheck, TrendingUp, BookOpen } from 'lucide-react';
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
      accentColor: 'border-blue-500/30 hover:border-cyan',
      numberColor: 'text-blue-500/15 group-hover:text-cyan/25',
      badgeBg: 'bg-blue-500/10 text-cyan',
    },
    {
      step: '02',
      title: 'Visa Application',
      desc: 'Expert guidance to navigate the complexities of international student visas, CAS, financial docs, and interviews smoothly.',
      image: '/assets/images/pathway-visa.png',
      fallbackIcon: ShieldCheck,
      accentColor: 'border-rose-500/30 hover:border-rose-400',
      numberColor: 'text-rose-500/15 group-hover:text-rose-400/25',
      badgeBg: 'bg-rose-500/10 text-rose-500',
    },
    {
      step: '03',
      title: 'Career Advice',
      desc: 'Strategic insights and post-study work roadmap to architect high-income career pathways across the UK, USA, and Europe.',
      image: '/assets/images/pathway-career.png',
      fallbackIcon: TrendingUp,
      accentColor: 'border-cyan/30 hover:border-cyan-light',
      numberColor: 'text-cyan/15 group-hover:text-cyan/25',
      badgeBg: 'bg-cyan/10 text-cyan',
    },
    {
      step: '04',
      title: 'IELTS / PTE Prep',
      desc: 'Comprehensive English language coaching, mock exams, and test strategy to achieve target band scores with confidence.',
      image: '/assets/images/pathway-ielts.png',
      fallbackIcon: BookOpen,
      accentColor: 'border-amber-500/30 hover:border-amber-400',
      numberColor: 'text-amber-500/15 group-hover:text-amber-400/25',
      badgeBg: 'bg-amber-500/10 text-amber-500',
    },
  ];

  const steps = getContent('sections.pathway.steps', defaultSteps);
  const headline = getContent('sections.pathway.headline', 'Your Pathway to Academic & Global Career Success');
  const subheadline = getContent('sections.pathway.subheadline', 'Guiding scholars seamlessly from their first inquiry to landing at top universities abroad.');

  return (
    <section className="py-24 sm:py-32 bg-gradient-to-b from-[#F8FAFC] via-white to-[#F0F7FF] relative overflow-hidden" id="pathway">
      {/* Ambient background glow & grid */}
      <div className="absolute inset-0 bg-[radial-gradient(#0099ff10_1px,transparent_1px)] [background-size:28px_28px] pointer-events-none opacity-60" />
      <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[350px] bg-cyan/10 rounded-full blur-[140px] pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-20">
          <motion.div
            initial={{ opacity: 0, y: -10 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/10 border border-cyan/25 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-4 shadow-sm"
          >
            <Sparkles className="w-3.5 h-3.5 text-cyan animate-pulse" />
            <span>( Global Continuum Journey )</span>
          </motion.div>

          <motion.h2
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#0B2545] tracking-tight leading-[1.15] mb-5"
          >
            {headline}
          </motion.h2>

          <motion.p
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.2 }}
            className="text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed"
          >
            {subheadline}
          </motion.p>
        </div>

        {/* 4 Interactive Connected Pathway Cards */}
        <div className="relative">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 lg:gap-8 items-stretch relative z-10">
            {steps.map((item: any, idx: number) => {
              const FallbackIcon = defaultSteps[idx]?.fallbackIcon || GraduationCap;
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
                    className={`relative flex-1 bg-white/95 backdrop-blur-xl rounded-3xl p-7 sm:p-8 border-2 ${item.accentColor || 'border-cyan/30'} shadow-[0_10px_35px_rgba(11,37,69,0.06)] hover:shadow-[0_25px_60px_rgba(0,153,255,0.18)] transition-all duration-300 group cursor-pointer flex flex-col justify-between overflow-hidden`}
                  >
                    {/* Top Watermark Step Number */}
                    <div className="flex items-start justify-between mb-6">
                      <span className={`font-display font-black text-5xl sm:text-6xl transition-colors duration-300 ${item.numberColor || 'text-cyan/15'}`}>
                        {item.step}
                      </span>

                      {/* Icon Circle */}
                      <div className="w-16 h-16 rounded-2xl p-2.5 bg-slate-50 border border-slate-100 shadow-inner flex items-center justify-center group-hover:scale-110 group-hover:rotate-3 transition-transform duration-300">
                        {item.image ? (
                          <img
                            src={siteAsset(item.image)}
                            alt={item.title}
                            className="w-full h-full object-contain filter drop-shadow-sm"
                            loading="lazy"
                            onError={(e) => {
                              // Fallback if image fails
                              (e.target as HTMLElement).style.display = 'none';
                            }}
                          />
                        ) : (
                          <FallbackIcon className="w-7 h-7 text-cyan" />
                        )}
                      </div>
                    </div>

                    {/* Content */}
                    <div>
                      <h3 className="font-display font-extrabold text-xl sm:text-2xl text-[#0B2545] mb-3 group-hover:text-cyan transition-colors">
                        {item.title}
                      </h3>
                      <p className="text-slate-600 text-xs sm:text-sm leading-relaxed mb-6">
                        {item.desc}
                      </p>
                    </div>

                    {/* Bottom Action Pill */}
                    <div className="pt-4 border-t border-slate-100 flex items-center justify-between">
                      <span className="text-xs font-display font-bold uppercase tracking-wider text-cyan group-hover:text-cyan-light transition-colors flex items-center gap-1.5">
                        <span>Explore Guidance</span>
                        <ArrowRight className="w-3.5 h-3.5 transform group-hover:translate-x-1.5 transition-transform" />
                      </span>
                      <span className="w-2 h-2 rounded-full bg-cyan/40 group-hover:bg-cyan group-hover:scale-125 transition-all" />
                    </div>
                  </motion.div>

                  {/* Playful Squiggly Connecting Arrow between cards (Desktop Only) */}
                  {hasNext && (
                    <div
                      className={`hidden lg:block absolute -right-7 top-1/2 -translate-y-1/2 z-20 pointer-events-none w-14 h-14 ${
                        idx % 2 === 1 ? '-rotate-12 translate-y-2' : 'rotate-12 -translate-y-2'
                      }`}
                    >
                      <img
                        src={siteAsset('/assets/images/swirl-arrow.webp')}
                        alt=""
                        className="w-full h-full object-contain filter drop-shadow opacity-70 animate-pulse"
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
