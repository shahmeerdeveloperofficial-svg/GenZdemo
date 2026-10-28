import React, { useRef } from 'react';
import { motion, useMotionValue, useSpring, useTransform } from 'framer-motion';
import {
  Target,
  Sparkles,
  ArrowRight,
  ShieldCheck,
  Award,
  Compass,
  GraduationCap,
  CheckCircle2,
  Trophy
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
    'GenZ Overseas Education is Pakistan’s number one study abroad consultant because we work hard to help every student achieve undeniable global success. Wider university networks and deep institutional know-how enable us to provide personalized mentorship and guidance from start to finish—including program shortlisting, full scholarship acquisition, and obtaining a visa. We distinguish ourselves through our complete, open, and dependable advisory services, making GenZ the first choice for ambitious scholars pursuing their dreams overseas.'
  );

  const badges = [
    { label: 'QUALITY', icon: ShieldCheck, position: 'top-10 left-4 sm:left-12', color: 'bg-white text-navy border-navy/20' },
    { label: 'SCHOLARSHIPS', icon: Award, position: 'top-20 right-2 sm:right-6', color: 'bg-[#0099FF] text-white border-cyan' },
    { label: 'PURPOSEFUL', icon: Compass, position: 'bottom-28 -left-2 sm:left-4', color: 'bg-[#0B2545] text-white border-cyan/40' },
    { label: 'ABILITIES', icon: GraduationCap, position: 'bottom-16 right-4 sm:right-12', color: 'bg-white text-navy border-slate-200' },
  ];

  return (
    <section
      ref={containerRef}
      onMouseMove={handleMouseMove}
      onMouseLeave={handleMouseLeave}
      className="py-24 sm:py-32 bg-white relative overflow-hidden"
      id="best-consultants"
    >
      {/* Background soft ambient accents */}
      <div className="absolute top-1/2 left-1/4 -translate-y-1/2 w-96 h-96 bg-amber-400/10 rounded-full blur-[140px] pointer-events-none" />
      <div className="absolute bottom-10 right-10 w-96 h-96 bg-cyan/10 rounded-full blur-[140px] pointer-events-none" />

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16 items-center">
          {/* Left Column: 3D Dartboard Bullseye Target Board with Orbiting Badges (Exact Reference Image) */}
          <motion.div
            style={{ rotateX, rotateY, transformStyle: 'preserve-3d' }}
            className="lg:col-span-6 relative flex items-center justify-center select-none"
          >
            {/* The Solar Amber/Gold Aura Backdrop */}
            <div className="absolute w-72 sm:w-96 h-72 sm:h-96 rounded-full bg-gradient-to-tr from-amber-400 via-amber-300 to-yellow-200 opacity-90 blur-sm pointer-events-none" />

            {/* Orbiting Subtle Orbit Path Ring */}
            <div className="absolute w-[320px] sm:w-[440px] h-[320px] sm:h-[440px] rounded-full border-2 border-dashed border-slate-300 pointer-events-none animate-spin-slow" />

            {/* Main 3D Dartboard Target Graphic */}
            <motion.div
              animate={{ y: [0, -10, 0] }}
              transition={{ repeat: Infinity, duration: 6, ease: 'easeInOut' }}
              className="relative z-10 w-72 sm:w-96 md:w-[420px] group cursor-pointer"
            >
              <img
                src={siteAsset('/assets/images/paragon-target-dart.png')}
                alt="GenZ Precision Target"
                className="w-full h-auto object-contain filter drop-shadow-2xl group-hover:scale-105 transition-transform duration-500 ease-out"
                loading="lazy"
              />
            </motion.div>

            {/* 4 Orbiting Value Badges (Matching Screenshot 4) */}
            {badges.map((b, idx) => {
              const Icon = b.icon;
              return (
                <motion.div
                  key={b.label}
                  initial={{ opacity: 0, scale: 0.8 }}
                  whileInView={{ opacity: 1, scale: 1 }}
                  viewport={{ once: true }}
                  transition={{ delay: 0.15 * idx, duration: 0.4 }}
                  whileHover={{ scale: 1.1, y: -4 }}
                  className={`absolute ${b.position} z-20 px-3.5 sm:px-4 py-1.5 sm:py-2 rounded-xl sm:rounded-2xl border-2 shadow-lg flex items-center gap-2 font-display font-extrabold text-[10px] sm:text-xs tracking-wider cursor-default ${b.color}`}
                >
                  <Icon className="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" />
                  <span>{b.label}</span>
                </motion.div>
              );
            })}
          </motion.div>

          {/* Right Column: Authority Narrative & Credentials */}
          <motion.div
            initial={{ opacity: 0, x: 30 }}
            whileInView={{ opacity: 1, x: 0 }}
            viewport={{ once: true }}
            transition={{ duration: 0.6 }}
            className="lg:col-span-6 flex flex-col justify-center"
          >
            {/* Top Tag */}
            <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/10 border border-cyan/20 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-4 w-fit shadow-sm">
              <Trophy className="w-3.5 h-3.5" />
              <span>( National Educational Benchmark )</span>
            </div>

            {/* Headline */}
            <h2 className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-[#0B2545] tracking-tight leading-[1.18] mb-6">
              {headline}
            </h2>

            {/* Paragraph */}
            <p className="text-slate-600 text-sm sm:text-base lg:text-lg leading-relaxed mb-8">
              {paragraph}
            </p>

            {/* 3 Metric Highlight Pills */}
            <div className="grid grid-cols-3 gap-3 sm:gap-4 mb-8">
              <div className="p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
                <div className="font-display font-black text-xl sm:text-2xl text-cyan">98.7%</div>
                <div className="text-[10px] sm:text-xs text-slate-500 font-semibold uppercase mt-0.5">Visa Grant Rate</div>
              </div>
              <div className="p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
                <div className="font-display font-black text-xl sm:text-2xl text-navy">$12M+</div>
                <div className="text-[10px] sm:text-xs text-slate-500 font-semibold uppercase mt-0.5">Scholarships Won</div>
              </div>
              <div className="p-3.5 sm:p-4 rounded-2xl bg-slate-50 border border-slate-200/80 text-center">
                <div className="font-display font-black text-xl sm:text-2xl text-emerald-600">100%</div>
                <div className="text-[10px] sm:text-xs text-slate-500 font-semibold uppercase mt-0.5">Free Evaluation</div>
              </div>
            </div>

            {/* CTA Button */}
            <div className="flex flex-wrap items-center gap-4">
              <button
                onClick={onOpenModal}
                className="px-8 py-4 bg-navy hover:bg-navy-dark text-white rounded-full font-display font-bold text-xs uppercase tracking-wider shadow-xl hover:shadow-cyan-glow hover:-translate-y-0.5 transition-all flex items-center gap-2 group"
              >
                <span>Book Strategic Counseling Session</span>
                <ArrowRight className="w-4 h-4 text-cyan group-hover:translate-x-1.5 transition-transform" />
              </button>

              <span className="text-xs text-slate-500 flex items-center gap-1.5">
                <CheckCircle2 className="w-4 h-4 text-cyan" />
                <span>Islamabad & Lahore Campus Hubs</span>
              </span>
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};
