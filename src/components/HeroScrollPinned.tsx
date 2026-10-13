import React, { useRef, useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Compass, Calendar, ArrowRight, Sparkles, CheckCircle2, ChevronDown } from 'lucide-react';
import { ThreeCanvas } from './ThreeCanvas';
import { CONTINUUM_STAGES } from '../data/schoolData';

interface HeroScrollPinnedProps {
  onOpenModal: () => void;
}

export const HeroScrollPinned: React.FC<HeroScrollPinnedProps> = ({ onOpenModal }) => {
  const containerRef = useRef<HTMLDivElement>(null);
  const [scrollProgress, setScrollProgress] = useState(0);
  const [activeStageIdx, setActiveStageIdx] = useState(-1); // -1 = Hero Intro, 0..4 = Stages
  const [headlineMouse, setHeadlineMouse] = useState<{ x: number; y: number } | null>(null);

  const handleHeadlineMouseMove = (e: React.MouseEvent<HTMLHeadingElement>) => {
    const rect = e.currentTarget.getBoundingClientRect();
    setHeadlineMouse({
      x: e.clientX - rect.left,
      y: e.clientY - rect.top,
    });
  };

  const handleHeadlineMouseLeave = () => {
    setHeadlineMouse(null);
  };

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
        <div className="hidden lg:flex fixed right-8 top-1/2 -translate-y-1/2 z-30 flex-col items-end gap-3 pointer-events-auto">
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
              /* Phase 0: Hero Intro Screen */
              <motion.div
                key="hero-intro"
                className="relative text-center max-w-4xl mx-auto py-10 sm:py-12 px-6 sm:px-12 rounded-[2.5rem] backdrop-blur-md bg-white/75 border border-white/90 shadow-[0_25px_70px_rgba(11,37,69,0.07)] overflow-hidden"
                initial={{ opacity: 0, scale: 0.95 }}
                animate={{ opacity: 1, scale: 1 }}
                exit={{ opacity: 0, scale: 0.95, y: -20 }}
                transition={{ duration: 0.5, ease: [0.16, 1, 0.3, 1] }}
              >
                {/* Luminous Inner Glass Shimmer */}
                <div className="absolute -top-32 -left-32 w-80 h-80 rounded-full bg-cyan/15 blur-3xl pointer-events-none" />
                <div className="absolute -bottom-32 -right-32 w-80 h-80 rounded-full bg-blue-500/15 blur-3xl pointer-events-none" />

                <div className="relative inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/80 border border-cyan/30 text-navy font-display text-xs font-bold uppercase tracking-wider mb-6 shadow-sm">
                  <Compass className="w-3.5 h-3.5 text-cyan" />
                  <span>A School Where Inquiry Shapes the Future</span>
                </div>

                <h1
                  onMouseMove={handleHeadlineMouseMove}
                  onMouseLeave={handleHeadlineMouseLeave}
                  className="relative font-display text-4xl sm:text-6xl lg:text-7xl font-extrabold text-[#07172C] tracking-tight leading-[1.08] mb-6 drop-shadow-sm select-none cursor-default py-2"
                >
                  {/* Interactive cursor spotlight glow behind letters */}
                  {headlineMouse && (
                    <span
                      className="pointer-events-none absolute w-72 h-72 -translate-x-1/2 -translate-y-1/2 rounded-full bg-cyan/20 blur-3xl transition-opacity duration-300 -z-10"
                      style={{ left: headlineMouse.x, top: headlineMouse.y }}
                    />
                  )}

                  <motion.span whileHover={{ y: -5, scale: 1.05 }} className="inline-block transition-all duration-200 hover:text-cyan hover:drop-shadow-[0_0_20px_rgba(0,153,255,0.7)]">
                    Where
                  </motion.span>{' '}
                  <motion.span whileHover={{ y: -5, scale: 1.05 }} className="inline-block transition-all duration-200 hover:text-cyan hover:drop-shadow-[0_0_20px_rgba(0,153,255,0.7)]">
                    Curiosity
                  </motion.span>{' '}
                  <motion.span whileHover={{ y: -5, scale: 1.05 }} className="inline-block transition-all duration-200 hover:text-cyan hover:drop-shadow-[0_0_20px_rgba(0,153,255,0.7)]">
                    Ignites
                  </motion.span>{' '}
                  <motion.span
                    whileHover={{ y: -5, scale: 1.06 }}
                    className="inline-block bg-gradient-to-r from-navy via-cyan to-cyan-light bg-clip-text text-transparent transition-all duration-300 hover:drop-shadow-[0_0_30px_rgba(0,153,255,0.9)]"
                  >
                    Global Ambition
                  </motion.span>
                  <span className="text-cyan animate-pulse">|</span>
                </h1>

                <p className="relative text-slate-700 font-medium text-base sm:text-xl leading-relaxed max-w-2xl mx-auto mb-10">
                  Authorised British Cambridge education seamlessly articulating from Early Years through A-Levels and into the world's most prestigious universities.
                </p>

                <div className="relative flex flex-wrap items-center justify-center gap-4">
                  <button
                    onClick={onOpenModal}
                    className="inline-flex items-center gap-3 px-8 py-4 bg-navy text-white rounded-full font-display font-bold text-sm tracking-wide hover:bg-navy-dark shadow-xl hover:shadow-cyan-glow transition-all transform hover:-translate-y-0.5 group"
                  >
                    <Calendar className="w-4 h-4 text-cyan-light" />
                    <span>Book Private Campus Tour</span>
                    <span className="w-2 h-2 rounded-full bg-cyan group-hover:scale-150 transition-transform" />
                  </button>

                  <button
                    onClick={() => scrollToStage(0)}
                    className="inline-flex items-center gap-2 px-7 py-4 bg-white/95 text-navy hover:text-cyan border border-navy/10 rounded-full font-display font-bold text-sm hover:border-cyan transition-all shadow-md group"
                  >
                    <span>Scroll to Discover Stages</span>
                    <ArrowRight className="w-4 h-4 text-cyan group-hover:translate-x-1 transition-transform" />
                  </button>
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

        {/* Clean, Non-intrusive Bottom Scroll Cue (Old clunky timeline pill removed as requested) */}
        <div className="relative z-20 flex items-center justify-center pb-2">
          {activeStageIdx === -1 && (
            <motion.div
              animate={{ y: [0, 6, 0] }}
              transition={{ repeat: Infinity, duration: 2, ease: 'easeInOut' }}
              className="flex items-center gap-2 text-xs font-bold text-slate-400 cursor-pointer"
              onClick={() => scrollToStage(0)}
            >
              <span>Scroll to discover educational stages</span>
              <ChevronDown className="w-4 h-4 text-cyan" />
            </motion.div>
          )}
        </div>
      </div>
    </div>
  );
};
