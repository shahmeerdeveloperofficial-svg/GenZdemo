import React, { useState, useRef } from 'react';
import { motion, AnimatePresence, useMotionValue, useSpring, useTransform } from 'framer-motion';
import {
  Quote,
  Sparkles,
  GraduationCap,
  Briefcase,
  ExternalLink,
  ShieldCheck,
  Building2,
  CheckCircle2,
  MapPin,
  ArrowRight,
  X,
  Mail,
  Globe2
} from 'lucide-react';
import { LEADERSHIP_PROFILES } from '../data/leadershipData';
import { LeadershipProfile } from '../types';
import { DenmarkFlag, UKFlag } from './CountryFlags';

interface LeadershipSectionProps {
  onOpenConsultation?: (profile?: LeadershipProfile) => void;
}

export const LeadershipSection: React.FC<LeadershipSectionProps> = ({ onOpenConsultation }) => {
  const [activeDossier, setActiveDossier] = useState<LeadershipProfile | null>(null);

  // Exclude CEO since CEO has his own dedicated section above
  const directors = LEADERSHIP_PROFILES.filter((p) => p.title.toLowerCase().includes('director'));

  return (
    <section
      className="py-24 md:py-36 bg-gradient-to-b from-[#07172C] via-[#0B2545] to-[#040E1B] relative overflow-hidden text-navy border-b border-cyan/20"
      id="leadership"
    >
      {/* Ambient Logo Theme Cyber Backdrops */}
      <div className="absolute inset-0 pointer-events-none overflow-hidden">
        <div className="absolute top-1/4 -left-48 w-[40rem] h-[40rem] bg-cyan/15 rounded-full blur-[180px] animate-pulse" />
        <div className="absolute bottom-1/4 -right-48 w-[40rem] h-[40rem] bg-blue-600/15 rounded-full blur-[180px]" />
        <div className="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[50rem] h-[50rem] bg-cyan/10 rounded-full blur-[220px]" />

        {/* Elegant Curved Circuit Connecting Line between cards */}
        <svg
          className="absolute inset-0 w-full h-full opacity-15 hidden lg:block"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M 300,250 C 700,500 800,800 350,1100"
            fill="none"
            stroke="#0099FF"
            strokeWidth="2"
            strokeDasharray="8 8"
          />
        </svg>
      </div>

      <div className="max-w-6xl mx-auto px-6 relative z-10">
        {/* Section Header */}
        <div className="text-center max-w-3xl mx-auto mb-16 md:mb-24 text-white">
          <motion.div
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            className="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-cyan/15 border border-cyan/40 text-cyan-light text-xs font-display font-bold uppercase tracking-widest shadow-[0_0_20px_rgba(0,153,255,0.35)] mb-4"
          >
            <Sparkles className="w-3.5 h-3.5 text-cyan animate-spin-slow" />
            <span>International Directorate & Advisory Board</span>
          </motion.div>

          <motion.h2
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.1 }}
            className="font-display text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white tracking-tight leading-[1.18] mb-5"
          >
            Executive Leadership & Directorate Messages<span className="heading-cursor-line">|</span>
          </motion.h2>

          <motion.p
            initial={{ opacity: 0, y: 15 }}
            whileInView={{ opacity: 1, y: 0 }}
            viewport={{ once: true }}
            transition={{ delay: 0.2 }}
            className="text-slate-300 text-sm sm:text-base leading-relaxed"
          >
            European doctoral fellows, Erasmus Mundus scholars, and British enterprise leaders providing personal mentorship into the world's most prestigious institutions.
          </motion.p>
        </div>

        {/* ALTERNATING ZIG-ZAG POSTER CARDS (Left -> Right -> Left) */}
        <div className="space-y-16 md:space-y-24">
          {directors.map((profile, idx) => {
            const isRightAligned = idx % 2 === 1;

            return (
              <div
                key={profile.id}
                className={`flex w-full ${isRightAligned ? 'justify-end' : 'justify-start'}`}
              >
                {/* Fixed Max-Width Poster Container for Exact Proportions */}
                <div className="w-full max-w-[540px]">
                  <AinexExactPosterCard
                    profile={profile}
                    index={idx}
                    isRightAligned={isRightAligned}
                    onOpenDossier={() => setActiveDossier(profile)}
                    onOpenConsultation={() => onOpenConsultation?.(profile)}
                  />
                </div>
              </div>
            );
          })}
        </div>

        {/* Section Bottom Banner */}
        <motion.div
          initial={{ opacity: 0, y: 25 }}
          whileInView={{ opacity: 1, y: 0 }}
          viewport={{ once: true }}
          className="mt-20 p-8 rounded-3xl bg-gradient-to-r from-white/[0.06] via-cyan/[0.08] to-white/[0.04] border border-cyan/30 backdrop-blur-xl relative overflow-hidden shadow-[0_20px_50px_rgba(0,0,0,0.5)] text-white"
        >
          <div className="flex flex-col md:flex-row items-center justify-between gap-6 relative z-10">
            <div className="text-center md:text-left">
              <span className="text-cyan text-xs font-display font-bold uppercase tracking-wider block mb-1">
                Direct Mentorship & Scholarship Advisory
              </span>
              <h3 className="font-display text-2xl font-extrabold text-white">
                Consult with our Directorate for Erasmus Mundus & UK Visas
              </h3>
              <p className="text-slate-300 text-sm mt-1">
                Personalized university applications, Scandinavian scholarship guidance, and British enterprise mentorship.
              </p>
            </div>
            <button
              onClick={() => onOpenConsultation?.()}
              className="shrink-0 px-7 py-3.5 rounded-xl bg-gradient-to-r from-cyan via-blue-500 to-cyan bg-[length:200%_auto] hover:bg-right font-display text-xs sm:text-sm font-bold text-white shadow-[0_0_30px_rgba(0,153,255,0.45)] hover:scale-105 active:scale-95 transition-all flex items-center gap-2"
            >
              <Sparkles className="w-4 h-4 text-white" />
              <span>Book Directorate Advisory</span>
              <ArrowRight className="w-4 h-4" />
            </button>
          </div>
        </motion.div>
      </div>

      {/* Interactive Dossier Modal */}
      <AnimatePresence>
        {activeDossier && (
          <ExecutiveDossierModal
            profile={activeDossier}
            onClose={() => setActiveDossier(null)}
            onConsult={() => {
              const current = activeDossier;
              setActiveDossier(null);
              onOpenConsultation?.(current);
            }}
          />
        )}
      </AnimatePresence>
    </section>
  );
};

