import React, { useRef, useState } from 'react';
import { motion, useScroll, useTransform, useSpring } from 'framer-motion';
import { Search, LayoutGrid, CheckCircle2, Sparkles, ArrowRight, ShieldCheck } from 'lucide-react';

interface AdmissionsProps {
  onOpenModal: () => void;
}

export const AdmissionsFlightPlan: React.FC<AdmissionsProps> = ({ onOpenModal }) => {
  const containerRef = useRef<HTMLDivElement>(null);
  const cardRef = useRef<HTMLDivElement>(null);

  const { scrollYProgress } = useScroll({
    target: containerRef,
    offset: ['start end', 'end start'],
  });

  // 3D scroll entrance
  const scrollRotateX = useTransform(scrollYProgress, [0, 0.45], [14, 0]);
  const scrollScale = useTransform(scrollYProgress, [0, 0.45], [0.92, 1]);
  const scrollY = useTransform(scrollYProgress, [0, 0.45], [50, 0]);

  // Interactive 3D cursor tilt state
  const springX = useSpring(0, { stiffness: 120, damping: 18 });
  const springY = useSpring(0, { stiffness: 120, damping: 18 });

  const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
    if (!cardRef.current) return;
    const rect = cardRef.current.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width - 0.5;
    const y = (e.clientY - rect.top) / rect.height - 0.5;
    springX.set(x * 12);
    springY.set(-y * 12);
  };

  const handleMouseLeave = () => {
    springX.set(0);
    springY.set(0);
  };

  return (
    <section
      ref={containerRef}
      className="py-24 sm:py-32 bg-gradient-to-b from-[#FAF7F2] via-[#F3EFE6] to-[#FAF7F2] relative overflow-hidden"
      id="admissions"
    >
      <div className="max-w-6xl mx-auto px-6 relative z-10">
        {/* Section Header */}
        <div className="text-center max-w-2xl mx-auto mb-16">
          <div className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/10 border border-cyan/20 text-cyan text-xs font-display font-bold uppercase tracking-wider mb-4">
            <Sparkles className="w-3.5 h-3.5" />
            <span>( Admissions & CRM Gateway )</span>
          </div>
          <h2 className="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-navy tracking-tight leading-[1.1] mb-6">
            Begin Your GenZ Journey<span className="text-cyan animate-pulse">|</span>
          </h2>
          <p className="text-slate-600 text-base sm:text-lg">
            A structured admissions journey designed to support every applicant with clarity, warmth, and care.
          </p>
        </div>

        {/* Realistic Tablet Held by Hands Container with 3D Tilt & Floating Levitation Animation */}
        <motion.div
          style={{
            rotateX: scrollRotateX,
            scale: scrollScale,
            y: scrollY,
            perspective: 1200,
          }}
          className="relative max-w-5xl mx-auto select-none"
        >
          {/* Continuous Floating Levitation Wrapper */}
          <motion.div
            ref={cardRef}
            onMouseMove={handleMouseMove}
            onMouseLeave={handleMouseLeave}
            animate={{ y: [0, -10, 0] }}
            transition={{ repeat: Infinity, duration: 5, ease: 'easeInOut' }}
            style={{
              rotateY: springX,
              rotateX: springY,
              transformStyle: 'preserve-3d',
            }}
            className="relative w-full max-w-4xl mx-auto cursor-pointer"
          >
            {/* Main iPad Device Frame */}
            <div className="relative z-10 w-full aspect-[16/11] sm:aspect-[16/10.5] rounded-[36px] sm:rounded-[48px] bg-gradient-to-b from-[#0F2A4A] to-[#07172C] p-3 sm:p-5 shadow-2xl border-4 border-slate-600/50">
              {/* Glossy Screen Rim */}
              <div className="relative w-full h-full rounded-[26px] sm:rounded-[38px] bg-gradient-to-b from-[#0B2240] to-[#07172C] overflow-hidden flex flex-col justify-between p-4 sm:p-7 px-8 sm:px-14 md:px-24 shadow-inner border border-white/10 text-white">
                {/* Diagonal Glass Sheen */}
                <div className="absolute -inset-full bg-gradient-to-tr from-transparent via-white/5 to-transparent rotate-12 pointer-events-none animate-pulse" />

                {/* Tablet Top Status Bar */}
                <div className="relative z-10 flex items-center justify-between text-[11px] font-semibold text-white/60 mb-2 sm:mb-4">
                  <span>9:41 AM · Admissions CRM Portal</span>
                  <div className="flex items-center gap-2">
                    <span className="w-2 h-2 rounded-full bg-cyan animate-ping" />
                    <span>GenZ Cloud CRM Active</span>
                  </div>
                </div>

                {/* Tablet Main Headline & Action */}
                <div className="relative z-10 text-center max-w-md mx-auto mb-2 sm:mb-4">
                  <h3 className="font-display text-lg sm:text-2xl md:text-3xl font-extrabold text-white leading-tight mb-2.5">
                    A structured admissions process designed to support every applicant.
                  </h3>
                  <button
                    onClick={onOpenModal}
                    className="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-white text-navy font-display font-bold text-xs uppercase tracking-wider hover:bg-cyan hover:text-white shadow-lg hover:shadow-cyan-glow transition-all transform hover:scale-105 group"
                  >
                    <span>Register Now</span>
                    <ArrowRight className="w-3.5 h-3.5 text-cyan group-hover:text-white group-hover:translate-x-1 transition-all" />
                  </button>
                </div>

                {/* 4 Colored Cards (Nested safely in center viewport away from hands) */}
                <div className="relative z-10 grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3.5 my-auto max-w-xl mx-auto w-full">
                  {/* 1. Inquiry (Yellow Card) */}
                  <motion.div
                    whileHover={{ scale: 1.04, y: -2 }}
                    whileTap={{ scale: 0.98 }}
                    onClick={onOpenModal}
                    className="cursor-pointer bg-[#FACC15] text-[#0B2545] p-3 sm:p-4 rounded-2xl shadow-md hover:shadow-xl hover:shadow-yellow-400/30 border border-yellow-300 transition-all duration-300 group"
                  >
                    <div className="flex items-center justify-between mb-1">
                      <h4 className="font-display font-extrabold text-sm sm:text-base">
                        1. Inquiry
                      </h4>
                      <CheckCircle2 className="w-4 h-4 text-[#0B2545]/70 group-hover:scale-125 transition-transform" />
                    </div>
                    <p className="text-xs font-medium leading-snug text-[#0B2545]/90">
                      Connect with our Admissions Team to explore programmes and admissions guidance.
                    </p>
                  </motion.div>

                  {/* 2. Registration (Orange Card) */}
                  <motion.div
                    whileHover={{ scale: 1.04, y: -2 }}
                    whileTap={{ scale: 0.98 }}
                    onClick={onOpenModal}
                    className="cursor-pointer bg-[#FB923C] text-[#0B2545] p-3 sm:p-4 rounded-2xl shadow-md hover:shadow-xl hover:shadow-orange-400/30 border border-orange-300 transition-all duration-300 group"
                  >
                    <div className="flex items-center justify-between mb-1">
                      <h4 className="font-display font-extrabold text-sm sm:text-base">
                        2. Registration
                      </h4>
                      <CheckCircle2 className="w-4 h-4 text-[#0B2545]/70 group-hover:scale-125 transition-transform" />
                    </div>
                    <p className="text-xs font-medium leading-snug text-[#0B2545]/90">
                      Complete the online pre-registration form and schedule an appointment.
                    </p>
                  </motion.div>

                  {/* 3. Assessment and Interview (Cyan Blue Card) */}
                  <motion.div
                    whileHover={{ scale: 1.04, y: -2 }}
                    whileTap={{ scale: 0.98 }}
                    onClick={onOpenModal}
                    className="cursor-pointer bg-[#38BDF8] text-[#0B2545] p-3 sm:p-4 rounded-2xl shadow-md hover:shadow-xl hover:shadow-sky-400/30 border border-sky-300 transition-all duration-300 group"
                  >
                    <div className="flex items-center justify-between mb-1">
                      <h4 className="font-display font-extrabold text-sm sm:text-base">
                        3. Assessment and Interview
                      </h4>
                      <CheckCircle2 className="w-4 h-4 text-[#0B2545]/70 group-hover:scale-125 transition-transform" />
                    </div>
                    <p className="text-xs font-medium leading-snug text-[#0B2545]/90">
                      Submit the required documents and attend the admissions assessment and interview.
                    </p>
                  </motion.div>

                  {/* 4. Enrolment (Purple Card) */}
                  <motion.div
                    whileHover={{ scale: 1.04, y: -2 }}
                    whileTap={{ scale: 0.98 }}
                    onClick={onOpenModal}
                    className="cursor-pointer bg-[#C084FC] text-[#0B2545] p-3 sm:p-4 rounded-2xl shadow-md hover:shadow-xl hover:shadow-purple-400/30 border border-purple-300 transition-all duration-300 group"
                  >
                    <div className="flex items-center justify-between mb-1">
                      <h4 className="font-display font-extrabold text-sm sm:text-base">
                        4. Enrolment
                      </h4>
                      <CheckCircle2 className="w-4 h-4 text-[#0B2545]/70 group-hover:scale-125 transition-transform" />
                    </div>
                    <p className="text-xs font-medium leading-snug text-[#0B2545]/90">
                      Receive the admission offer and confirm enrolment through fee submission.
                    </p>
                  </motion.div>
                </div>

                {/* Tablet Bottom Dock Bar */}
                <div className="relative z-10 mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between text-xs text-white/70">
                  <div className="flex items-center gap-1.5">
                    <LayoutGrid className="w-3.5 h-3.5 text-cyan" />
                    <span className="font-bold text-white">Admissions CRM</span>
                  </div>
                  <div className="flex items-center gap-4 text-white/50 text-[11px] hidden sm:flex">
                    <span className="hover:text-white cursor-pointer transition-colors" onClick={onOpenModal}>LMS Portal</span>
                    <span className="hover:text-white cursor-pointer transition-colors" onClick={onOpenModal}>Scholarships</span>
                    <span className="hover:text-white cursor-pointer transition-colors" onClick={onOpenModal}>Diagnostic Tests</span>
                  </div>
                  <ShieldCheck className="w-3.5 h-3.5 text-cyan" />
                </div>
              </div>
            </div>

            {/* Hands Layer: Positioned over the outer tablet bezel holding it firmly from left and right! */}
            <div className="absolute -inset-x-8 sm:-inset-x-12 -inset-y-4 pointer-events-none z-30 flex items-center justify-center">
              <img
                src="/assets/images/mac-holdings-hands.webp"
                alt="Hands holding iPad"
                className="w-full h-full object-contain filter drop-shadow-2xl"
              />
            </div>
          </motion.div>
        </motion.div>
      </div>
    </section>
  );
};
