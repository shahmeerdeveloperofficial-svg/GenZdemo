import React, { useRef, useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Compass, Calendar, ArrowRight, Sparkles, CheckCircle2, ChevronDown, GraduationCap, Cpu, Globe2, Award } from 'lucide-react';
import { ThreeCanvas } from './ThreeCanvas';
import { CONTINUUM_STAGES } from '../data/schoolData';
import { GooeyBubbleButton } from './GooeyBubbleButton';

interface HeroScrollPinnedProps {
  onOpenModal: () => void;
}

export const HeroScrollPinned: React.FC<HeroScrollPinnedProps> = ({ onOpenModal }) => {
  const containerRef = useRef<HTMLDivElement>(null);
  const [scrollProgress, setScrollProgress] = useState(0);
  const [activeStageIdx, setActiveStageIdx] = useState(-1); // -1 = Hero Intro, 0..4 = Stages

  useEffect(() => {
    const handleScroll = () => {
      if (!containerRef.current) return;
      const rect = containerRef.current.getBoundingClientRect();
      const containerHeight = containerRef.current.scrollHeight - window.innerHeight;
      const scrolled = -rect.top;

      let progress = scrolled / containerHeight;
      progress = Math.max(0, Math.min(1, progress));
      setScrollProgress(progress);

      // Phase distribution:
      // 0.00 - 0.16 : Hero Intro Screen with 3D Globe
      // 0.16 - 0.33 : Stage 01 (Early Years Foundation)
      // 0.33 - 0.50 : Stage 02 (Primary Continuum)
      // 0.50 - 0.67 : Stage 03 (Middle Years Academy)
      // 0.67 - 0.84 : Stage 04 (Senior Cambridge A-Levels)
      // 0.84 - 1.00 : Stage 05 (Beyond GenZ Flight Path)
      if (progress < 0.15) {
        setActiveStageIdx(-1);
      } else if (progress < 0.32) {
        setActiveStageIdx(0);
      } else if (progress < 0.49) {
        setActiveStageIdx(1);
      } else if (progress < 0.66) {
        setActiveStageIdx(2);
      } else if (progress < 0.83) {
        setActiveStageIdx(3);
      } else {
        setActiveStageIdx(4);
      }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const scrollToStage = (stageIdx: number) => {
    if (!containerRef.current) return;
    const containerTop = containerRef.current.offsetTop;
    const containerHeight = containerRef.current.scrollHeight - window.innerHeight;

    let targetRatio = 0.02;
    if (stageIdx === 0) targetRatio = 0.22;
    else if (stageIdx === 1) targetRatio = 0.40;
    else if (stageIdx === 2) targetRatio = 0.58;
    else if (stageIdx === 3) targetRatio = 0.75;
    else if (stageIdx === 4) targetRatio = 0.92;

    const targetY = containerTop + containerHeight * targetRatio;
    window.scrollTo({ top: targetY, behavior: 'smooth' });
  };

  const currentStage = activeStageIdx >= 0 ? CONTINUUM_STAGES[activeStageIdx] : null;

  return (
    <div
      ref={containerRef}
      className="relative h-[480vh] bg-gradient-to-b from-[#F0F6FF] via-[#F8FAFC] to-white"
      id="continuum"
    >
      {/* Sticky Fullscreen Viewport */}
      <div className="sticky top-0 h-screen w-full overflow-hidden flex flex-col justify-between py-6 px-6 sm:px-10 lg:px-16">
        {/* SVG Goo Filter for Organic Liquid Bubbles */}
        <svg
          xmlns="http://www.w3.org/2000/svg"
          version="1.1"
          className="absolute w-0 h-0 pointer-events-none opacity-0 -z-50"
          aria-hidden="true"
        >
          <defs>
            <filter id="goo">
              <feGaussianBlur in="SourceGraphic" stdDeviation="10" result="blur" />
              <feColorMatrix
                in="blur"
                mode="matrix"
                values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9"
                result="goo"
              />
              <feComposite in="SourceGraphic" in2="goo" />
            </filter>
          </defs>
        </svg>

        {/* Authentic Drifting Soft Clouds */}
        <div className="absolute inset-0 pointer-events-none z-0 overflow-hidden" aria-hidden="true">
          <img
            src="/assets/images/cream-top-cloud.webp"
            alt=""
            className="absolute top-0 -left-10 w-[450px] opacity-40 animate-pulse"
          />
          <img
            src="/assets/images/cream-bottom-cloud.webp"
            alt=""
            className="absolute bottom-10 right-0 w-[500px] opacity-40 animate-pulse"
          />
        </div>

        {/* Full-Header 3D Interactive Canvas: Blue lines orbit across entire header without any border clipping! */}
        <div className="absolute inset-0 pointer-events-none z-10 overflow-hidden w-full h-full">
          <ThreeCanvas scrollProgress={scrollProgress} />

          {/* Ambient blurred glow circles */}
          <div className="absolute top-1/4 left-10 w-96 h-96 rounded-full bg-cyan/15 blur-3xl pointer-events-none" />
          <div className="absolute bottom-10 right-10 w-96 h-96 rounded-full bg-cyan-light/15 blur-3xl pointer-events-none" />
        </div>



        {/* Right Sleek Minimalist Stage Dots Indicator (Replacing the removed clunky bottom bar) */}
        <div className={`hidden lg:flex fixed right-8 top-1/2 -translate-y-1/2 z-30 flex-col items-end gap-3 pointer-events-auto transition-opacity duration-300 ${
          activeStageIdx === -1 ? 'opacity-0 pointer-events-none' : 'opacity-100'
        }`}>
          {/* Overview Dot */}
          <button
            onClick={() => scrollToStage(-1)}
            className="group flex items-center gap-2 text-right focus:outline-none"
            title="Overview"
          >
            <span className={`text-[10px] font-bold uppercase tracking-wider transition-opacity duration-300 opacity-0 group-hover:opacity-100 ${
              activeStageIdx === -1 ? 'text-navy' : 'text-slate-400'
            }`}>
              Intro
            </span>
            <span className={`h-2 rounded-full transition-all duration-300 ${
              activeStageIdx === -1
                ? 'w-6 bg-cyan shadow-cyan-glow'
                : 'w-2 bg-slate-300 group-hover:bg-cyan/60'
            }`} />
          </button>

          {/* Stages 01 to 05 Dots */}
          {CONTINUUM_STAGES.map((st, i) => (
            <button
              key={st.id}
              onClick={() => scrollToStage(i)}
              className="group flex items-center gap-2 text-right focus:outline-none"
              title={st.name}
            >
              <span className={`text-[10px] font-bold uppercase tracking-wider transition-opacity duration-300 opacity-0 group-hover:opacity-100 ${
                activeStageIdx === i ? 'text-navy' : 'text-slate-400'
              }`}>
                0{i + 1} {st.name.split(' ')[0]}
              </span>
              <span className={`h-2 rounded-full transition-all duration-300 ${
                activeStageIdx === i
                  ? 'w-7 bg-navy ring-2 ring-cyan shadow-md'
                  : 'w-2 bg-slate-300 group-hover:bg-cyan/60'
              }`} />
            </button>
          ))}
        </div>

        {/* Center Dynamic Stage Content */}
        <div className="relative z-20 max-w-7xl mx-auto w-full my-auto">
          <AnimatePresence mode="wait">
            {activeStageIdx === -1 ? (
              /* Phase 0: Hero Intro - 4 Transparent Corner Buttons + Top "Welcome to GenZ" */
              <motion.div
                key="hero-intro"
                className="relative w-full h-[76vh] flex flex-col justify-between"
                initial={{ opacity: 0 }}
                animate={{ opacity: 1 }}
                exit={{ opacity: 0, scale: 0.95, y: -20 }}
                transition={{ duration: 0.5, ease: [0.16, 1, 0.3, 1] }}
              >
                {/* 1. TOP-CENTER: "Welcome to GenZ" Floating Badge */}
                <div className="w-full flex justify-center pointer-events-auto">
                  <motion.div
                    initial={{ opacity: 0, y: -15 }}
                    animate={{ opacity: 1, y: 0 }}
                    transition={{ duration: 0.6, delay: 0.1 }}
                  >
                    <div className="inline-flex items-center gap-3 px-6 sm:px-8 py-2.5 sm:py-3 rounded-full bg-white/70 backdrop-blur-xl border border-white/80 shadow-[0_15px_35px_rgba(0,153,255,0.15)] hover:shadow-[0_20px_45px_rgba(0,153,255,0.25)] hover:scale-105 transition-all duration-300 group cursor-default">
                      <Sparkles className="w-5 h-5 sm:w-6 sm:h-6 text-cyan animate-pulse group-hover:rotate-45 transition-transform" />
                      <span className="font-display font-extrabold text-2xl sm:text-3xl lg:text-4xl text-[#07172C] tracking-tight">
                        Welcome to <span className="bg-gradient-to-r from-navy via-cyan to-blue-600 bg-clip-text text-transparent group-hover:from-cyan group-hover:to-cyan-light transition-all">GenZ</span>
                      </span>
                      <span className="w-2.5 h-2.5 rounded-full bg-cyan animate-ping" />
                    </div>
                  </motion.div>
                </div>

                {/* 2. THE 4 CORNERS: Ultra-Attractive Transparent Gooey Liquid Bubble Buttons */}
                {/* CORNER 1: TOP-LEFT — USA */}
                <div className="absolute top-2 left-0 sm:left-2 pointer-events-auto">
                  <GooeyBubbleButton
                    country="USA"
                    subtitle="Ivy League & Top Unis"
                    icon={Globe2}
                    align="left"
                    onClick={onOpenModal}
                  />
                </div>

                {/* CORNER 2: TOP-RIGHT — UK */}
                <div className="absolute top-2 right-0 sm:right-2 pointer-events-auto">
                  <GooeyBubbleButton
                    country="UK"
                    subtitle="Cambridge & Russell Group"
                    icon={GraduationCap}
                    align="right"
                    onClick={onOpenModal}
                  />
                </div>

                {/* CORNER 3: BOTTOM-LEFT — MALAYSIA */}
                <div className="absolute bottom-4 left-0 sm:left-2 pointer-events-auto">
                  <GooeyBubbleButton
                    country="MALAYSIA"
                    subtitle="Asia-Pacific Education Hub"
                    icon={Compass}
                    align="left"
                    onClick={onOpenModal}
                  />
                </div>

                {/* CORNER 4: BOTTOM-RIGHT — TURKEY */}
                <div className="absolute bottom-4 right-0 sm:right-2 pointer-events-auto">
                  <GooeyBubbleButton
                    country="TURKEY"
                    subtitle="Eurasian Campuses & Awards"
                    icon={Award}
                    align="right"
                    onClick={onOpenModal}
                  />
                </div>
              </motion.div>
            ) : currentStage ? (
              /* Phases 1..5: Educational Continuum Stage Card */
              <motion.div
                key={currentStage.id}
                className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center bg-white/95 backdrop-blur-2xl rounded-3xl p-6 sm:p-10 lg:p-12 border border-navy/10 shadow-2xl"
                initial={{ opacity: 0, y: 30 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -30 }}
                transition={{ duration: 0.45, ease: [0.16, 1, 0.3, 1] }}
              >
                {/* Left: Stage Information */}
                <div className="lg:col-span-7 flex flex-col justify-between">
                  <div>
                    <div className="flex items-center gap-3 mb-3">
                      <span className="px-3.5 py-1 rounded-full bg-cyan text-white font-display text-xs font-black uppercase tracking-wider shadow-sm">
                        Stage 0{currentStage.stageNumber}
                      </span>
                      <span className="text-xs font-bold text-slate-500">
                        {currentStage.ageRange}
                      </span>
                    </div>

                    <h2 className="font-display text-2xl sm:text-4xl lg:text-5xl font-extrabold text-navy mb-2 tracking-tight">
                      {currentStage.name}
                    </h2>
                    <div className="text-cyan font-display font-bold text-sm sm:text-base mb-4">
                      {currentStage.subtitle}
                    </div>

                    <p className="text-slate-600 text-sm sm:text-base leading-relaxed mb-6">
                      {currentStage.description}
                    </p>

                    {/* 4 Pillars */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-8">
                      {currentStage.pillars.map((pillar) => (
                        <div key={pillar} className="flex items-start gap-2.5 p-2 rounded-xl bg-slate-50/80 border border-slate-100">
                          <CheckCircle2 className="w-4 h-4 text-cyan flex-shrink-0 mt-0.5" />
                          <span className="text-xs font-bold text-navy">
                            {pillar}
                          </span>
                        </div>
                      ))}
                    </div>
                  </div>

                  <div className="flex flex-wrap items-center justify-between gap-4 pt-6 border-t border-navy/10">
                    <button
                      onClick={onOpenModal}
                      className="px-7 py-3.5 bg-navy text-white rounded-full font-display font-bold text-xs uppercase tracking-wider hover:bg-navy-dark shadow-md transition-all flex items-center gap-2 group"
                    >
                      <span>Inquire for {currentStage.name}</span>
                      <ArrowRight className="w-4 h-4 text-cyan group-hover:translate-x-1 transition-transform" />
                    </button>
                    <span className="text-xs text-slate-400 font-semibold hidden sm:inline flex items-center gap-1.5">
                      <Sparkles className="w-3.5 h-3.5 text-cyan" />
                      <span>Cambridge Continuum</span>
                    </span>
                  </div>
                </div>

                {/* Right: High-Resolution Photographic Showcase */}
                <div className="lg:col-span-5 relative rounded-2xl overflow-hidden aspect-[4/3] lg:aspect-[4/4] bg-navy-dark shadow-xl group">
                  <img
                    src={currentStage.image}
                    alt={currentStage.name}
                    className="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-navy-dark/90 via-navy-dark/10 to-transparent" />

                  {/* Floating Badge */}
                  <div className="absolute bottom-5 left-5 right-5 bg-navy/90 backdrop-blur-md p-4 rounded-xl border border-white/10 text-white shadow-lg">
                    <div className="flex items-center gap-2 text-cyan-light text-xs font-bold uppercase tracking-wider mb-1">
                      <Sparkles className="w-3.5 h-3.5 text-cyan" />
                      <span>{currentStage.accreditationBadge}</span>
                    </div>
                    <p className="text-xs text-white/80 leading-snug">
                      {currentStage.accreditationDetail}
                    </p>
                  </div>
                </div>
              </motion.div>
            ) : null}
          </AnimatePresence>
        </div>

        {/* Compact Bottom Action Dock - Exactly at the bottom position requested by user */}
        <div className="relative z-30 flex flex-col items-center justify-center pb-3 pointer-events-auto">
          {activeStageIdx === -1 && (
            <motion.div
              initial={{ opacity: 0, y: 15 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5, delay: 0.2 }}
              className="flex items-center gap-2 sm:gap-3 backdrop-blur-xl bg-white/90 p-1.5 rounded-full border border-white/90 shadow-[0_8px_25px_rgba(11,37,69,0.12)]"
            >
              <button
                onClick={onOpenModal}
                className="inline-flex items-center gap-2 px-4 sm:px-5 py-2 bg-navy text-white rounded-full font-display font-bold text-xs tracking-wide hover:bg-navy-dark shadow-md hover:shadow-cyan-glow transition-all transform hover:-translate-y-0.5 group"
              >
                <Calendar className="w-3.5 h-3.5 text-cyan-light" />
                <span>Book Private Campus Tour</span>
                <span className="w-1.5 h-1.5 rounded-full bg-cyan group-hover:scale-150 transition-transform" />
              </button>

              <button
                onClick={() => scrollToStage(0)}
                className="inline-flex items-center gap-1.5 px-3.5 sm:px-4 py-2 bg-white/95 text-navy hover:text-cyan border border-navy/15 rounded-full font-display font-bold text-xs hover:border-cyan transition-all shadow-sm group"
              >
                <span>Scroll to Discover Stages</span>
                <ChevronDown className="w-3.5 h-3.5 text-cyan group-hover:translate-y-0.5 transition-transform" />
              </button>
            </motion.div>
          )}
        </div>
      </div>
    </div>
  );
};
