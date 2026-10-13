import React, { useState, useEffect } from 'react';
import { motion } from 'framer-motion';
import { Compass, ArrowRight, Calendar, Sparkles, CheckCircle2, Globe2, Award } from 'lucide-react';
import { ThreeCanvas } from './ThreeCanvas';

interface HeroSectionProps {
  onOpenModal: () => void;
}

export const HeroSection: React.FC<HeroSectionProps> = ({ onOpenModal }) => {
  const [scrollProgress, setScrollProgress] = useState(0);

  useEffect(() => {
    const handleScroll = () => {
      const scrollY = window.scrollY;
      const height = window.innerHeight;
      const progress = Math.min(1, Math.max(0, scrollY / height));
      setScrollProgress(progress);
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  return (
    <section className="relative overflow-hidden bg-gradient-to-b from-[#F0F7FF] via-[#FAF7F2] to-white pt-8 pb-20 md:pt-14 md:pb-28">
      {/* Top Subtle Cloud Texture */}
      <div className="absolute top-0 left-0 right-0 h-28 pointer-events-none opacity-40 z-0">
        <img
          src="/assets/images/cream-top-cloud.webp"
          alt=""
          className="w-full h-full object-cover"
        />
      </div>

      {/* Background Aerospace Vector & Subtle Ambient Glow */}
      <div className="absolute inset-0 pointer-events-none overflow-hidden" aria-hidden="true">
        {/* Floating Airplane Flight Path SVG matching logo */}
        <svg
          className="absolute top-12 right-0 w-[550px] md:w-[850px] opacity-35 animate-swoosh"
          viewBox="0 0 700 350"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M20 320 C 180 280, 360 200, 620 40"
            stroke="url(#heroJetGradient)"
            strokeWidth="3.5"
            strokeDasharray="12 12"
            strokeLinecap="round"
          />
          <path d="M620 40 L645 22 L632 50 Z" fill="#0099FF" />
          <defs>
            <linearGradient id="heroJetGradient" x1="0%" y1="100%" x2="100%" y2="0%">
              <stop offset="0%" stopColor="#0099FF" stopOpacity="0" />
              <stop offset="50%" stopColor="#0099FF" stopOpacity="0.4" />
              <stop offset="100%" stopColor="#0099FF" stopOpacity="0.9" />
            </linearGradient>
          </defs>
        </svg>

        {/* Soft Ambient Blurred Circles */}
        <div className="absolute top-1/4 -left-32 w-96 h-96 rounded-full bg-cyan/10 blur-3xl" />
        <div className="absolute bottom-10 right-10 w-96 h-96 rounded-full bg-cyan-light/10 blur-3xl" />
      </div>

      <div className="max-w-7xl mx-auto px-6 relative z-10">
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
          {/* Left Column: Editorial Headline & Actions */}
          <motion.div
            className="lg:col-span-7 flex flex-col items-start"
            initial={{ opacity: 0, y: 30 }}
            animate={{ opacity: 1, y: 0 }}
            transition={{ duration: 0.8, ease: [0.16, 1, 0.3, 1] }}
          >
            {/* Tag Pill */}
            <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/10 border border-cyan/20 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-6">
              <Compass className="w-4 h-4 text-cyan" />
              <span>A Cambridge International World School</span>
            </div>

            {/* Bold Editorial Headline with Animated Cursor */}
            <h1 className="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-navy tracking-tight leading-[1.1] mb-6">
              Where Curiosity Ignites{' '}
              <span className="bg-gradient-to-r from-navy via-cyan to-cyan-light bg-clip-text text-transparent">
                Global Ambition
              </span>
              <span className="text-cyan animate-pulse">|</span>
            </h1>

            {/* Supporting Statement */}
            <p className="text-slate-600 text-lg sm:text-xl font-normal leading-relaxed max-w-2xl mb-8">
              Authorised British Cambridge education seamlessly articulating from Early
              Years through A-Levels and into the world's most prestigious universities.
            </p>

            {/* Dual CTAs */}
            <div className="flex flex-wrap items-center gap-4 w-full sm:w-auto">
              <button
                onClick={onOpenModal}
                className="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 bg-navy text-white rounded-full font-display font-bold text-sm tracking-wide hover:bg-navy-dark shadow-xl hover:shadow-cyan-glow transition-all transform hover:-translate-y-0.5 group"
              >
                <Calendar className="w-4 h-4 text-cyan-light" />
                <span>Book Private Campus Tour</span>
                <span className="w-2 h-2 rounded-full bg-cyan group-hover:scale-150 transition-transform" />
              </button>

              <a
                href="#continuum"
                className="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-7 py-4 bg-white/90 text-navy hover:text-cyan border border-navy/10 rounded-full font-display font-bold text-sm hover:border-cyan transition-all shadow-sm"
              >
                <span>Explore Continuum</span>
                <ArrowRight className="w-4 h-4 text-cyan" />
              </a>
            </div>

            {/* Highlights row */}
            <div className="mt-10 pt-8 border-t border-navy/10 flex flex-wrap items-center gap-6 sm:gap-10">
              <div className="flex items-center gap-2">
                <CheckCircle2 className="w-4 h-4 text-cyan" />
                <span className="text-xs font-semibold text-slate-700">Authorised Cambridge Pathway</span>
              </div>
              <div className="flex items-center gap-2">
                <CheckCircle2 className="w-4 h-4 text-cyan" />
                <span className="text-xs font-semibold text-slate-700">1:8 Faculty Ratio</span>
              </div>
              <div className="flex items-center gap-2">
                <CheckCircle2 className="w-4 h-4 text-cyan" />
                <span className="text-xs font-semibold text-slate-700">Ivy League & Russell Group Success</span>
              </div>
            </div>
          </motion.div>

          {/* Right Column: Interactive 3D Academic Globe + Architecture Layer */}
          <motion.div
            className="lg:col-span-5 relative"
            initial={{ opacity: 0, scale: 0.95 }}
            animate={{ opacity: 1, scale: 1 }}
            transition={{ duration: 0.9, delay: 0.2, ease: [0.16, 1, 0.3, 1] }}
          >
            {/* Main Stage Presentation Container */}
            <div className="relative mx-auto max-w-md lg:max-w-none">
              {/* Glass / Solid Card Shell */}
              <div className="relative rounded-3xl overflow-hidden shadow-2xl border-4 border-white/90 aspect-[4/5] bg-gradient-to-br from-[#0B2545] via-[#07172C] to-[#0A192F] flex items-center justify-center">
                {/* Background Campus Texture with Overlay */}
                <img
                  src="/assets/images/hero-campus.jpg"
                  alt="GenZ Campus Architecture"
                  className="absolute inset-0 w-full h-full object-cover opacity-20 mix-blend-luminosity filter blur-[1px]"
                />

                {/* 3D Global Knowledge Sphere Canvas */}
                <div className="absolute inset-0 z-10 flex items-center justify-center">
                  <ThreeCanvas scrollProgress={scrollProgress} />
                </div>

                {/* Gradient vignette */}
                <div className="absolute inset-0 bg-gradient-to-t from-navy-dark via-transparent to-navy-dark/40 z-10 pointer-events-none" />

                {/* Bottom Overlay Info */}
                <div className="absolute bottom-6 left-6 right-6 text-white z-20 pointer-events-none">
                  <div className="flex items-center gap-2 text-cyan-light text-xs font-bold uppercase tracking-wider mb-1">
                    <Sparkles className="w-3.5 h-3.5 text-cyan" />
                    <span>Global Learning Ecosystem</span>
                  </div>
                  <h3 className="font-display text-xl font-bold text-white">
                    Islamabad & Lahore Campuses
                  </h3>
                  <p className="text-white/70 text-xs mt-1">
                    Interactive global inquiry with student delegations in 18+ countries.
                  </p>
                </div>
              </div>

              {/* Floating Top Left Badge: Cambridge Accreditation */}
              <motion.div
                initial={{ y: -10, opacity: 0 }}
                animate={{ y: 0, opacity: 1 }}
                transition={{ delay: 0.4, duration: 0.6 }}
                className="absolute -top-5 -left-5 bg-white/95 backdrop-blur-md px-4 py-3 rounded-2xl shadow-xl border border-navy/10 flex items-center gap-3 z-30"
              >
                <div className="w-10 h-10 rounded-xl bg-cyan/15 flex items-center justify-center text-cyan">
                  <Globe2 className="w-5 h-5 text-cyan" />
                </div>
                <div>
                  <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Accreditation</div>
                  <div className="font-display font-extrabold text-sm text-navy">Cambridge International</div>
                </div>
              </motion.div>

              {/* Floating Bottom Right Badge: 100% University Placement */}
              <motion.div
                initial={{ y: 10, opacity: 0 }}
                animate={{ y: 0, opacity: 1 }}
                transition={{ delay: 0.6, duration: 0.6 }}
                className="absolute -bottom-6 -right-6 bg-navy text-white px-5 py-4 rounded-2xl shadow-2xl border border-white/10 max-w-[240px] z-30"
              >
                <div className="flex items-center gap-2 text-[10px] uppercase tracking-wider text-cyan-light font-bold mb-1">
                  <Award className="w-3.5 h-3.5 text-cyan" />
                  <span>Scholar Placements</span>
                </div>
                <div className="font-display text-base font-bold text-white mb-1">100% University Offer Rate</div>
                <p className="text-[11px] text-white/70 leading-snug">Stanford, Oxford, Toronto, UCL & Melbourne cohorts.</p>
              </motion.div>
            </div>
          </motion.div>
        </div>
      </div>
    </section>
  );
};
