import React from 'react';
import { motion } from 'framer-motion';
import {
  FileText,
  Briefcase,
  Award,
  CheckCircle2,
  Sparkles,
  ArrowRight
} from 'lucide-react';
import { getContent } from '../content';

interface ProcedureSectionProps {
  onOpenModal?: () => void;
}

export const ProcedureSection: React.FC<ProcedureSectionProps> = ({ onOpenModal }) => {
  const defaultSteps = [
    {
      step: 'STEP 01',
      title: 'Get Details',
      desc: 'Contact us for initial assessment and receive transparent guidance from our certified global advisors.',
      badgePosition: 'top' as const,
      icon: FileText,
      colorTheme: 'emerald',
      borderColor: 'border-t-4 border-t-emerald-500 rounded-tr-[3rem]',
      badgeBorder: 'border-emerald-500',
      badgeBg: 'bg-emerald-500 text-white',
      badgeShadow: 'shadow-[0_8px_20px_rgba(16,185,129,0.35)]',
      hoverBorder: 'group-hover:border-emerald-400 group-hover:shadow-[0_20px_45px_rgba(16,185,129,0.22)]',
      stepTextColor: 'text-emerald-700 bg-emerald-50 border-emerald-200',
    },
    {
      step: 'STEP 02',
      title: 'Initiate Case',
      desc: 'Submit academic transcripts and our team maps your target programs across premier worldwide universities.',
      badgePosition: 'bottom' as const,
      icon: Briefcase,
      colorTheme: 'indigo',
      borderColor: 'border-b-4 border-b-indigo-500 rounded-br-[3rem]',
      badgeBorder: 'border-indigo-500',
      badgeBg: 'bg-indigo-600 text-white',
      badgeShadow: 'shadow-[0_8px_20px_rgba(99,102,241,0.35)]',
      hoverBorder: 'group-hover:border-indigo-400 group-hover:shadow-[0_20px_45px_rgba(99,102,241,0.22)]',
      stepTextColor: 'text-indigo-700 bg-indigo-50 border-indigo-200',
    },
    {
      step: 'STEP 03',
      title: 'Get Offer Letter',
      desc: 'Following direct university submissions, secure official unconditional or scholarship offer letters.',
      badgePosition: 'top' as const,
      icon: Award,
      colorTheme: 'amber',
      borderColor: 'border-t-4 border-t-amber-500 rounded-tr-[3rem]',
      badgeBorder: 'border-amber-500',
      badgeBg: 'bg-amber-500 text-slate-950',
      badgeShadow: 'shadow-[0_8px_20px_rgba(245,158,11,0.35)]',
      hoverBorder: 'group-hover:border-amber-400 group-hover:shadow-[0_20px_45px_rgba(245,158,11,0.22)]',
      stepTextColor: 'text-amber-800 bg-amber-50 border-amber-200',
    },
    {
      step: 'STEP 04',
      title: 'Admission Done',
      desc: 'Complete fee arrangements, fulfill visa compliance, and embark on your journey abroad with confidence.',
      badgePosition: 'bottom' as const,
      icon: CheckCircle2,
      colorTheme: 'rose',
      borderColor: 'border-b-4 border-b-rose-500 rounded-br-[3rem]',
      badgeBorder: 'border-rose-500',
      badgeBg: 'bg-rose-500 text-white',
      badgeShadow: 'shadow-[0_8px_20px_rgba(244,63,94,0.35)]',
      hoverBorder: 'group-hover:border-rose-400 group-hover:shadow-[0_20px_45px_rgba(244,63,94,0.22)]',
      stepTextColor: 'text-rose-700 bg-rose-50 border-rose-200',
    },
  ];

  const steps = getContent('sections.procedure.steps', defaultSteps);
  const headline = getContent('sections.procedure.headline', 'Our Procedure');
  const subheadline = getContent(
    'sections.procedure.subheadline',
    'A structured 4-step roadmap designed to take you from initial consultation to campus enrollment with total peace of mind.'
  );

  return (
    <section className="py-24 sm:py-32 bg-[#FAF7F2] relative overflow-hidden" id="procedure">
      {/* Decorative ambient subtle lines & multi-color glows */}
      <div className="absolute inset-0 bg-[linear-gradient(to_right,#0099ff06_1px,transparent_1px),linear-gradient(to_bottom,#0099ff06_1px,transparent_1px)] bg-[size:4rem_4rem] pointer-events-none" />
      <div className="absolute top-1/3 left-10 w-96 h-96 bg-emerald-400/10 rounded-full blur-[140px] pointer-events-none" />
      <div className="absolute bottom-1/3 right-10 w-96 h-96 bg-amber-400/10 rounded-full blur-[140px] pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-24">
          <motion.div
            initial={{ opacity: 0, y: -10 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gradient-to-r from-emerald-500/15 via-indigo-500/15 to-amber-500/15 border border-slate-300/60 text-slate-800 text-xs font-display font-bold uppercase tracking-wider mb-4 shadow-sm"
          >
            <Sparkles className="w-3.5 h-3.5 text-indigo-600" />
            <span>( Step-By-Step Workflow )</span>
          </motion.div>

          <motion.h2
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="font-display text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-tight mb-4"
          >
            Our{' '}
            <span className="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 via-indigo-600 to-amber-600">
              Procedure
            </span>
          </motion.h2>

          <motion.p
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.2 }}
            className="text-slate-600 text-sm sm:text-base lg:text-lg max-w-2xl mx-auto"
          >
            {subheadline}
          </motion.p>
        </div>

        {/* 4 Cards Grid with Alternating Circular Icons & Distinct Colors */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6 relative items-center">
          {steps.map((item: any, idx: number) => {
            const config = defaultSteps[idx] || defaultSteps[0];
            const isTopBadge = config.badgePosition === 'top';
            const Icon = config.icon || FileText;

            return (
              <motion.div
                key={item.step || idx}
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: idx * 0.12, duration: 0.55 }}
                whileHover={{ y: -8, transition: { duration: 0.25 } }}
                onClick={onOpenModal}
                className="relative group cursor-pointer"
              >
                {/* Outer Framing Card */}
                <div
                  className={`relative bg-white rounded-3xl p-8 pt-12 pb-12 shadow-md border-2 border-slate-200/90 ${config.borderColor} ${config.hoverBorder} transition-all duration-300 min-h-[320px] flex flex-col justify-between text-center overflow-visible`}
                >
                  {/* Floating Circular Badge: Alternating Top / Bottom with Unique Colors */}
                  <div
                    className={`absolute left-1/2 -translate-x-1/2 z-20 flex items-center justify-center w-16 h-16 rounded-full bg-white border-4 ${config.badgeBorder} ${config.badgeShadow} group-hover:scale-115 transition-all duration-300 ${
                      isTopBadge ? '-top-8' : '-bottom-8'
                    }`}
                  >
                    <div className={`w-11 h-11 rounded-full flex items-center justify-center ${config.badgeBg} transition-transform duration-300 group-hover:rotate-6`}>
                      <Icon className="w-5 h-5" />
                    </div>
                  </div>

                  {/* Step Label */}
                  <div className={`${isTopBadge ? 'mt-3' : 'mt-0'}`}>
                    <span className={`inline-block font-display font-extrabold text-[10px] sm:text-xs uppercase tracking-widest px-3 py-1 rounded-full border mb-3 ${config.stepTextColor}`}>
                      {item.step}
                    </span>
                    <h3 className="font-display font-black text-xl sm:text-2xl text-slate-900 mb-3 group-hover:text-navy transition-colors">
                      {item.title}
                    </h3>
                    <p className="text-slate-600 text-xs sm:text-sm leading-relaxed">
                      {item.desc}
                    </p>
                  </div>

                  {/* Bottom Action Trigger */}
                  <div className={`pt-4 border-t border-slate-100 flex items-center justify-center gap-1.5 text-xs font-bold text-slate-800 ${isTopBadge ? 'mb-0' : 'mb-3'}`}>
                    <span>Get Started</span>
                    <ArrowRight className="w-3.5 h-3.5 text-slate-500 group-hover:translate-x-1 transition-transform" />
                  </div>
                </div>
              </motion.div>
            );
          })}
        </div>
      </div>
    </section>
  );
};
