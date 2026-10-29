import { getContent, siteAsset, safeUrl, route, siteConfig } from '../content';
import React, { lazy, Suspense, useRef, useState, useEffect } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { Compass, Calendar, ArrowRight, Sparkles, CheckCircle2, ChevronDown, GraduationCap, Cpu, Globe2, Award } from 'lucide-react';
import { CONTINUUM_STAGES } from '../data/schoolData';
import { GooeyBubbleButton } from './GooeyBubbleButton';

const ThreeCanvas = lazy(() => import('./ThreeCanvas').then(module => ({ default: module.ThreeCanvas })));

const HeroGlobeFallback = () => (
  <div className="absolute inset-0 flex items-center justify-center" aria-hidden="true">
    <svg viewBox="0 0 800 800" className="w-[82vw] max-w-[820px] h-auto max-h-[88vh] text-cyan/70" fill="none">
      <circle cx="400" cy="400" r="260" fill="currentColor" fillOpacity=".06" stroke="currentColor" strokeOpacity=".5" strokeWidth="2" />
      <ellipse cx="400" cy="400" rx="260" ry="94" stroke="currentColor" strokeOpacity=".5" strokeWidth="2" />
      <ellipse cx="400" cy="400" rx="260" ry="175" stroke="currentColor" strokeOpacity=".3" />
      <ellipse cx="400" cy="400" rx="112" ry="260" stroke="currentColor" strokeOpacity=".45" />
      <ellipse cx="400" cy="400" rx="205" ry="260" stroke="currentColor" strokeOpacity=".25" />
      <ellipse cx="400" cy="400" rx="350" ry="130" transform="rotate(-20 400 400)" stroke="currentColor" strokeWidth="6" />
      <ellipse cx="400" cy="400" rx="355" ry="126" transform="rotate(28 400 400)" stroke="currentColor" strokeOpacity=".7" strokeWidth="3" />
    </svg>
  </div>
);

interface HeroScrollPinnedProps {
  onOpenModal: () => void;
}

