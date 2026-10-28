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
  Lightbulb
} from 'lucide-react';
import { getContent, siteAsset } from '../content';

interface WhyChooseConsultingProps {
  onOpenModal?: () => void;
}

export const WhyChooseConsultingSection: React.FC<WhyChooseConsultingProps> = ({ onOpenModal }) => {
  const defaultPillars = [
    {
      title: 'University Admissions Support',
      desc: 'Get expert guidance to find the perfect program and institution for your future.',
      icon: GraduationCap,
    },
    {
      title: 'Scholarship Guidance',
      desc: 'We help you identify and apply for merit and need-based scholarships to ease financial burdens.',
      icon: Award,
    },
    {
      title: 'Application Assistance',
      desc: 'Streamlining the application process, essays, and documentation to maximize your acceptance rate.',
      icon: FileCheck2,
    },
    {
      title: 'Travel & Accommodation',
      desc: 'Enjoy stress-free travel arrangements, visa interview prep, housing, and airport pickups.',
      icon: PlaneTakeoff,
    },
  ];

  const tag = getContent('sections.whyChoose.tag', 'Study Abroad & Unlock Global Opportunities');
  const headline = getContent('sections.whyChoose.headline', 'Why Choose GenZ Future Consulting for Your Studies?');
  const narrative = getContent(
    'sections.whyChoose.narrative',
    'GenZ Consultant is dedicated to helping students achieve their academic ambitions worldwide. With years of experience in educational consultancy, executive coaching, and the backing of premier international universities, we provide personalized support tailored to your dreams. From securing university admissions to ensuring a smooth transition abroad, we’re with you every step of the way.'
  );

  return (
    <section className="py-24 sm:py-32 bg-white relative overflow-hidden" id="consulting-advantage">
      {/* Decorative ambient gradients */}
      <div className="absolute top-1/4 -right-40 w-96 h-96 bg-cyan/10 rounded-full blur-[120px] pointer-events-none" />
      <div className="absolute bottom-10 -left-40 w-96 h-96 bg-blue-600/10 rounded-full blur-[140px] pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
          {/* Left Column: Signature Creative Photo Montage (Matching Reference Image) */}
          <motion.div
            initial={{ opacity: 0, x: -40 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.7 }}
            className="lg:col-span-5 relative"
          >
            {/* Outer Decorative Floating Shapes */}
            <div className="relative mx-auto max-w-md lg:max-w-none">
              {/* Backing Color Block */}
              <div className="absolute -inset-4 bg-gradient-to-tr from-cyan/20 via-blue-500/10 to-transparent rounded-[2.5rem] transform -rotate-3 scale-95 blur-sm" />

              {/* Main Composite Image */}
              <div className="relative rounded-[2rem] overflow-hidden bg-slate-50 border-4 border-white shadow-2xl group">
                <img
                  src={siteAsset('/assets/images/meezab-student-montage.png')}
                  alt="GenZ International Scholars Abroad"
                  className="w-full h-auto object-cover transform group-hover:scale-105 transition-transform duration-700 ease-out"
                  loading="lazy"
                />

                {/* Subtle Inner Glass Vignette */}
                <div className="absolute inset-0 bg-gradient-to-t from-[#07172C]/40 via-transparent to-transparent pointer-events-none" />
              </div>

              {/* Floating Mini Badge 1: 100% Visa Support */}
              <motion.div
                animate={{ y: [0, -8, 0] }}
                transition={{ duration: 4, repeat: Infinity, ease: 'easeInOut' }}
                className="absolute -bottom-6 -left-4 sm:left-4 bg-white/95 backdrop-blur-md p-3.5 sm:p-4 rounded-2xl shadow-xl border border-navy/10 flex items-center gap-3 z-20"
              >
                <div className="w-10 h-10 rounded-xl bg-cyan/15 flex items-center justify-center text-cyan">
                  <ShieldCheck className="w-5 h-5 text-cyan" />
                </div>
                <div>
                  <div className="font-display font-extrabold text-xs sm:text-sm text-navy">100% Guided Visa Support</div>
                  <div className="text-[10px] sm:text-xs text-slate-500">Certified Global Advisors</div>
                </div>
              </motion.div>

              {/* Floating Mini Badge 2: Global Placement Network */}
              <motion.div
                animate={{ y: [0, 8, 0] }}
                transition={{ duration: 4.5, repeat: Infinity, ease: 'easeInOut', delay: 0.5 }}
                className="absolute -top-4 -right-2 sm:right-4 bg-[#07172C]/95 backdrop-blur-md p-3 sm:p-3.5 rounded-2xl shadow-xl border border-white/10 text-white flex items-center gap-2.5 z-20"
              >
                <div className="w-8 h-8 rounded-lg bg-cyan flex items-center justify-center text-white">
                  <Globe2 className="w-4 h-4" />
                </div>
                <div>
                  <div className="font-display font-extrabold text-xs text-cyan-light">850+ Universities</div>
                  <div className="text-[10px] text-white/70">UK, USA, Europe & Asia</div>
                </div>
              </motion.div>
            </div>
          </motion.div>

          {/* Right Column: High-Impact Typography & Feature Bullets */}
          <motion.div
            initial={{ opacity: 0, x: 40 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.7 }}
            className="lg:col-span-7 flex flex-col justify-center"
          >
            {/* Top Tag */}
            <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/10 border border-cyan/20 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-4 w-fit shadow-sm">
              <Sparkles className="w-3.5 h-3.5" />
              <span>{tag}</span>
            </div>

            {/* Headline with dynamic highlight */}
            <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#0B2545] tracking-tight leading-[1.15] mb-6">
              Why Choose GenZ Future Consulting for Your{' '}
              <span className="text-transparent bg-clip-text bg-gradient-to-r from-cyan via-blue-600 to-cyan-light">
                Studies?
              </span>
            </h2>

            {/* Narrative text */}
            <p className="text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed mb-8">
              {narrative}
            </p>

            {/* 4 Feature Bullets with Glowing Hover Cards */}
            <div className="space-y-4 mb-10">
              {defaultPillars.map((item, idx) => {
                const Icon = item.icon;
                return (
                  <motion.div
                    key={item.title}
                    initial={{ opacity: 0, y: 15 }}
                    whileInView={{ opacity: 1, y: 0 }}
                    viewport={{ once: true }}
                    transition={{ delay: 0.1 * idx, duration: 0.45 }}
                    whileHover={{ x: 6, transition: { duration: 0.2 } }}
                    className="p-4 sm:p-5 rounded-2xl bg-slate-50/80 hover:bg-white border border-slate-200/80 hover:border-cyan/40 hover:shadow-[0_10px_30px_rgba(0,153,255,0.12)] transition-all duration-300 flex items-start gap-4 group cursor-default"
                  >
                    <div className="w-10 h-10 rounded-xl bg-cyan/10 border border-cyan/25 flex items-center justify-center flex-shrink-0 group-hover:bg-cyan group-hover:text-white transition-colors duration-300">
                      <Icon className="w-5 h-5 text-cyan group-hover:text-white transition-colors duration-300" />
                    </div>
                    <div>
                      <h4 className="font-display font-bold text-sm sm:text-base text-navy group-hover:text-cyan transition-colors mb-1">
                        {item.title}
                      </h4>
                      <p className="text-slate-600 text-xs sm:text-sm leading-relaxed">
                        {item.desc}
                      </p>
                    </div>
                  </motion.div>
                );
              })}
            </div>

            {/* Bottom Actions */}
            <div className="flex flex-wrap items-center gap-4">
              <button
                onClick={onOpenModal}
                className="px-8 py-4 bg-navy hover:bg-navy-dark text-white rounded-full font-display font-bold text-xs uppercase tracking-wider shadow-lg hover:shadow-cyan-glow hover:-translate-y-0.5 transition-all flex items-center gap-2 group"
              >
                <span>Start Your Free Evaluation</span>
                <ArrowRight className="w-4 h-4 text-cyan group-hover:translate-x-1.5 transition-transform" />
              </button>

              <div className="text-xs text-slate-500 font-medium flex items-center gap-2">
                <CheckCircle2 className="w-4 h-4 text-emerald-500" />
                <span>Zero Obligation · 100% Confidential</span>
              </div>
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};
