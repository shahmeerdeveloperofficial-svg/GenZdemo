import { getContent, siteAsset, safeUrl, route, siteConfig } from '../content';
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
            <span>{getContent<string>("sections.admissions.copy.admissionsCrmGateway", "( Admissions & CRM Gateway )")}</span>
          </div>
          <h2 className="font-display text-4xl sm:text-5xl lg:text-6xl font-extrabold text-navy tracking-tight leading-[1.1] mb-6">{getContent<string>("sections.admissions.copy.beginYourGenzJourney", "Begin Your GenZ Journey")}<span className="text-cyan animate-pulse">{getContent<string>("sections.admissions.copy.label", "|")}</span>
          </h2>
          <p className="text-slate-600 text-base sm:text-lg">{getContent<string>("sections.admissions.copy.aStructuredAdmissionsJourneyDesignedTo", "A structured admissions journey designed to support every applicant with clarity, warmth, and care.")}</p>
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
            <div className="relative z-10 w-full aspect-auto sm:aspect-[16/10.5] rounded-[24px] sm:rounded-[48px] bg-gradient-to-b from-[#0F2A4A] to-[#07172C] p-2 sm:p-5 shadow-2xl border-2 sm:border-4 border-slate-600/50">
              {/* Glossy Screen Rim */}
              <div className="relative w-full h-full rounded-[18px] sm:rounded-[38px] bg-gradient-to-b from-[#0B2240] to-[#07172C] overflow-hidden flex flex-col justify-between p-3.5 sm:p-7 px-3.5 sm:px-14 md:px-24 shadow-inner border border-white/10 text-white">
                {/* Diagonal Glass Sheen */}
                <div className="absolute -inset-full bg-gradient-to-tr from-transparent via-white/5 to-transparent rotate-12 pointer-events-none animate-pulse" />

                {/* Tablet Top Status Bar */}
                <div className="relative z-10 flex items-center justify-between text-[11px] font-semibold text-white/60 mb-2 sm:mb-4">
                  <span>{getContent<string>("sections.admissions.copy.941AmAdmissionsCrmPortal", "Admissions CRM Portal")}</span>
                  <div className="flex items-center gap-2">
                    <span className="w-2 h-2 rounded-full bg-cyan animate-ping" />
                    <span>{getContent<string>("sections.admissions.copy.genzCloudCrmActive", "GenZ Student Services")}</span>
                  </div>
                </div>

                {/* Tablet Main Headline & Action */}
                <div className="relative z-10 text-center max-w-md mx-auto mb-2 sm:mb-4">
                  <h3 className="font-display text-lg sm:text-2xl md:text-3xl font-extrabold text-white leading-tight mb-2.5">{getContent<string>("sections.admissions.copy.aStructuredAdmissionsProcessDesignedTo", "A structured admissions process designed to support every applicant.")}</h3>
                  <button
                    onClick={() => window.location.assign(route('registration'))}
                    className="inline-flex items-center gap-2 px-6 py-2 rounded-full bg-white text-navy font-display font-bold text-xs uppercase tracking-wider hover:bg-cyan hover:text-white shadow-lg hover:shadow-cyan-glow transition-all transform hover:scale-105 group"
                  >
                    <span>{getContent<string>("sections.admissions.copy.registerNow", "Register Now")}</span>
                    <ArrowRight className="w-3.5 h-3.5 text-cyan group-hover:text-white group-hover:translate-x-1 transition-all" />
                  </button>
                </div>

                {/* 4 Colored Cards (Nested safely in center viewport away from hands) */}
                <div className="relative z-10 grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-3.5 my-auto max-w-xl mx-auto w-full">
                  {getContent<{title:string;description:string;route:string}[]>('sections.admissions.cards', []).map((card,index) => {
                    const colors = ['bg-[#FACC15] border-yellow-300','bg-[#FB923C] border-orange-300','bg-[#38BDF8] border-sky-300','bg-[#C084FC] border-purple-300'];
                    return <motion.a key={index} href={route(card.route)} whileHover={{scale:1.04,y:-2}} whileTap={{scale:0.98}} className={`text-[#0B2545] p-3 sm:p-4 rounded-2xl shadow-md hover:shadow-xl border transition-all duration-300 group ${colors[index % colors.length]}`}><div className="flex items-center justify-between mb-1"><h4 className="font-display font-extrabold text-sm sm:text-base">{card.title}</h4><CheckCircle2 className="w-4 h-4 shrink-0" /></div><p className="text-xs font-medium leading-snug text-[#0B2545]/90">{card.description}</p></motion.a>;
                  })}
        </div>

                {/* Tablet Bottom Dock Bar */}
                <div className="relative z-10 mt-3 pt-2.5 border-t border-white/10 flex items-center justify-between text-xs text-white/70">
                  <div className="flex items-center gap-1.5">
                    <LayoutGrid className="w-3.5 h-3.5 text-cyan" />
                    <span className="font-bold text-white">{getContent<string>("sections.admissions.copy.admissionsCrm", "Admissions CRM")}</span>
                  </div>
                  <div className="flex items-center gap-4 text-white/50 text-[11px] hidden sm:flex">
                    <span className="hover:text-white cursor-pointer transition-colors" onClick={() => window.location.assign(route('login'))}>{getContent<string>("sections.admissions.copy.lmsPortal", "LMS Portal")}</span>
                    <span className="hover:text-white cursor-pointer transition-colors" onClick={() => window.location.assign(route('scholarships'))}>{getContent<string>("sections.admissions.copy.scholarships", "Scholarships")}</span>
                    <span className="hover:text-white cursor-pointer transition-colors" onClick={() => window.location.assign(route('consultations'))}>{getContent<string>("sections.admissions.copy.consultations", "Consultations")}</span>
                  </div>
                  <ShieldCheck className="w-3.5 h-3.5 text-cyan" />
                </div>
              </div>
            </div>

            {/* Hands Layer: Positioned over the outer tablet bezel holding it firmly from left and right! */}
            <div className="hidden md:flex absolute -inset-x-8 sm:-inset-x-12 -inset-y-4 pointer-events-none z-30 items-center justify-center">
              <img loading="lazy" decoding="async"
                src={siteAsset(getContent<string>("sections.admissions.media.mac_holdings_hands_webp", "/assets/images/mac-holdings-hands.webp"))}
                alt={getContent<string>("sections.admissions.copy.handsHoldingIpad", "Hands holding iPad")}
                className="w-full h-full object-contain filter drop-shadow-2xl"
              />
            </div>
          </motion.div>
        </motion.div>
      </div>
    </section>
  );
};