export const HeroScrollPinned: React.FC<HeroScrollPinnedProps> = ({ onOpenModal }) => {
  const containerRef = useRef<HTMLDivElement>(null);
  const scrollProgressRef = useRef(0);
  const activeStageIdxRef = useRef(-1);
  const [activeStageIdx, setActiveStageIdx] = useState(-1); // -1 = Hero Intro, 0..4 = Stages
  const [canvasEnabled, setCanvasEnabled] = useState(false);

  useEffect(() => {
    // Let navigation and hero text paint before loading the 3D engine and model.
    const device = navigator as Navigator & { deviceMemory?: number; connection?: { saveData?: boolean } };
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const lowMemory = device.deviceMemory !== undefined && device.deviceMemory <= 2;
    if (reducedMotion || lowMemory || device.connection?.saveData) return;
    const delay = window.matchMedia('(pointer: coarse)').matches ? 700 : 250;
    const timer = window.setTimeout(() => setCanvasEnabled(true), delay);
    return () => window.clearTimeout(timer);
  }, []);

  useEffect(() => {
    let ticking = false;

    const handleScroll = () => {
      if (!ticking) {
        window.requestAnimationFrame(() => {
          if (containerRef.current) {
            const rect = containerRef.current.getBoundingClientRect();
            const containerHeight = Math.max(1, containerRef.current.scrollHeight - window.innerHeight);
            const scrolled = -rect.top;

            let progress = scrolled / containerHeight;
            progress = Math.max(0, Math.min(1, progress));
            scrollProgressRef.current = progress;

            const count = CONTINUUM_STAGES.length;
            const introShare = 1 / (count + 1);
            const nextStage = !count || progress < introShare ? -1 :
              Math.min(count - 1, Math.floor((progress - introShare) / (1 - introShare) * count));

            if (nextStage !== activeStageIdxRef.current) {
              activeStageIdxRef.current = nextStage;
              setActiveStageIdx(nextStage);
            }
          }
          ticking = false;
        });
        ticking = true;
      }
    };

    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const scrollToStage = (stageIdx: number) => {
    if (!containerRef.current) return;
    const containerTop = containerRef.current.offsetTop;
    const containerHeight = Math.max(1, containerRef.current.scrollHeight - window.innerHeight);

    const count = CONTINUUM_STAGES.length;
    const targetRatio = stageIdx < 0 || !count ? 0.02 : (Math.min(stageIdx, count - 1) + 1.5) / (count + 1);

    const targetY = containerTop + containerHeight * targetRatio;
    if (window.__lenis) {
      window.__lenis.scrollTo(targetY, { duration: 0.85 });
    } else {
      window.scrollTo({ top: targetY, behavior: 'smooth' });
    }
  };

  const currentStage = activeStageIdx >= 0 ? CONTINUUM_STAGES[activeStageIdx] : null;

  return (
    <div
      ref={containerRef}
      style={{ height: `${Math.max(110, (CONTINUUM_STAGES.length + 1) * 80)}vh` }}
      className="relative bg-gradient-to-b from-[#F0F6FF] via-[#F8FAFC] to-white"
      id="continuum"
    >
      {/* Sticky Fullscreen Viewport */}
      <div className="sticky top-0 h-[100dvh] w-full overflow-hidden flex flex-col justify-between py-2 sm:py-6 px-3 sm:px-10 lg:px-16">
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
            src={siteAsset(getContent<string>("sections.hero.media.cream_top_cloud_webp", "/assets/images/cream-top-cloud.webp"))}
            alt=""
            className="absolute top-0 -left-10 w-[450px] opacity-40 animate-pulse pointer-events-none"
          />
          <img
            src={siteAsset(getContent<string>("sections.hero.media.cream_bottom_cloud_webp", "/assets/images/cream-bottom-cloud.webp"))}
            alt=""
            className="absolute bottom-10 right-0 w-[500px] opacity-40 animate-pulse pointer-events-none"
          />
        </div>

        {/* Full-Header 3D Interactive Canvas */}
        <div className="absolute inset-0 pointer-events-none z-10 overflow-hidden w-full h-full">
          {canvasEnabled ? <Suspense fallback={<HeroGlobeFallback />}><ThreeCanvas scrollProgressRef={scrollProgressRef} /></Suspense> : <HeroGlobeFallback />}

          {/* Ambient blurred glow circles */}
          <div className="site-ambient-glow absolute top-1/4 left-10 w-96 h-96 rounded-full bg-cyan/15 blur-3xl pointer-events-none" />
          <div className="site-ambient-glow absolute bottom-10 right-10 w-96 h-96 rounded-full bg-cyan-light/15 blur-3xl pointer-events-none" />
        </div>



        {/* Right Sleek Minimalist Stage Dots Indicator (Replacing the removed clunky bottom bar) */}
        <div className={`hidden lg:flex absolute right-8 top-1/2 -translate-y-1/2 z-30 flex-col items-end gap-3 pointer-events-auto transition-opacity duration-300 ${
          activeStageIdx === -1 ? 'opacity-0 pointer-events-none' : 'opacity-100'
        }`}>
          {/* Overview Dot */}
          <button
            onClick={() => scrollToStage(-1)}
            className="group flex items-center gap-2 text-right focus:outline-none"
            title={getContent<string>("sections.hero.copy.overview", "Overview")}
          >
            <span className={`text-[10px] font-bold uppercase tracking-wider transition-opacity duration-300 opacity-0 group-hover:opacity-100 ${
              activeStageIdx === -1 ? 'text-navy' : 'text-slate-400'
            }`}>{getContent<string>("sections.hero.copy.intro", "Intro")}</span>
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
              }`}>0{i + 1} ' + (st.id === 'uk-pathway' ? 'UK' : st.id === 'usa-pathway' ? 'USA' : st.id === 'aus-canada-pathway' ? 'AUS/CA' : st.id === 'europe-germany-pathway' ? 'EUROPE' : 'VISA & IELTS') + '
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
                className="relative w-full h-[78dvh] sm:h-[76vh] flex flex-col justify-between"
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
                    <div className="inline-flex items-center gap-2 sm:gap-3 px-4 sm:px-8 py-1.5 sm:py-3 rounded-full bg-white/70 backdrop-blur-xl border border-white/80 shadow-[0_15px_35px_rgba(0,153,255,0.15)] hover:shadow-[0_20px_45px_rgba(0,153,255,0.25)] hover:scale-105 transition-all duration-300 group cursor-default">
                      <Sparkles className="w-4 h-4 sm:w-6 sm:h-6 text-cyan animate-pulse group-hover:rotate-45 transition-transform" />
                      <span className="font-display font-extrabold text-lg sm:text-3xl lg:text-4xl text-[#07172C] tracking-tight">{getContent<string>("sections.hero.copy.welcomeTo", "Welcome to ")}<span className="bg-gradient-to-r from-navy via-cyan to-blue-600 bg-clip-text text-transparent group-hover:from-cyan group-hover:to-cyan-light transition-all">{getContent<string>("sections.hero.copy.genz", "GenZ")}</span>
                      </span>
                      <span className="w-2 sm:w-2.5 h-2 sm:h-2.5 rounded-full bg-cyan animate-ping" />
                    </div>
                  </motion.div>
                </div>

                {/* 2. THE 4 CORNERS: Ultra-Attractive Transparent Gooey Liquid Bubble Buttons with Animated Flags */}
                {/* CORNER 1: TOP-LEFT â€” USA */}
                <div className="absolute top-12 sm:top-2 left-0 sm:left-2 pointer-events-auto">
                  <GooeyBubbleButton
                    country={getContent<string>("sections.hero.copy.usa", "USA")}
                    subtitle={getContent<string>("sections.hero.copy.ivyLeagueTopUnis", "Ivy League & Top Unis")}
                    icon={Globe2}
                    align="left"
                    flagPlacement="bottom"
                    onClick={onOpenModal}
                  />
                </div>

                {/* CORNER 2: TOP-RIGHT â€” UK */}
                <div className="absolute top-12 sm:top-2 right-0 sm:right-2 pointer-events-auto">
                  <GooeyBubbleButton
                    country={getContent<string>("sections.hero.copy.uk", "UK")}
                    subtitle={getContent<string>("sections.hero.copy.cambridgeRussellGroup", "Cambridge & Russell Group")}
                    icon={GraduationCap}
                    align="right"
                    flagPlacement="bottom"
                    onClick={onOpenModal}
                  />
                </div>

                {/* CORNER 3: BOTTOM-LEFT â€” MALAYSIA */}
                <div className="absolute bottom-12 sm:bottom-4 left-0 sm:left-2 pointer-events-auto">
                  <GooeyBubbleButton
                    country={getContent<string>("sections.hero.copy.malaysia", "MALAYSIA")}
                    subtitle={getContent<string>("sections.hero.copy.asiaPacificEducationHub", "Asia-Pacific Education Hub")}
                    icon={Compass}
                    align="left"
                    flagPlacement="top"
                    onClick={onOpenModal}
                  />
                </div>

                {/* CORNER 4: BOTTOM-RIGHT â€” TURKEY */}
                <div className="absolute bottom-12 sm:bottom-4 right-0 sm:right-2 pointer-events-auto">
                  <GooeyBubbleButton
                    country={getContent<string>("sections.hero.copy.turkey", "TURKEY")}
                    subtitle={getContent<string>("sections.hero.copy.eurasianCampusesAwards", "Eurasian Campuses & Awards")}
                    icon={Award}
                    align="right"
                    flagPlacement="top"
                    onClick={onOpenModal}
                  />
                </div>
              </motion.div>
            ) : currentStage ? (
              /* Phases 1..5: Educational Continuum Stage Card */
              <motion.div
                key={currentStage.id}
                className="grid grid-cols-1 lg:grid-cols-12 gap-5 lg:gap-8 items-center bg-white/95 backdrop-blur-2xl rounded-2xl sm:rounded-3xl p-4 sm:p-8 lg:p-12 border border-navy/10 shadow-2xl max-h-[78dvh] overflow-y-auto lg:overflow-visible"
                initial={{ opacity: 0, y: 30 }}
                animate={{ opacity: 1, y: 0 }}
                exit={{ opacity: 0, y: -30 }}
                transition={{ duration: 0.45, ease: [0.16, 1, 0.3, 1] }}
              >
                {/* Left: Stage Information */}
                <div className="lg:col-span-7 flex flex-col justify-between">
                  <div>
                    <div className="flex items-center gap-3 mb-2 sm:mb-3">
                      <span className="px-3 py-0.5 sm:px-3.5 sm:py-1 rounded-full bg-cyan text-white font-display text-[10px] sm:text-xs font-black uppercase tracking-wider shadow-sm">Destination {currentStage.stageNumber}
                      </span>
                      <span className="text-[11px] sm:text-xs font-bold text-slate-500">
                        {currentStage.ageRange}
                      </span>
                    </div>

                    <h2 className="font-display text-xl sm:text-3xl lg:text-5xl font-extrabold text-navy mb-1 sm:mb-2 tracking-tight">
                      {currentStage.name}
                    </h2>
                    <div className="text-cyan font-display font-bold text-xs sm:text-base mb-2 sm:mb-4">
                      {currentStage.subtitle}
                    </div>

                    <p className="text-slate-600 text-xs sm:text-base leading-relaxed mb-3 sm:mb-6 line-clamp-3 sm:line-clamp-none">
                      {currentStage.description}
                    </p>

                    {/* 4 Pillars */}
                    <div className="grid grid-cols-2 gap-2 sm:gap-3 mb-4 sm:mb-8">
                      {currentStage.pillars.map((pillar) => (
                        <div key={pillar} className="flex items-start gap-1.5 sm:gap-2.5 p-1.5 sm:p-2 rounded-xl bg-slate-50/80 border border-slate-100">
                          <CheckCircle2 className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-cyan flex-shrink-0 mt-0.5" />
                          <span className="text-[10px] sm:text-xs font-bold text-navy leading-tight">
                            {pillar}
                          </span>
                        </div>
                      ))}
                    </div>
                  </div>

                  <div className="flex flex-wrap items-center justify-between gap-3 pt-3 sm:pt-6 border-t border-navy/10">
                    <button
                      onClick={onOpenModal}
                      className="w-full sm:w-auto px-5 sm:px-7 py-2.5 sm:py-3.5 bg-navy text-white rounded-full font-display font-bold text-[11px] sm:text-xs uppercase tracking-wider hover:bg-navy-dark shadow-md transition-all flex items-center justify-center gap-2 group"
                    >
                      <span>Apply for {currentStage.name}</span>
                      <ArrowRight className="w-4 h-4 text-cyan group-hover:translate-x-1 transition-transform" />
                    </button>
                    <span className="text-xs text-slate-400 font-semibold hidden sm:inline flex items-center gap-1.5">
                      <Sparkles className="w-3.5 h-3.5 text-cyan" />
                      <span>GenZ Global Education</span>
                    </span>
                  </div>
                </div>

                {/* Right: High-Resolution Photographic Showcase */}
                <div className="lg:col-span-5 relative rounded-xl sm:rounded-2xl overflow-hidden aspect-[16/9] sm:aspect-[4/3] lg:aspect-square bg-navy-dark shadow-xl group max-h-48 sm:max-h-64 lg:max-h-none">
                  <img
                    src={siteAsset(currentStage.image)}
                    alt={currentStage.name}
                    className="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700"
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-navy-dark/90 via-navy-dark/10 to-transparent" />

                  {/* Floating Badge */}
                  <div className="absolute bottom-2.5 sm:bottom-5 left-2.5 sm:left-5 right-2.5 sm:right-5 bg-navy/90 backdrop-blur-md p-2.5 sm:p-4 rounded-lg sm:rounded-xl border border-white/10 text-white shadow-lg">
                    <div className="flex items-center gap-1.5 sm:gap-2 text-cyan-light text-[10px] sm:text-xs font-bold uppercase tracking-wider mb-0.5 sm:mb-1">
                      <Sparkles className="w-3 h-3 sm:w-3.5 sm:h-3.5 text-cyan" />
                      <span>{currentStage.accreditationBadge}</span>
                    </div>
                    <p className="text-[10px] sm:text-xs text-white/80 leading-snug line-clamp-2 sm:line-clamp-none">
                      {currentStage.accreditationDetail}
                    </p>
                  </div>
                </div>
              </motion.div>
            ) : null}
          </AnimatePresence>
        </div>

        {/* Compact Bottom Action Dock - Exactly at the bottom position requested by user */}
        <div className="relative z-30 flex flex-col items-center justify-center pb-1.5 sm:pb-3 pointer-events-auto">
          {activeStageIdx === -1 && (
            <motion.div
              initial={{ opacity: 0, y: 15 }}
              animate={{ opacity: 1, y: 0 }}
              transition={{ duration: 0.5, delay: 0.2 }}
              className="flex items-center gap-1.5 sm:gap-3 backdrop-blur-xl bg-white/90 p-1 sm:p-1.5 rounded-full border border-white/90 shadow-[0_8px_25px_rgba(11,37,69,0.12)] max-w-full"
            >
              <button
                onClick={onOpenModal}
                className="inline-flex items-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-1.5 sm:py-2 bg-navy text-white rounded-full font-display font-bold text-[10px] sm:text-xs tracking-wide hover:bg-navy-dark shadow-md hover:shadow-cyan-glow transition-all transform hover:-translate-y-0.5 group whitespace-nowrap"
              >
                <Calendar className="w-3 h-3 sm:w-3.5 sm:h-3.5 text-cyan-light" />
                <span>Book Free Consultation</span>
                <span className="w-1.5 h-1.5 rounded-full bg-cyan group-hover:scale-150 transition-transform" />
              </button>

              <button
                onClick={() => scrollToStage(0)}
                disabled={!CONTINUUM_STAGES.length}
                className="inline-flex items-center gap-1 sm:gap-1.5 px-2.5 sm:px-4 py-1.5 sm:py-2 bg-white/95 text-navy hover:text-cyan border border-navy/15 rounded-full font-display font-bold text-[10px] sm:text-xs hover:border-cyan transition-all shadow-sm group whitespace-nowrap"
              >
                <span>Explore Destinations</span>
                <ChevronDown className="w-3 h-3 sm:w-3.5 sm:h-3.5 text-cyan group-hover:translate-y-0.5 transition-transform" />
              </button>
            </motion.div>
          )}
        </div>
      </div>
    </div>
  );
};
