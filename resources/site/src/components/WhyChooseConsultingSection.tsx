import React from 'react';
import { motion } from 'framer-motion';
import {
  Sparkles,
  CheckCircle2,
  GraduationCap,
  Award,
  FileCheck2,
  PlaneTakeoff,
  ArrowRight,
  ShieldCheck,
  Globe2,
  Star
} from 'lucide-react';
import { getContent, siteAsset } from '../content';

interface WhyChooseConsultingProps {
  onOpenModal?: () => void;
}

export const WhyChooseConsultingSection: React.FC<WhyChooseConsultingProps> = ({ onOpenModal }) => {
  const defaultPillars = [
    {
      title: 'University Admissions Support',
      desc: 'Expert portfolio & course alignment to match your strengths with world-renowned universities.',
      icon: GraduationCap,
      color: 'indigo',
      iconBg: 'bg-gradient-to-br from-indigo-500 to-purple-600',
      iconColor: 'text-white',
      hoverBorder: 'hover:border-indigo-400',
      tagBg: 'bg-indigo-50 text-indigo-700 border-indigo-200',
      cardHover: 'hover:shadow-[0_12px_32px_rgba(99,102,241,0.18)] hover:bg-indigo-50/20',
    },
    {
      title: 'Scholarship Guidance',
      desc: 'Dedicated mentorship to unlock merit fellowships and need-based financial awards across top campuses.',
      icon: Award,
      color: 'amber',
      iconBg: 'bg-gradient-to-br from-amber-400 via-amber-500 to-yellow-500',
      iconColor: 'text-slate-950',
      hoverBorder: 'hover:border-amber-400',
      tagBg: 'bg-amber-50 text-amber-900 border-amber-200',
      cardHover: 'hover:shadow-[0_12px_32px_rgba(245,158,11,0.2)] hover:bg-amber-50/20',
    },
    {
      title: 'Application & Visa Assistance',
      desc: 'Impeccable statement of purpose curation, CAS verification, and guaranteed interview mastery.',
      icon: FileCheck2,
      color: 'emerald',
      iconBg: 'bg-gradient-to-br from-emerald-500 to-teal-600',
      iconColor: 'text-white',
      hoverBorder: 'hover:border-emerald-400',
      tagBg: 'bg-emerald-50 text-emerald-800 border-emerald-200',
      cardHover: 'hover:shadow-[0_12px_32px_rgba(16,185,129,0.18)] hover:bg-emerald-50/20',
    },
    {
      title: 'Travel & Accommodation',
      desc: 'Stress-free pre-departure guidance, on-campus dormitory placement, and overseas airport reception.',
      icon: PlaneTakeoff,
      color: 'rose',
      iconBg: 'bg-gradient-to-br from-rose-500 via-pink-500 to-orange-500',
      iconColor: 'text-white',
      hoverBorder: 'hover:border-rose-400',
      tagBg: 'bg-rose-50 text-rose-800 border-rose-200',
      cardHover: 'hover:shadow-[0_12px_32px_rgba(244,63,94,0.18)] hover:bg-rose-50/20',
    },
  ];

  const tag = getContent('sections.whyChoose.tag', 'Study Abroad & Unlock Global Opportunities');
  const headline = getContent('sections.whyChoose.headline', 'Why Choose GenZ Future Consulting for Your Studies?');
  const narrative = getContent(
    'sections.whyChoose.narrative',
    'GenZ Consultant is dedicated to helping students achieve their academic ambitions worldwide. With years of experience in educational consultancy, executive coaching, and direct partnerships with premier international universities, we provide personalized support tailored to your dreams.'
  );

  return (
    <section className="py-24 sm:py-32 bg-gradient-to-b from-[#FFFDF9] via-[#FAF7F2] to-[#F5F8FA] relative overflow-hidden" id="consulting-advantage">
      {/* Decorative ambient multi-color light glows */}
      <div className="absolute top-10 right-0 w-[28rem] h-[28rem] bg-amber-300/15 rounded-full blur-[140px] pointer-events-none" />
      <div className="absolute bottom-10 left-0 w-[28rem] h-[28rem] bg-rose-400/12 rounded-full blur-[140px] pointer-events-none" />
      <div className="absolute top-1/2 left-1/3 w-96 h-96 bg-emerald-400/10 rounded-full blur-[160px] pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
          {/* Left Column: Signature Photo Montage with London Bridge */}
          <motion.div
            initial={{ opacity: 0, x: -40 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.7 }}
            className="lg:col-span-5 relative"
          >
            <div className="relative mx-auto max-w-md lg:max-w-none">
              {/* Backing Warm Gradient Glow */}
              <div className="absolute -inset-4 bg-gradient-to-tr from-amber-400/25 via-rose-400/20 to-indigo-400/20 rounded-[2.5rem] transform -rotate-3 scale-95 blur-md" />

              {/* Main Composite Image Card */}
              <div className="relative rounded-[2.2rem] overflow-hidden bg-white p-2.5 border-2 border-amber-200/60 shadow-[0_20px_50px_rgba(245,158,11,0.15)] group">
                <div className="rounded-[1.8rem] overflow-hidden bg-slate-100 relative">
                  <img
                    src={siteAsset('/assets/images/meezab-student-montage.png')}
                    alt="GenZ International Scholars Worldwide"
                    className="w-full h-auto object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out"
                    loading="lazy"
                  />
                  {/* Subtle Light Vignette */}
                  <div className="absolute inset-0 bg-gradient-to-t from-slate-900/35 via-transparent to-transparent pointer-events-none" />
                </div>
              </div>

              {/* Floating Mini Badge 1: 100% Guided Visa Support (Emerald Green) */}
              <motion.div
                animate={{ y: [0, -8, 0] }}
                transition={{ duration: 4, repeat: Infinity, ease: 'easeInOut' }}
                className="absolute -bottom-6 -left-4 sm:left-2 bg-white/95 backdrop-blur-md p-3.5 sm:p-4 rounded-2xl shadow-xl border-2 border-emerald-400/50 flex items-center gap-3 z-20"
              >
                <div className="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-md shadow-emerald-500/30">
                  <ShieldCheck className="w-6 h-6" />
                </div>
                <div>
                  <div className="font-display font-extrabold text-xs sm:text-sm text-slate-900 flex items-center gap-1.5">
                    <span>100% Guided Visa Support</span>
                    <span className="w-2 h-2 rounded-full bg-emerald-500 animate-ping" />
                  </div>
                  <div className="text-[10px] sm:text-xs text-emerald-700 font-semibold">Certified Global Advisory</div>
                </div>
              </motion.div>

              {/* Floating Mini Badge 2: 850+ Universities (Radiant Amber / Orange) */}
              <motion.div
                animate={{ y: [0, 8, 0] }}
                transition={{ duration: 4.5, repeat: Infinity, ease: 'easeInOut', delay: 0.5 }}
                className="absolute -top-5 -right-2 sm:right-2 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 p-3 sm:p-3.5 rounded-2xl shadow-xl border-2 border-white/60 text-white flex items-center gap-2.5 z-20"
              >
                <div className="w-9 h-9 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white">
                  <Globe2 className="w-5 h-5" />
                </div>
                <div>
                  <div className="font-display font-black text-xs text-white flex items-center gap-1">
                    <span>850+ Universities</span>
                    <Star className="w-3 h-3 fill-yellow-300 text-yellow-300" />
                  </div>
                  <div className="text-[10px] text-amber-100 font-medium">UK, USA, Canada & Europe</div>
                </div>
              </motion.div>
            </div>
          </motion.div>

          {/* Right Column: High-Impact Multi-Color Feature Bullets */}
          <motion.div
            initial={{ opacity: 0, x: 40 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.7 }}
            className="lg:col-span-7 flex flex-col justify-center"
          >
            {/* Top Multi-Color Tag */}
            <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gradient-to-r from-amber-500/15 via-orange-500/15 to-rose-500/15 border border-amber-500/30 text-amber-800 text-xs font-display font-bold uppercase tracking-wider mb-4 w-fit shadow-sm">
              <Sparkles className="w-3.5 h-3.5 text-amber-600 animate-spin-slow" />
              <span>{tag}</span>
            </div>

            {/* Headline with vibrant dual-color gradient */}
            <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#0B2545] tracking-tight leading-[1.16] mb-5">
              Why Choose GenZ Future Consulting for Your{' '}
              <span className="text-transparent bg-clip-text bg-gradient-to-r from-amber-600 via-orange-600 to-rose-600">
                Studies?
              </span>
            </h2>

            {/* Narrative text */}
            <p className="text-slate-600 text-sm sm:text-base leading-relaxed mb-7 max-w-2xl">
              {narrative}
            </p>

            {/* 4 Feature Bullets with 4 Distinct Vibrant Colors */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
              {defaultPillars.map((item, idx) => {
                const Icon = item.icon;
                return (
                  <motion.div
                    key={item.title}
                    initial={{ opacity: 0, y: 15 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ delay: 0.08 * idx, duration: 0.4 }}
                    whileHover={{ y: -4, transition: { duration: 0.2 } }}
                    className={`p-4 sm:p-5 rounded-2xl bg-white border border-slate-200/90 shadow-sm ${item.hoverBorder} ${item.cardHover} transition-all duration-300 flex flex-col justify-between group cursor-default`}
                  >
                    <div>
                      <div className="flex items-center justify-between mb-3">
                        <div className={`w-11 h-11 rounded-xl ${item.iconBg} ${item.iconColor} flex items-center justify-center flex-shrink-0 shadow-md group-hover:scale-110 transition-transform duration-300`}>
                          <Icon className="w-5 h-5" />
                        </div>
                        <span className={`text-[10px] font-bold px-2 py-0.5 rounded-full border ${item.tagBg}`}>
                          0{idx + 1}
                        </span>
                      </div>
                      <h4 className="font-display font-extrabold text-sm sm:text-base text-slate-900 group-hover:text-amber-800 transition-colors mb-1.5">
                        {item.title}
                      </h4>
                      <p className="text-slate-600 text-xs leading-relaxed">
                        {item.desc}
                      </p>
                    </div>
                  </motion.div>
                );
              })}
            </div>

            {/* Bottom Actions with Warm Sunset Button */}
            <div className="flex flex-wrap items-center gap-4">
              <button
                onClick={onOpenModal}
                className="px-8 py-4 bg-gradient-to-r from-amber-500 via-orange-500 to-amber-600 hover:from-amber-400 hover:to-orange-500 text-slate-950 rounded-full font-display font-black text-xs uppercase tracking-wider shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 hover:-translate-y-0.5 transition-all flex items-center gap-2 group"
              >
                <span>Start Your Free Evaluation</span>
                <ArrowRight className="w-4 h-4 text-slate-950 group-hover:translate-x-1.5 transition-transform" />
              </button>

              <div className="px-4 py-2 rounded-full bg-emerald-50 border border-emerald-200 text-xs text-emerald-800 font-bold flex items-center gap-2">
                <CheckCircle2 className="w-4 h-4 text-emerald-600" />
                <span>Zero Obligation · 100% Free Consultation</span>
              </div>
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};