/* -------------------------------------------------------------------------- */
/* AINEX EXACT REPLICATION POSTER CARD                                        */
/* -------------------------------------------------------------------------- */
interface AinexExactPosterCardProps {
  profile: LeadershipProfile;
  index: number;
  isRightAligned: boolean;
  onOpenDossier: () => void;
  onOpenConsultation: () => void;
}

const AinexExactPosterCard: React.FC<AinexExactPosterCardProps> = ({
  profile,
  index,
  isRightAligned,
  onOpenDossier,
  onOpenConsultation
}) => {
  // 3D Parallax Tilt for Director card
  const cardRef = useRef<HTMLDivElement>(null);
  const mouseX = useMotionValue(0);
  const mouseY = useMotionValue(0);

  const springConfig = { damping: 20, stiffness: 220 };
  const rotateX = useSpring(useTransform(mouseY, [-0.5, 0.5], [6, -6]), springConfig);
  const rotateY = useSpring(useTransform(mouseX, [-0.5, 0.5], [-6, 6]), springConfig);

  const handleMouseMove = (e: React.MouseEvent<HTMLDivElement>) => {
    if (!cardRef.current) return;
    const rect = cardRef.current.getBoundingClientRect();
    const x = (e.clientX - rect.left) / rect.width - 0.5;
    const y = (e.clientY - rect.top) / rect.height - 0.5;
    mouseX.set(x);
    mouseY.set(y);
  };

  const handleMouseLeave = () => {
    mouseX.set(0);
    mouseY.set(0);
  };

  // Custom zoom & focal position for each photo to keep face perfectly centered
  const getImageStyling = () => {
    switch (profile.id) {
      case 'zahid-bajwa':
        return 'object-cover object-[50%_25%] scale-[1.12]';
      case 'muzaffar-hussain':
        return 'object-cover object-[50%_28%] scale-[1.12]';
      case 'suhaib-cheema':
        return 'object-cover object-[52%_15%] scale-[1.8] origin-[52%_15%]';
      default:
        return 'object-cover object-top';
    }
  };

  const renderFlag = () => {
    if (profile.countryCode === 'DK') return <DenmarkFlag className="w-4 h-3 rounded-sm shadow-sm" />;
    if (profile.countryCode === 'GB') return <UKFlag className="w-4 h-3 rounded-sm shadow-sm" />;
    return <span className="text-xs">{profile.countryFlagEmoji}</span>;
  };

  const getWebsiteUrl = () => {
    if (profile.id === 'muzaffar-hussain') return 'www.donerking.dk';
    if (profile.id === 'suhaib-cheema') return 'www.horizon-foods.co.uk';
    return 'www.genzinternational.com';
  };

  const getWebsiteHref = () => {
    if (profile.id === 'muzaffar-hussain') return 'https://www.donerking.dk';
    if (profile.id === 'suhaib-cheema') return 'https://www.horizon-foods.co.uk';
    return '#';
  };

  return (
    <motion.div
      initial={{ opacity: 0, x: isRightAligned ? 35 : -35 }}
      whileInView={{ opacity: 1, x: 0 }}
      viewport={{ once: true, margin: '-50px' }}
      transition={{ duration: 0.6, delay: 0.1 }}
      className="relative group w-full perspective-1000"
    >
      {/* Outer Dynamic Cyan Glow on Hover */}
      <div className="absolute -inset-1.5 bg-gradient-to-r from-cyan via-blue-500 to-cyan rounded-[3rem] opacity-30 group-hover:opacity-80 blur-xl transition-all duration-500 pointer-events-none" />

      {/* Main Crisp White Card Container with 3D Parallax Tilt */}
      <motion.div
        ref={cardRef}
        style={{
          rotateX,
          rotateY,
          transformStyle: 'preserve-3d'
        }}
        onMouseMove={handleMouseMove}
        onMouseLeave={handleMouseLeave}
        className="relative rounded-[2rem] sm:rounded-[2.5rem] bg-white text-navy p-6 sm:p-8 shadow-[0_20px_50px_rgba(0,0,0,0.5)] border-2 border-slate-100 overflow-hidden transition-all duration-500 group-hover:border-cyan group-hover:shadow-[0_30px_90px_rgba(0,153,255,0.4)] group-hover:-translate-y-2 cursor-pointer"
      >
        {/* Hexagon Pattern in Upper Background (Exact Reference Detail) */}
        <div
          className="absolute inset-0 opacity-[0.035] pointer-events-none"
          style={{
            backgroundImage: `url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='28' height='49' viewBox='0 0 28 49'%3E%3Cpath fill-rule='evenodd' stroke='%230B2545' stroke-width='1' fill='none' d='M13.99 9.25l13 7.5v15l-13 7.5L1 31.75v-15l12.99-7.5zM0 0h28v49H0z'/%3E%3C/svg%3E")`,
            backgroundSize: '48px 84px'
          }}
        />

        {/* Diagonal Accent Slashes on Right (Exact Reference Detail) */}
        <div className="absolute -right-12 top-[46%] w-48 h-3.5 bg-gradient-to-r from-cyan to-blue-600 -rotate-25 rounded-full opacity-90 pointer-events-none" />
        <div className="absolute -right-8 top-[52%] w-36 h-2 bg-slate-200 -rotate-25 rounded-full opacity-70 pointer-events-none" />
        <div className="absolute -left-10 bottom-[35%] w-32 h-2.5 bg-slate-100 -rotate-25 rounded-full opacity-60 pointer-events-none" />

        {/* 1. TOP BRAND LOGO (Exact Reference Placement) */}
        <div className="relative z-10 flex flex-col items-center mb-4">
          <div className="flex items-center gap-1.5 mb-3">
            <span className="font-display font-black text-xl sm:text-2xl text-navy tracking-tight">
              GEN<span className="text-cyan">Z</span>
            </span>
            <div className="h-3.5 w-[1.5px] bg-slate-300 mx-1" />
            <span className="text-[10px] uppercase font-display font-extrabold tracking-widest text-slate-500">
              International
            </span>
          </div>

          {/* Dark Navy Statement Banner (Exact Reference Placement & Proportions) */}
          <div className="w-full px-4 py-2.5 rounded-xl bg-[#07172C] text-white text-center shadow-md border border-cyan/20">
            <p className="text-[11px] sm:text-xs font-medium leading-relaxed">
              At <strong className="text-cyan-light font-bold">Gen Z International</strong>, we don’t just teach curriculum — we architect prestigious scholarships, global careers, and international leadership.
            </p>
          </div>
        </div>

        {/* 2. THE LARGE CIRCULAR PORTRAIT WITH NAVY SEMI-CIRCLE ARCH (Exact Proportions) */}
        <div className="relative z-10 flex justify-center my-4 sm:my-5">
          <div className="relative">
            {/* Deep Navy Semi-Circle / Arch Backdrop Behind Circle */}
            <div className="absolute -inset-x-6 sm:-inset-x-8 -top-6 sm:-top-8 bottom-0 bg-[#0B2545] rounded-t-full opacity-95 shadow-lg" />

            {/* Cyan Accent Ring on Arch Edge */}
            <div className="absolute -inset-x-6 sm:-inset-x-8 -top-6 sm:-top-8 bottom-0 border-t-2 border-x-2 border-cyan/40 rounded-t-full pointer-events-none" />

            {/* Circular Portrait Frame (Well-Proportioned: w-52 h-52 sm:w-60 sm:h-60) */}
            <div className="relative w-52 h-52 sm:w-60 sm:h-60 rounded-full border-4 sm:border-[6px] border-white shadow-[0_12px_30px_rgba(11,37,69,0.35)] overflow-hidden bg-navy-dark transition-all duration-500 group-hover:scale-105 group-hover:border-cyan/40 group-hover:ring-4 group-hover:ring-cyan/20">
              <img
                src={profile.image}
                alt={profile.name}
                loading="lazy"
                decoding="async"
                className={`w-full h-full filter brightness-105 contrast-105 transition-transform duration-700 ease-out group-hover:scale-110 ${getImageStyling()}`}
              />

              {/* Bottom Subtle Gradient */}
              <div className="absolute inset-0 bg-gradient-to-t from-navy-dark/30 via-transparent to-transparent pointer-events-none" />
            </div>

            {/* Nationality Pill Badge at Portrait Base */}
            <div className="absolute -bottom-2.5 left-1/2 -translate-x-1/2 inline-flex items-center gap-1.5 px-3.5 py-1 rounded-full bg-white shadow-md border border-slate-200 text-navy text-[11px] font-display font-bold group-hover:border-cyan/50 group-hover:shadow-[0_0_15px_rgba(0,153,255,0.3)] transition-all">
              {renderFlag()}
              <span>{profile.nationality}</span>
            </div>
          </div>
        </div>

        {/* 3. NAME, ROLE & FRAMED QUOTATION (Exact Reference Typography with Hover Effects) */}
        <div className="relative z-10 text-center mt-4 mb-4">
          <h3 className="font-display text-2xl sm:text-3xl font-black text-navy tracking-tight mb-0.5 group-hover:text-cyan transition-colors duration-300">
            {profile.name}
          </h3>

          <p className="text-xs sm:text-sm font-semibold text-slate-500 mb-4">
            {profile.nationality} · {profile.title}
          </p>

          {/* Quotation Area Framed with Big Solid Quotes (Exact Reference Detail with Hover Glow) */}
          <div className="relative px-6 sm:px-8 py-3 rounded-2xl transition-all duration-300 border border-transparent group-hover:border-cyan/30 group-hover:bg-cyan/[0.04] group-hover:shadow-inner">
            {/* Solid Navy Opening Quote on Left */}
            <div className="absolute left-0 top-1 text-[#0B2545] select-none transition-transform duration-300 group-hover:scale-125 group-hover:text-cyan">
              <Quote className="w-7 h-7 sm:w-8 sm:h-8 fill-current" />
            </div>

            {/* Authentic Quote Text */}
            <p className="text-slate-700 text-xs sm:text-[13px] sm:leading-relaxed font-normal italic px-2 transition-colors duration-300 group-hover:text-navy">
              "{profile.quote || profile.experience[0]}"
            </p>

            {/* Solid Navy Closing Quote on Right */}
            <div className="absolute right-0 bottom-1 text-[#0B2545] select-none transition-transform duration-300 group-hover:scale-125 group-hover:text-cyan">
              <Quote className="w-7 h-7 sm:w-8 sm:h-8 fill-current rotate-180" />
            </div>
          </div>

          {/* Academic Degrees & Honors Badges with Hover Pop */}
          <div className="flex flex-wrap items-center justify-center gap-1.5 mt-4">
            {profile.qualifications.slice(0, 2).map((q, qIdx) => (
              <span
                key={qIdx}
                className="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200 text-navy text-[11px] font-semibold transition-all duration-200 hover:scale-105 hover:bg-cyan/15 hover:border-cyan/50 hover:text-navy cursor-default"
              >
                <CheckCircle2 className="w-3 h-3 text-cyan shrink-0" />
                <span>
                  <strong>{q.degree}:</strong> {q.institution}
                </span>
              </span>
            ))}
          </div>

          {/* Discreet Credentials Dossier Link */}
          <div className="mt-3.5">
            <button
              onClick={onOpenDossier}
              className="text-[11px] font-display font-bold text-navy hover:text-cyan transition-colors underline underline-offset-4 inline-flex items-center gap-1.5 group/link"
            >
              <span>View Full Credentials & Academic Dossier</span>
              <ArrowRight className="w-3 h-3 group-hover/link:translate-x-1.5 transition-transform" />
            </button>
          </div>
        </div>

        {/* 4. BOTTOM PILL ACTION BUTTONS (Exact Reference Replication with Hover Glow) */}
        <div className="relative z-10 pt-4 border-t border-slate-100 flex items-center justify-between gap-2 flex-wrap">
          {/* Left Pill: info/advisory email */}
          <button
            onClick={onOpenConsultation}
            className="flex-1 min-w-[140px] px-3.5 py-2.5 rounded-full bg-[#334155] hover:bg-cyan text-white text-[11px] font-display font-semibold shadow-sm hover:shadow-[0_0_20px_rgba(0,153,255,0.4)] transition-all duration-300 flex items-center justify-center gap-1.5 group/btn active:scale-95"
          >
            <Mail className="w-3.5 h-3.5 text-cyan-light group-hover/btn:text-white transition-colors" />
            <span>Advisory Office</span>
          </button>

          {/* Right Pill: Website Link */}
          {profile.ventures && profile.ventures.length > 0 ? (
            <a
              href={getWebsiteHref()}
              target="_blank"
              rel="noopener noreferrer"
              className="flex-1 min-w-[140px] px-3.5 py-2.5 rounded-full bg-[#334155] hover:bg-cyan text-white text-[11px] font-display font-semibold shadow-sm hover:shadow-[0_0_20px_rgba(0,153,255,0.4)] transition-all duration-300 flex items-center justify-center gap-1.5 group/link active:scale-95"
            >
              <Globe2 className="w-3.5 h-3.5 text-cyan-light group-hover/link:text-white transition-colors" />
              <span className="truncate">{getWebsiteUrl()}</span>
              <ExternalLink className="w-2.5 h-2.5 ml-0.5 shrink-0 group-hover/link:rotate-12 transition-transform" />
            </a>
          ) : (
            <div className="flex-1 min-w-[140px] px-3.5 py-2.5 rounded-full bg-[#334155] text-white text-[11px] font-display font-semibold shadow-sm flex items-center justify-center gap-1.5">
              <Globe2 className="w-3.5 h-3.5 text-cyan-light" />
              <span className="truncate">{getWebsiteUrl()}</span>
            </div>
          )}
        </div>
      </motion.div>
    </motion.div>
  );
};


