import React from 'react';
import { motion } from 'framer-motion';
import {
  FileText,
  Briefcase,
  Award,
  CheckCircle2,
  Sparkles,
  ArrowRight,
  Send,
  GraduationCap
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
      desc: 'Contact us for initial details and get advice from our professional consultants.',
      badgePosition: 'top' as const,
      icon: FileText,
    },
    {
      step: 'STEP 02',
      title: 'Initiate Case',
      desc: 'Submit your documents we will start finding the best opportunities for you.',
      badgePosition: 'bottom' as const,
      icon: Briefcase,
    },
    {
      step: 'STEP 03',
      title: 'Get Offer Letter',
      desc: 'After applying to some top universities, you will get an offer letter from the university.',
      badgePosition: 'top' as const,
      icon: Award,
    },
    {
      step: 'STEP 04',
      title: 'Admission Done',
      desc: 'Following the offer letter, you get admission to the university, submit the fee, and done.',
      badgePosition: 'bottom' as const,
      icon: CheckCircle2,
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
      {/* Decorative ambient subtle lines */}
      <div className="absolute inset-0 bg-[linear-gradient(to_right,#0099ff08_1px,transparent_1px),linear-gradient(to_bottom,#0099ff08_1px,transparent_1px)] bg-[size:4rem_4rem] pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-20">
          <motion.div
            initial={{ opacity: 0, y: -10 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/10 border border-cyan/25 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-4 shadow-sm"
          >
            <Sparkles className="w-3.5 h-3.5 text-cyan" />
            <span>( Step-By-Step Workflow )</span>
          </motion.div>

          <motion.h2
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="font-display text-4xl sm:text-5xl lg:text-6xl font-black text-navy tracking-tight leading-tight mb-4"
          >
            {headline}
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

        {/* 4 Cards Grid with Exact Reference Layout & Alternating Circular Icons */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6 relative items-center">
          {steps.map((item: any, idx: number) => {
            const isTopBadge = item.badgePosition === 'top';
            const Icon = defaultSteps[idx]?.icon || FileText;

            return (
              <motion.div
                key={item.step || idx}
                initial={{ opacity: 0, y: 30 }}
                whileInView={{ opacity: 1, y: 0 }}
                viewport={{ once: true }}
                transition={{ delay: idx * 0.15, duration: 0.55 }}
                whileHover={{ y: -8, transition: { duration: 0.25 } }}
                onClick={onOpenModal}
                className="relative group cursor-pointer"
              >
                {/* Outer Framing Card: Matching exact Teal Rounded Corner Brackets in Reference Screenshot */}
                <div
                  className={`relative bg-white rounded-3xl p-8 pt-10 pb-10 shadow-lg border-2 border-slate-200/80 group-hover:border-cyan group-hover:shadow-[0_20px_50px_rgba(0,153,255,0.2)] transition-all duration-300 min-h-[310px] flex flex-col justify-between text-center overflow-visible ${
                    isTopBadge
                      ? 'border-t-4 border-t-cyan/80 rounded-tr-[3rem]'
                      : 'border-b-4 border-b-cyan/80 rounded-br-[3rem]'
                  }`}
                >
                  {/* Floating Circular Badge: Alternating Top / Bottom as per Screenshot 3 */}
                  <div
                    className={`absolute left-1/2 -translate-x-1/2 z-20 flex items-center justify-center w-16 h-16 rounded-full bg-white border-4 border-[#07172C] group-hover:border-cyan shadow-xl group-hover:scale-110 transition-all duration-300 ${
                      isTopBadge ? '-top-8' : '-bottom-8'
                    }`}
                  >
                    <div className="w-11 h-11 rounded-full bg-slate-50 flex items-center justify-center text-[#07172C] group-hover:bg-cyan group-hover:text-white transition-colors duration-300">
                      <Icon className="w-5 h-5 transition-transform duration-300 group-hover:scale-110" />
                    </div>
                  </div>

                  {/* Top Step Number Label */}
                  <div className={`${isTopBadge ? 'mt-3' : 'mt-0'}`}>
                    <span className="font-display font-extrabold text-[11px] sm:text-xs uppercase tracking-widest text-slate-400 group-hover:text-cyan transition-colors">
                      {item.step}
                    </span>
                    <h3 className="font-display font-extrabold text-xl sm:text-2xl text-navy mt-1 mb-3 group-hover:text-cyan transition-colors">
                      {item.title}
                    </h3>
                  </div>

                  {/* Card Description */}
                  <p className="text-slate-600 text-xs sm:text-sm leading-relaxed mb-3">
                    {item.desc}
                  </p>

                  {/* Bottom Prompt Link */}
                  <div className={`flex items-center justify-center gap-1.5 text-xs font-bold text-cyan group-hover:text-cyan-light transition-colors ${!isTopBadge ? 'mb-4' : 'mb-0'}`}>
                    <span>Apply Step</span>
                    <ArrowRight className="w-3.5 h-3.5 transform group-hover:translate-x-1 transition-transform" />
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
