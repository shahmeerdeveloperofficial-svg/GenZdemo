import React, { useRef } from 'react';
import { motion, useMotionValue, useSpring, useTransform } from 'framer-motion';
import {
  Sparkles,
  ArrowRight,
  ShieldCheck,
  Award,
  Compass,
  GraduationCap,
  CheckCircle2,
  Trophy,
  Flame,
  Star
} from 'lucide-react';
import { getContent, siteAsset } from '../content';

interface StrategicTargetProps {
  onOpenModal?: () => void;
}

export const StrategicTargetSection: React.FC<StrategicTargetProps> = ({ onOpenModal }) => {
  const containerRef = useRef<HTMLDivElement>(null);
  const mouseX = useMotionValue(0);
  const mouseY = useMotionValue(0);

  const springConfig = { damping: 20, stiffness: 200 };
  const rotateX = useSpring(useTransform(mouseY, [-0.5, 0.5], [6, -6]), springConfig);
  const rotateY = useSpring(useTransform(mouseX, [-0.5, 0.5], [-6, 6]), springConfig);

  const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
    if (!containerRef.current) return;
    const rect = containerRef.current.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width - 0.5;
    const y = (e.clientY - rect.top) / rect.height - 0.5;
    mouseX.set(x);
    mouseY.set(y);
  };

  const handleMouseLeave = () => {
    mouseX.set(0);
    mouseY.set(0);
  };

  const headline = getContent(
    'sections.strategicTarget.headline',
    'Why GenZ Overseas Education is the Best Study Abroad Consultant in Pakistan'
  );

  const paragraph = getContent(
    'sections.strategicTarget.paragraph',
    'GenZ Overseas Education is Pakistan’s number one study abroad consultant because we work tirelessly to ensure every student achieves undeniable global success. Wider university networks and deep institutional know-how enable us to provide personalized mentorship from start to finish—including program shortlisting, full scholarship acquisition, and obtaining a visa.'
  );

  // 4 Orbiting Badges in 4 Distinct Jewel Colors (Emerald, Gold, Purple, Crimson)
  const badges = [
    {
      label: 'QUALITY',
      icon: ShieldCheck,
      position: 'top-10 left-3 sm:left-10',
      badgeClass: 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white border-2 border-emerald-300 shadow-[0_8px_20px_rgba(16,185,129,0.35)]',
    },
    {
      label: 'SCHOLARSHIPS',
      icon: Award,
      position: 'top-16 right-2 sm:right-6',
      badgeClass: 'bg-gradient-to-r from-amber-400 via-yellow-400 to-amber-500 text-slate-950 font-black border-2 border-amber-200 shadow-[0_8px_20px_rgba(245,158,11,0.35)]',
    },
    {
      label: 'PURPOSEFUL',
      icon: Compass,
      position: 'bottom-24 -left-2 sm:left-4',
      badgeClass: 'bg-gradient-to-r from-purple-600 to-indigo-600 text-white border-2 border-purple-300 shadow-[0_8px_20px_rgba(147,51,234,0.35)]',
    },
    {
      label: 'ABILITIES',
      icon: GraduationCap,
      position: 'bottom-14 right-3 sm:right-10',
      badgeClass: 'bg-gradient-to-r from-rose-500 via-pink-500 to-red-600 text-white border-2 border-rose-300 shadow-[0_8px_20px_rgba(244,63,94,0.35)]',
    },
  ];

  const highlights = [
    { label: '99.4% Visa Success', detail: 'Impeccable embassy compliance', color: 'emerald', bg: 'bg-emerald-50 text-emerald-800 border-emerald-200' },
    { label: '$12M+ Scholarships', detail: 'Merit & need-based funding', color: 'amber', bg: 'bg-amber-50 text-amber-900 border-amber-200' },
    { label: '25+ Global Hubs', detail: 'UK, USA, Canada, Australia', color: 'indigo', bg: 'bg-indigo-50 text-indigo-800 border-indigo-200' },
    { label: 'Fast-Track CAS', detail: 'Direct university liaison', color: 'rose', bg: 'bg-rose-50 text-rose-800 border-rose-200' },
  ];

  return (
    <section
      ref={containerRef}
      onMouseMove={handleMouseMove}
      onMouseLeave={handleMouseLeave}
      className="py-24 sm:py-32 bg-gradient-to-b from-[#F5F8FA] via-[#FAF7F2] to-white relative overflow-hidden"
      id="best-consultants"
    >
      {/* Background soft ambient multi-color bursts */}
      <div className="absolute top-1/2 left-1/4 -translate-y-1/2 w-[30rem] h-[30rem] bg-amber-400/15 rounded-full blur-[160px] pointer-events-none" />
      <div className="absolute bottom-10 right-10 w-[28rem] h-[28rem] bg-rose-400/12 rounded-full blur-[150px] pointer-events-none" />
      <div className="absolute top-10 right-1/3 w-[24rem] h-[24rem] bg-emerald-400/10 rounded-full blur-[140px] pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
          {/* Left Column: 3D Dartboard Bullseye Target Board with 4 Jewel Badges */}
          <motion.div
            style={{ rotateX, rotateY, transformStyle: 'preserve-3d' }}
            className="lg:col-span-6 relative flex items-center justify-center select-none"
          >
            {/* The Solar Amber/Gold Aura Backdrop */}
            <div className="absolute w-72 sm:w-96 h-72 sm:h-96 rounded-full bg-gradient-to-tr from-amber-400 via-orange-400 to-yellow-300 opacity-80 blur-md pointer-events-none animate-pulse" />

            {/* Orbiting Subtle Dashed Ring */}
            <div className="absolute w-[320px] sm:w-[440px] h-[320px] sm:h-[440px] rounded-full border-2 border-dashed border-amber-300/60 pointer-events-none animate-spin-slow" />

            {/* Main 3D Dartboard Target Graphic (Paragon Target Dart) */}
            <motion.div
              animate={{ y: [0, -10, 0] }}
              transition={{ repeat: Infinity, duration: 6, ease: 'easeInOut' }}
              className="relative z-10 w-72 sm:w-96 md:w-[420px] group cursor-pointer"
            >
              <img
                src={siteAsset('/assets/images/paragon-target-dart.png')}
                alt="GenZ Precision Target"
                className="w-full h-auto object-contain filter drop-shadow-[0_25px_35px_rgba(245,158,11,0.25)] group-hover:scale-105 transition-transform duration-500 ease-out"
                loading="lazy"
              />
            </motion.div>

            {/* 4 Orbiting Jewel Value Badges (Screenshot 4) */}
            {badges.map((b, idx) => {
              const Icon = b.icon;
              return (
                <motion.div
                  key={b.label}
                  initial={{ opacity: 0, scale: 0.8 }}
                  whileInView={{ opacity: 1, scale: 1 }}
                  viewport={{ once: true }}
                  transition={{ delay: 0.12 * idx, duration: 0.4 }}
                  whileHover={{ scale: 1.12, y: -4 }}
                  className={`absolute ${b.position} z-20 px-3.5 sm:px-4 py-1.5 sm:py-2 rounded-xl sm:rounded-2xl flex items-center gap-2 font-display font-extrabold text-[10px] sm:text-xs tracking-wider cursor-default ${b.badgeClass}`}
                >
                  <Icon className="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" />
                  <span>{b.label}</span>
                </motion.div>
              );
            })}
          </motion.div>

          {/* Right Column: Authority Narrative & Colorful Key Metrics */}
          <motion.div
            initial={{ opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="lg:col-span-6 flex flex-col justify-center"
          >
            {/* Top Tag */}
            <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-gradient-to-r from-amber-500/15 to-rose-500/15 border border-amber-400/40 text-amber-800 text-xs font-display font-bold uppercase tracking-wider mb-4 w-fit shadow-sm">
              <Trophy className="w-3.5 h-3.5 text-amber-600" />
              <span>( National Educational Benchmark )</span>
            </div>

            {/* Headline */}
            <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#0B2545] tracking-tight leading-[1.18] mb-5">
              Why GenZ Overseas Education is the{' '}
              <span className="text-transparent bg-clip-text bg-gradient-to-r from-amber-600 via-orange-600 to-rose-600">
                Best Study Abroad Consultant
              </span>{' '}
              in Pakistan
            </h2>

            {/* Paragraph */}
            <p className="text-slate-600 text-sm sm:text-base leading-relaxed mb-7">
              {paragraph}
            </p>

            {/* 4 High-Impact Micro Metrics Grid */}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3.5 mb-8">
              {highlights.map((h, i) => (
                <div
                  key={i}
                  className={`p-3.5 rounded-xl border flex items-center gap-3 ${h.bg} transition-all duration-300 hover:scale-[1.02]`}
                >
                  <div className="w-2.5 h-2.5 rounded-full bg-current" />
                  <div>
                    <div className="font-display font-black text-xs sm:text-sm">{h.label}</div>
                    <div className="text-[10px] opacity-80">{h.detail}</div>
                  </div>
                </div>
              ))}
            </div>

            {/* Bottom Actions */}
            <div className="flex flex-wrap items-center gap-4">
              <button
                onClick={onOpenModal}
                className="px-8 py-4 bg-gradient-to-r from-[#0B2545] to-[#133E68] hover:from-amber-600 hover:to-orange-600 text-white rounded-full font-display font-bold text-xs uppercase tracking-wider shadow-lg hover:shadow-amber-500/30 hover:-translate-y-0.5 transition-all flex items-center gap-2 group"
              >
                <span>Book Direct Consultation</span>
                <ArrowRight className="w-4 h-4 text-amber-400 group-hover:translate-x-1.5 transition-transform" />
              </button>

              <div className="flex items-center gap-1.5 text-xs text-slate-500 font-semibold">
                <Star className="w-4 h-4 fill-amber-400 text-amber-400" />
                <span>Ranked #1 for UK & USA Admissions</span>
              </div>
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};