/* -------------------------------------------------------------------------- */
/* FULL EXECUTIVE DOSSIER MODAL                                              */
/* -------------------------------------------------------------------------- */
interface ExecutiveDossierModalProps {
  profile: LeadershipProfile;
  onClose: () => void;
  onConsult: () => void;
}

const ExecutiveDossierModal: React.FC<ExecutiveDossierModalProps> = ({
  profile,
  onClose,
  onConsult
}) => {
  const getImageStyling = () => {
    switch (profile.id) {
      case 'zahid-bajwa':
        return 'object-cover object-[50%_25%] scale-[1.12]';
      case 'muzaffar-hussain':
        return 'object-cover object-[50%_28%] scale-[1.12]';
      case 'suhaib-cheema':
        return 'object-cover object-[52%_15%] scale-[1.8] origin-[52%_15%]';
      default:
        return 'object-cover object-top';
    }
  };

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto">
      <motion.div
        initial={{ opacity: 0 }}
        animate={{ opacity: 1 }}
        exit={{ opacity: 0 }}
        onClick={onClose}
        className="fixed inset-0 bg-[#040E1B]/85 backdrop-blur-xl"
      />

      <motion.div
        initial={{ opacity: 0, scale: 0.9, y: 25 }}
        animate={{ opacity: 1, scale: 1, y: 0 }}
        exit={{ opacity: 0, scale: 0.9, y: 25 }}
        transition={{ type: 'spring', damping: 25, stiffness: 300 }}
        className="relative w-full max-w-4xl bg-gradient-to-b from-[#0F356B] via-[#0B2545] to-[#040E1B] border-2 border-cyan/40 rounded-3xl p-6 sm:p-10 shadow-[0_25px_80px_rgba(0,0,0,0.85)] text-white z-10 max-h-[90vh] overflow-y-auto"
      >
        <button
          onClick={onClose}
          className="absolute top-6 right-6 p-2 rounded-full bg-white/10 hover:bg-white/20 text-white transition-colors z-20"
        >
          <X className="w-5 h-5" />
        </button>

        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
          {/* Left Column: Headshot & Action */}
          <div className="lg:col-span-5 flex flex-col items-center text-center">
            <div className="relative w-44 h-44 sm:w-52 sm:h-52 rounded-full border-4 border-white shadow-[0_0_35px_rgba(0,153,255,0.4)] overflow-hidden bg-navy-dark mb-4">
              <img
                src={profile.image}
                alt={profile.name}
                className={`w-full h-full filter brightness-105 contrast-105 ${getImageStyling()}`}
              />
            </div>

            <div className="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-xs font-display font-semibold mb-2">
              <MapPin className="w-3.5 h-3.5 text-cyan" />
              <span>
                {profile.nationality} {profile.countryFlagEmoji}
              </span>
            </div>

            <p className="text-xs text-slate-300 font-medium">{profile.roleBadge}</p>

            <button
              onClick={onConsult}
              className="mt-6 w-full py-3.5 px-5 rounded-xl bg-gradient-to-r from-cyan via-blue-500 to-cyan bg-[length:200%_auto] hover:bg-right font-display text-xs sm:text-sm font-bold text-white shadow-[0_0_25px_rgba(0,153,255,0.4)] hover:shadow-[0_0_40px_rgba(0,153,255,0.6)] transition-all flex items-center justify-center gap-2"
            >
              <Sparkles className="w-4 h-4" />
              <span>Request Directorate Advisory</span>
            </button>
          </div>

          {/* Right Column: Full Academic & Industry Credentials */}
          <div className="lg:col-span-7 space-y-6">
            <div>
              <div className="inline-block text-xs font-display font-bold text-cyan uppercase tracking-wider mb-1">
                Director Dossier
              </div>
              <h2 className="font-display text-2xl sm:text-3xl font-extrabold text-white">
                {profile.name}
              </h2>
            </div>

            {profile.quote && (
              <div className="p-4 rounded-2xl bg-cyan/15 border-l-4 border-cyan text-slate-200 text-xs sm:text-sm italic leading-relaxed">
                "{profile.quote}"
              </div>
            )}

            <div>
              <h4 className="font-display text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 mb-3">
                <GraduationCap className="w-4 h-4 text-cyan" />
                <span>Academic Degrees & International Fellowships</span>
              </h4>
              <div className="space-y-2.5">
                {profile.qualifications.map((qual, idx) => (
                  <div
                    key={idx}
                    className="p-3.5 rounded-xl bg-white/5 border border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2"
                  >
                    <div>
                      <div className="font-display font-bold text-sm text-white flex items-center gap-2">
                        <span className="px-2 py-0.5 rounded bg-cyan/20 text-cyan text-xs">
                          {qual.degree}
                        </span>
                        <span>{qual.institution}</span>
                      </div>
                      {qual.field && (
                        <div className="text-xs text-slate-300 mt-0.5">{qual.field}</div>
                      )}
                    </div>
                    {qual.country && (
                      <span className="text-xs text-slate-400 font-medium shrink-0">
                        {qual.country}
                      </span>
                    )}
                  </div>
                ))}
              </div>
            </div>

            {profile.ventures && profile.ventures.length > 0 && (
              <div>
                <h4 className="font-display text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 mb-3">
                  <Building2 className="w-4 h-4 text-amber-400" />
                  <span>Commercial Ventures & Enterprise Leadership</span>
                </h4>
                <div className="space-y-2.5">
                  {profile.ventures.map((venture, vIdx) => (
                    <div
                      key={vIdx}
                      className="p-3.5 rounded-xl bg-amber-500/[0.07] border border-amber-500/20"
                    >
                      <div className="flex items-center justify-between gap-2 mb-1">
                        <span className="font-display font-bold text-sm text-amber-300">
                          {venture.name}
                        </span>
                        {venture.url && (
                          <a
                            href={venture.url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1 text-[11px] text-cyan hover:underline"
                          >
                            <span>{venture.url.replace(/^https?:\/\//, '')}</span>
                            <ExternalLink className="w-3 h-3" />
                          </a>
                        )}
                      </div>
                      <div className="text-xs text-slate-300 font-medium mb-1">
                        Role: {venture.role}
                      </div>
                      <p className="text-xs text-slate-400 leading-relaxed">
                        {venture.description}
                      </p>
                    </div>
                  ))}
                </div>
              </div>
            )}

            <div>
              <h4 className="font-display text-sm font-bold uppercase tracking-wider text-slate-300 flex items-center gap-2 mb-3">
                <Briefcase className="w-4 h-4 text-cyan" />
                <span>Executive Mentorship & Track Record</span>
              </h4>
              <ul className="space-y-2 text-xs sm:text-sm text-slate-300">
                {profile.experience.map((exp, eIdx) => (
                  <li key={eIdx} className="flex items-start gap-2">
                    <CheckCircle2 className="w-4 h-4 text-cyan shrink-0 mt-0.5" />
                    <span>{exp}</span>
                  </li>
                ))}
              </ul>
            </div>

            <div>
              <h4 className="font-display text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">
                Specialization Areas
              </h4>
              <div className="flex flex-wrap gap-2">
                {profile.specializations.map((spec, sIdx) => (
                  <span
                    key={sIdx}
                    className="px-2.5 py-1 rounded-lg bg-cyan/10 border border-cyan/30 text-cyan-light text-xs font-medium"
                  >
                    {spec}
                  </span>
                ))}
              </div>
            </div>
          </div>
        </div>
      </motion.div>
    </div>
  );
};
