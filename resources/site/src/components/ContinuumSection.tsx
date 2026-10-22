import React, { useState } from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import { CheckCircle2, ArrowRight, Layers, Sparkles, BookOpen, GraduationCap } from 'lucide-react';
import { CONTINUUM_STAGES } from '../data/schoolData';

interface ContinuumSectionProps {
  onOpenModal: () => void;
}

export const ContinuumSection: React.FC<ContinuumSectionProps> = ({ onOpenModal }) => {
  const [activeIdx, setActiveIdx] = useState(0);
  const currentStage = CONTINUUM_STAGES[activeIdx];

  return (
    <section className="py-24 bg-white relative overflow-hidden" id="continuum">
      <div className="max-w-7xl mx-auto px-6">
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-14">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/10 border border-cyan/20 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-4">
            <Layers className="w-3.5 h-3.5" />
            <span>( The Academic Continuum )</span>
          </div>
          <h2 className="font-display text-3xl sm:text-5xl font-extrabold text-navy tracking-tight leading-[1.15] mb-6">
            A Continuous Learning Pathway From Early Years to Global Flight<span className="text-cyan animate-pulse">|</span>
          </h2>
          <p className="text-slate-600 text-base sm:text-lg">
            Each educational stage is intentionally sequenced to deepen curiosity, analytical rigour, and self-efficacy as students grow.
          </p>
        </div>

        {/* Stage Selector Tabs */}
        <div className="flex items-center justify-center flex-wrap gap-2.5 mb-12" role="tablist">
          {CONTINUUM_STAGES.map((stage, idx) => (
            <button
              key={stage.id}
              role="tab"
              aria-selected={activeIdx === idx}
              onClick={() => setActiveIdx(idx)}
              className={`flex items-center gap-2.5 px-5 py-3 rounded-full font-display text-xs sm:text-sm font-bold transition-all duration-300 ${
                activeIdx === idx
                  ? 'bg-navy text-white shadow-xl shadow-navy/20 scale-105 ring-2 ring-cyan/40'
                  : 'bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-navy'
              }`}
            >
              <span
                className={`w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-extrabold ${
                  activeIdx === idx ? 'bg-cyan text-white' : 'bg-slate-300 text-slate-700'
                }`}
              >
                0{stage.stageNumber}
              </span>
              <span>{stage.name}</span>
            </button>
          ))}
        </div>

        {/* Dynamic Stage Display Card */}
        <div className="bg-[#FAF7F2] rounded-3xl border border-navy/10 shadow-2xl overflow-hidden min-h-[580px]">
          <AnimatePresence mode="wait">
            <motion.div
              key={currentStage.id}
              className="grid grid-cols-1 lg:grid-cols-12 min-h-[580px]"
              initial={{ opacity: 0, x: 20 }}
              animate={{ opacity: 1, x: 0 }}
              exit={{ opacity: 0, x: -20 }}
              transition={{ duration: 0.35, ease: [0.16, 1, 0.3, 1] }}
            >
              {/* Left Column: Stage Details */}
              <div className="lg:col-span-7 p-8 sm:p-12 lg:p-16 flex flex-col justify-between">
                <div>
                  <div className="flex items-center gap-3 mb-4">
                    <span className="px-3.5 py-1 rounded-full bg-cyan/15 text-cyan font-display text-xs font-extrabold uppercase tracking-wider">
                      Stage 0{currentStage.stageNumber}
                    </span>
                    <span className="text-xs font-bold text-slate-400">
                      Age Bracket: {currentStage.ageRange}
                    </span>
                  </div>

                  <h3 className="font-display text-3xl sm:text-4xl font-extrabold text-navy mb-2">
                    {currentStage.name}
                  </h3>
                  <div className="text-cyan font-display font-bold text-sm mb-6 flex items-center gap-2">
                    <BookOpen className="w-4 h-4 text-cyan" />
                    <span>{currentStage.subtitle}</span>
                  </div>

                  <p className="text-slate-600 text-base leading-relaxed mb-8">
                    {currentStage.description}
                  </p>

                  {/* 4 Pillars */}
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                    {currentStage.pillars.map((pillar) => (
                      <div key={pillar} className="flex items-start gap-2.5 p-3 rounded-xl bg-white/70 border border-navy/5 shadow-sm">
                        <CheckCircle2 className="w-4 h-4 text-cyan flex-shrink-0 mt-0.5" />
                        <span className="text-xs sm:text-sm font-semibold text-navy">
                          {pillar}
                        </span>
                      </div>
                    ))}
                  </div>
                </div>

                <div className="pt-6 border-t border-navy/10 flex flex-wrap items-center justify-between gap-4">
                  <button
                    onClick={onOpenModal}
                    className="inline-flex items-center gap-2 px-7 py-3.5 bg-navy text-white rounded-full font-display font-bold text-xs uppercase tracking-wider hover:bg-navy-dark shadow-md hover:shadow-cyan-glow transition-all"
                  >
                    <span>Inquire for {currentStage.name}</span>
                    <ArrowRight className="w-4 h-4 text-cyan-light" />
                  </button>

                  <div className="text-right">
                    <div className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pedagogical Framework</div>
                    <div className="text-xs font-bold text-navy flex items-center gap-1.5 justify-end">
                      <GraduationCap className="w-3.5 h-3.5 text-cyan" />
                      <span>Cambridge Continuum</span>
                    </div>
                  </div>
                </div>
              </div>

              {/* Right Column: Visual Stage Image */}
              <div className="lg:col-span-5 relative bg-navy-dark min-h-[360px] lg:min-h-full">
                <img
                  src={currentStage.image}
                  alt={currentStage.name}
                  className="w-full h-full object-cover"
                />
                <div className="absolute inset-0 bg-gradient-to-t from-navy-dark/95 via-navy-dark/30 to-transparent" />

                {/* Floating Accreditation Card */}
                <div className="absolute bottom-6 left-6 right-6 bg-navy/90 backdrop-blur-md p-5 rounded-2xl border border-white/10 text-white shadow-2xl">
                  <div className="flex items-center gap-2 text-cyan-light text-xs font-bold uppercase tracking-wider mb-1.5">
                    <Sparkles className="w-3.5 h-3.5 text-cyan" />
                    <span>{currentStage.accreditationBadge}</span>
                  </div>
                  <p className="text-xs text-white/80 leading-relaxed">
                    {currentStage.accreditationDetail}
                  </p>
                </div>
              </div>
            </motion.div>
          </AnimatePresence>
        </div>
      </div>
    </section>
  );
};
